<?php
namespace App\Exports;

use App\Models\Advance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AdvanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $counter = 0;

    /**
     * Ambil semua data Advance
     */
    public function collection()
    {
        // Pastikan eager loading agar relasi tidak N+1
        return Advance::with('type')->where('main_type', 'Advance')->get();
    }

    /**
     * Mapping isi setiap baris
     */
    public function map($row): array
    {
        $this->counter++;

        return [
            $this->counter,
            $row->date_advance->format('Y-m-d H:i:s'),         // Timestamp
            $row->type->name ?? '-',                           // TYPE dari relasi
            $row->date_advance->format('m'),                   // Month (2 digit)
            $row->date_advance->format('y'),                   // Year (2 digit)
            $row->code_advance,
            $row->description,
            $row->nominal_advance,
        ];
    }

    /**
     * Judul kolom di Excel
     */
    public function headings(): array
    {
        return ['No', 'Timestamp', 'TYPE', 'Month', 'Year', 'Unique', 'Description', 'Nominal'];
    }
}
