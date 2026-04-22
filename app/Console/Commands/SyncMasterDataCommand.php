<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vendor;
use App\Models\LedgerAccount;
use App\Models\Type;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomExcelImport implements ToArray, WithCalculatedFormulas
{
    public function array(array $array)
    {
        return $array;
    }
}

class SyncMasterDataCommand extends Command
{
    protected $signature = 'sync:master-data {filePath?}';
    protected $description = 'Sync Vendor and Ledger Account master data from Excel file.';

    public function handle()
    {
        $filePath = $this->argument('filePath') ?? base_path('GL Account & Vendor Code AVI.xlsx');

        if (!url()->isValidUrl($filePath) && !file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return;
        }

        $this->info("Parsing Excel: {$filePath} ...");

        try {
            $data = Excel::toArray(new CustomExcelImport, $filePath);
            $sheet = $data[1]; // Using sheet index 1 mapping
            
            // Find header
            $headerRowIndex = 0;
            foreach ($sheet as $idx => $row) {
                if (in_array('Vendor', $row) || in_array('VENDOR NAME', $row) || in_array('Unit', $row)) {
                    $headerRowIndex = $idx;
                    break;
                }
            }
            
            $headers = $sheet[$headerRowIndex];
            
            // Array Column Indexes Mapping
            $colVendorName = array_search('VENDOR NAME', $headers);
            $colVendorNumber = array_search('VENDOR NUMBER', $headers);
            $colCostCenter = array_search('COST CENTER', $headers);
            $colGlAccount = array_search('GL ACCOUNT', $headers);
            $colDescCoa = array_search('DESC COA', $headers);
            $colTax = array_search('TAX (PPH 23)', $headers);
            $colType = array_search('TYPE', $headers);
            
            $vendorsFromExcel = []; // store raw rows grouped by vendor
            
            // Group by Vendor name
            for ($i = $headerRowIndex + 1; $i < count($sheet); $i++) {
                $row = $sheet[$i];
                if (empty(array_filter($row))) continue; // skip empty rows
                
                $vendorName = trim($row[$colVendorName] ?? '');
                if (!$vendorName || $vendorName === '#N/A' || $vendorName === '-' || $vendorName === 'V') continue;
                
                if (!isset($vendorsFromExcel[$vendorName])) {
                    $vendorsFromExcel[$vendorName] = [
                        'type' => trim($row[$colType] ?? ''),
                        'vendor_number' => trim($row[$colVendorNumber] ?? ''),
                        'cost_center' => trim($row[$colCostCenter] ?? ''),
                        'gl_accounts' => []
                    ];
                }
                
                $glAccount = trim($row[$colGlAccount] ?? '');
                if ($glAccount && $glAccount !== '#N/A' && $glAccount !== '-') {
                    $vendorsFromExcel[$vendorName]['gl_accounts'][] = [
                        'ledger_account' => $glAccount,
                        'desc_coa' => trim($row[$colDescCoa] ?? ''),
                        'tax_percent' => trim($row[$colTax] ?? ''),
                    ];
                }
            }

            $this->info("Found " . count($vendorsFromExcel) . " unique vendors in Excel.");

            DB::beginTransaction();

            // Track changes for report
            $report = [
                'added' => [],
                'updated' => [],
                'deleted' => []
            ];

            // Setup type mapping for new vendors
            $typeMappings = [
                'GA' => Type::where('name', 'GAO')->first()->id ?? null,
                'HR' => Type::where('name', 'HRO')->first()->id ?? null,
            ];

            // 1. Process Excel Vendors
            $processedVendorIds = [];
            foreach ($vendorsFromExcel as $vendorName => $vData) {
                // Find vendor in DB
                // case insensitive search
                $vendor = Vendor::whereRaw('LOWER(name) = ?', [strtolower($vendorName)])->first();
                
                $isNew = false;
                if (!$vendor) {
                    $isNew = true;
                    $vendor = new Vendor();
                    $vendor->name = $vendorName;
                    
                    // Only map TYPE for NEW vendors to HRO / GAO
                    $rawType = strtoupper($vData['type']);
                    $vendor->em_type_id = $typeMappings[$rawType] ?? null;
                    
                    $report['added'][] = $vendorName;
                } else {
                    $report['updated'][] = $vendorName;
                }
                
                $vendor->vendor_number = $vData['vendor_number'] ?: null;
                $vendor->cost_center = $vData['cost_center'] ?: null;
                // do NOT touch em_type_id if updating!
                $vendor->save();
                
                $processedVendorIds[] = $vendor->id;
                
                // Store pivot logic to array, supports duplicate GL Accounts for the same vendor
                $ledgerPivotDataList = [];

                // Process GL Accounts for this vendor
                foreach ($vData['gl_accounts'] as $gl) {
                    $taxVal = $gl['tax_percent'];
                    if (strtolower($taxVal) === 'bebas potongan pph' || $taxVal === '' || $taxVal === '#N/A' || $taxVal === '-') {
                        $taxVal = null;
                    } elseif (is_string($taxVal)) {
                        preg_match('/[0-9]+[.,]?[0-9]*/', $taxVal, $matches);
                        if (!empty($matches)) {
                            $val = (float) str_replace(',', '.', $matches[0]);
                            if (strpos($taxVal, '%') !== false) {
                                $val = $val / 100;
                            }
                            $taxVal = $val;
                        } else {
                            $taxVal = null;
                        }
                    }

                    // A single cell might contain multiple GLs separated by newline.
                    // e.g. "Hotel Abroad: 62117202\nFlight Abroad: 62117201"
                    $rawGlText = trim($gl['ledger_account']);
                    $lines = explode("\n", $rawGlText);

                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (!$line) continue;

                        $code = '';
                        $descInline = '';

                        if (preg_match('/^(.*?)\s*:\s*(\d{5,10})$/', $line, $m)) {
                            $descInline = trim($m[1]);
                            $code = $m[2];
                        } elseif (preg_match('/\b\d{5,10}\b/', $line, $m)) {
                            $code = $m[0];
                            $descInline = trim(str_replace($code, '', $line));
                        } else {
                            // No valid GL numeric code found — skip junk lines (e.g. "SWIFT CODE : BOFAUS6S")
                            continue;
                        }

                        $ledgerCode = substr($code, 0, 20);
                        if (!$ledgerCode) continue;

                        // Upsert LedgerAccount global record
                        $ledger = LedgerAccount::where('ledger_account', $ledgerCode)->first();
                        if (!$ledger) {
                            $ledger = new LedgerAccount();
                            $ledger->ledger_account = $ledgerCode;
                            // Only set desc_coa on first-time creation
                            $ledger->desc_coa = $descInline ? substr($descInline, 0, 255) : null;
                        }
                        // Always update tax_percent from Excel source of truth
                        $ledger->tax_percent = $taxVal;
                        $ledger->save();

                        // Store in list for attaching (allows duplicates)
                        $pivotDesc = $descInline ?: ($gl['desc_coa'] !== '#N/A' ? $gl['desc_coa'] : null);
                        $ledgerPivotDataList[] = [
                            'ledger_account_id' => $ledger->id,
                            'desc_override' => $pivotDesc ? substr($pivotDesc, 0, 255) : null,
                        ];
                    }
                }

                // Sync many-to-many: Detach all first, then attach to allow duplicate GL Codes per vendor
                $vendor->ledgerAccounts()->detach();
                foreach ($ledgerPivotDataList as $pivotItem) {
                    $vendor->ledgerAccounts()->attach($pivotItem['ledger_account_id'], [
                        'desc_override' => $pivotItem['desc_override']
                    ]);
                }
            }

            // 2. Process Deletions (Vendors in DB not in Excel)
            $vendorsToDelete = Vendor::whereNotIn('id', $processedVendorIds)->get();
            foreach ($vendorsToDelete as $delV) {
                // Remove pivot relations before deleting to be clean
                $delV->ledgerAccounts()->detach();
                $report['deleted'][] = $delV->name;
                $delV->delete(); // applies SoftDeletes if set
            }

            DB::commit();
            
            // Build text report
            $reportText  = "--- MASTER DATA SYNC REPORT ---\n";
            $reportText .= "Date: " . now()->format('Y-m-d H:i:s') . "\n\n";
            
            $reportText .= "[+] ADDED VENDORS (" . count($report['added']) . "):\n";
            foreach ($report['added'] as $a) $reportText .= "  - $a\n";
            
            $reportText .= "\n[~] UPDATED VENDORS (" . count($report['updated']) . "):\n";
            foreach ($report['updated'] as $u) $reportText .= "  - $u\n";
            
            $reportText .= "\n[-] DELETED VENDORS (" . count($report['deleted']) . "):\n";
            foreach ($report['deleted'] as $d) $reportText .= "  - $d\n";

            $reportFilePath = storage_path('logs/vendor_sync_report.txt');
            file_put_contents($reportFilePath, $reportText);

            $this->info("Sync complete! Detail report saved to: " . $reportFilePath);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed syncing master data: " . $e->getMessage());
        }
    }
}
