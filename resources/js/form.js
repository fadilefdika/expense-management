import { formatRupiah, parseNumber, renumberRows } from "./utils/helpers";
import { setupCalculations } from "./modules/settlement-calculations";
import { setupVendorLogic } from "./modules/vendor-manager";

document.addEventListener("DOMContentLoaded", () => {
    // === DOM Elements ===
    const elements = {
        nominalAdvance: document.getElementById("nominal_advance_edit"),
        nominalSettlement: document.getElementById("nominal_settlement_edit"),
        difference: document.getElementById("difference_edit"),
        vendorSelect: document.getElementById("vendor_id_edit"),
        typeSettlementSelect: document.getElementById("code_settlement_edit"),
        usageTableBody: document.querySelector("#rincianTableEdit tbody"),
        costCenterTableBody: document.querySelector(
            "#costCenterTableEdit tbody",
        ),
        usageGrandTotal: document.getElementById("grandTotalUsageDetailsEdit"),
        costCenterGrandTotal: document.getElementById(
            "grandTotalCostCenterEdit",
        ),
        hiddenCostCenterTotal: document.getElementById(
            "grand_total_cost_center_edit",
        ),
        expenseTypeSelect: document.getElementById("expense_type_edit"),
        expenseCategorySelect: document.getElementById(
            "expense_category_edit",
        ),
        usdTotal: document.getElementById("usd_total"),
        yenTotal: document.getElementById("yen_total"),
        usdHidden: document.getElementById("usd_settlement"),
        yenHidden: document.getElementById("yen_settlement"),
    };

    // Track the current vendor's cost_center
    let currentVendorCostCenter = "";

    // === 1. Setup Calculations ===
    const calc = setupCalculations({
        usageTableBody: elements.usageTableBody,
        usageGrandTotalInput: elements.usageGrandTotal,
        nominalSettlementInput: elements.nominalSettlement,
        nominalAdvanceInput: elements.nominalAdvance,
        differenceInput: elements.difference,
        costCenterTableBody: elements.costCenterTableBody,
        costCenterGrandTotalInput: elements.costCenterGrandTotal,
        hiddenCostCenterTotalInput: elements.hiddenCostCenterTotal,
        updateCurrencyCallback: () => {
            if (elements.usdTotal && elements.usdHidden)
                elements.usdHidden.value = parseNumber(
                    elements.usdTotal.value,
                );
            if (elements.yenTotal && elements.yenHidden)
                elements.yenHidden.value = parseNumber(
                    elements.yenTotal.value,
                );
        },
    });

    // === Helper: Update GL dropdowns ===
    const updateSelectOptions = (selector, accounts) => {
        document.querySelectorAll(selector).forEach((select) => {
            const prevValue = select.value;
            select.innerHTML =
                '<option value="">-- Select GL Account --</option>';

            accounts?.forEach((acc) => {
                const opt = document.createElement("option");
                opt.value = acc.id;
                opt.textContent = `${acc.ledger_account} - ${acc.desc_coa ?? ""}`;
                opt.dataset.descOverride =
                    acc.desc_override ?? acc.desc_coa ?? "";
                opt.dataset.taxPercent = acc.tax_percent ?? "";
                select.appendChild(opt);
            });
            if (prevValue) select.value = prevValue;
        });
    };

    // === Helper: Auto-fill cost center in all rows ===
    const fillCostCenterInputs = (costCenter) => {
        document
            .querySelectorAll(".cost-center-input")
            .forEach((input) => {
                input.value = costCenter;
            });
    };

    // === 2. Setup Vendor & Ledger Logic ===
    const vendorManager = setupVendorLogic({
        vendorSelect: elements.vendorSelect,
        typeSettlementSelect: elements.typeSettlementSelect,
        onLedgerAccountsUpdated: ({ data }) => {
            const allAccounts = data.ledger_accounts || [];

            // Store vendor cost center for new rows
            currentVendorCostCenter = data.cost_center || "";

            // Populate BOTH tables with ALL vendor GL accounts
            updateSelectOptions(
                ".ledger-account-select-usage-details",
                allAccounts,
            );
            updateSelectOptions(
                ".ledger-account-select-cost-center",
                allAccounts,
            );

            // Auto-fill cost center inputs
            fillCostCenterInputs(currentVendorCostCenter);

            // Recalculate cost center amounts with new tax data
            calc.updateCostCenterGrandTotal();
        },
    });

    // === 3. Dynamic Table Rows ===
    const addRow = (type) => {
        const isUsage = type === "usage";
        const tableBody = isUsage
            ? elements.usageTableBody
            : elements.costCenterTableBody;
        const rowCount = tableBody.rows.length;
        const namePrefix = isUsage ? "usage_items" : "items_costcenter";

        // Get existing GL account options HTML from the appropriate class
        const existingGLSelect = document.querySelector(
            isUsage
                ? ".ledger-account-select-usage-details"
                : ".ledger-account-select-cost-center",
        );
        const glOptionsHtml = existingGLSelect
            ? existingGLSelect.innerHTML
            : '<option value="">-- Select GL Account --</option>';

        const newRow = tableBody.insertRow();

        if (isUsage) {
            newRow.innerHTML = `
                <td>${rowCount + 1}</td>
                <td><select class="form-select form-select-sm ledger-account-select-usage-details" name="${namePrefix}[${rowCount}][ledger_account_id]">${glOptionsHtml}</select></td>
                <td><input type="text" name="${namePrefix}[${rowCount}][description]" class="form-control form-control-sm" required></td>
                <td><input type="number" name="${namePrefix}[${rowCount}][qty]" class="form-control form-control-sm qty" min="1" value="1"></td>
                <td><input type="number" name="${namePrefix}[${rowCount}][nominal]" class="form-control form-control-sm nominal" min="0" value="0"></td>
                <td><input type="text" class="form-control form-control-sm total" name="${namePrefix}[${rowCount}][amount]" value="0" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
            `;
        } else {
            newRow.innerHTML = `
                <td>${rowCount + 1}</td>
                <td><input type="text" name="${namePrefix}[${rowCount}][cost_center]" class="form-control form-control-sm cost-center-input" value="${currentVendorCostCenter}" readonly required></td>
                <td><select class="form-select form-select-sm ledger-account-select-cost-center" name="${namePrefix}[${rowCount}][ledger_account_id]">${glOptionsHtml}</select></td>
                <td><input type="text" name="${namePrefix}[${rowCount}][description]" class="form-control form-control-sm" required></td>
                <td><input type="text" class="form-control form-control-sm total" name="${namePrefix}[${rowCount}][amount]"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
            `;
        }

        // Reset the new GL select to default (no selection)
        const newGLSelect = newRow.querySelector(
            "select[name*='ledger_account_id']",
        );
        if (newGLSelect) newGLSelect.selectedIndex = 0;
    };

    // === Event Listeners ===
    document
        .getElementById("addItemUsageDetails")
        ?.addEventListener("click", () => addRow("usage"));
    document
        .getElementById("addItemCostCenter")
        ?.addEventListener("click", () => addRow("costCenter"));

    // Usage Details: qty × nominal = total
    elements.usageTableBody.addEventListener("input", (e) => {
        if (
            e.target.classList.contains("qty") ||
            e.target.classList.contains("nominal")
        ) {
            const row = e.target.closest("tr");
            const qty = parseFloat(row.querySelector(".qty")?.value) || 0;
            const nominal =
                parseFloat(row.querySelector(".nominal")?.value) || 0;
            row.querySelector(".total").value = formatRupiah(qty * nominal);
            calc.updateGrandTotal();
            calc.updateCostCenterGrandTotal();
        }
    });

    // Usage Details: remove row
    elements.usageTableBody.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-item")) {
            e.target.closest("tr").remove();
            renumberRows(elements.usageTableBody);
            calc.updateGrandTotal();
            calc.updateCostCenterGrandTotal();
        }
    });

    // Cost Center: manual input for non-tax amounts
    elements.costCenterTableBody.addEventListener("input", (e) => {
        if (e.target.classList.contains("total"))
            calc.updateCostCenterGrandTotal();
    });

    // Cost Center: remove row
    elements.costCenterTableBody.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-item")) {
            e.target.closest("tr").remove();
            renumberRows(elements.costCenterTableBody);
            calc.updateCostCenterGrandTotal();
        }
    });

    // GL Account change handler — auto-fill description + handle tax amount
    document.addEventListener("change", (e) => {
        if (
            e.target.classList.contains(
                "ledger-account-select-usage-details",
            ) ||
            e.target.classList.contains("ledger-account-select-cost-center")
        ) {
            const opt = e.target.options[e.target.selectedIndex];
            const desc = opt?.dataset?.descOverride;
            const row = e.target.closest("tr");

            // Auto-fill description if empty
            if (row && desc) {
                const descInput = row.querySelector(
                    'input[name*="[description]"]',
                );
                if (descInput && !descInput.value) descInput.value = desc;
            }

            // Cost Center: handle tax auto-calc & readonly toggle
            if (
                e.target.classList.contains("ledger-account-select-cost-center")
            ) {
                const amountInput = row?.querySelector(".total");
                const taxPercent = opt?.dataset?.taxPercent
                    ? parseFloat(opt.dataset.taxPercent)
                    : 0;

                if (amountInput) {
                    if (taxPercent > 0) {
                        amountInput.readOnly = true;
                    } else {
                        amountInput.readOnly = false;
                        amountInput.value = "";
                    }
                }
                calc.updateCostCenterGrandTotal();
            }
        }
    });

    // Expense Category Filter
    if (elements.expenseCategorySelect && elements.expenseTypeSelect) {
        const allCats = Array.from(elements.expenseCategorySelect.options);
        const tsType = new TomSelect(elements.expenseTypeSelect, {
            placeholder: "-- Pilih Tipe --",
            allowEmptyOption: true,
        });
        const tsCat = new TomSelect(elements.expenseCategorySelect, {
            placeholder: "-- Pilih Kategori --",
            allowEmptyOption: true,
        });
        const initialCat = elements.expenseCategorySelect.value;

        const filterCats = () => {
            const typeId = tsType.getValue();
            tsCat.clear();
            tsCat.clearOptions();
            if (typeId) {
                tsCat.addOptions(
                    allCats
                        .filter((o) => !o.value || o.dataset.type === typeId)
                        .map((o) => ({ value: o.value, text: o.text })),
                );
                tsCat.enable();
                if (initialCat) tsCat.setValue(initialCat);
            } else {
                tsCat.disable();
            }
        };
        tsType.on("change", filterCats);
        filterCats();
    }

    // Currency Formatting for direct inputs
    document
        .querySelectorAll("#nominal_advance_edit, #nominal_settlement_edit")
        .forEach((input) => {
            input.addEventListener("input", (e) => {
                e.target.value = formatRupiah(
                    e.target.value.replace(/\D/g, ""),
                );
            });
        });

    // Initial calculations on page load
    calc.updateGrandTotal();
    calc.updateCostCenterGrandTotal();
});
