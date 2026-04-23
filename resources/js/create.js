import { formatRupiah, parseNumber, renumberRows } from "./utils/helpers";

document.addEventListener("DOMContentLoaded", () => {
    // === DOM & Constants ===
    const NOMINAL_INPUTS = document.querySelectorAll(
        "#nominal_advance, #nominal_settlement",
    );
    const EXPENSE_CATEGORY_SELECT =
        document.getElementById("expense_category");
    const EXPENSE_TYPE_SELECT = document.getElementById("expense_type");
    const MAIN_TYPE_SELECT = document.getElementById("main_type");
    const VENDOR_SELECT = document.getElementById("vendor_id");
    const TYPE_SETTLEMENT_SELECT =
        document.getElementById("type_settlement");
    const USAGE_TABLE_BODY = document.querySelector("#rincianTable tbody");
    const COST_CENTER_TABLE_BODY = document.querySelector(
        "#costCenterTable tbody",
    );
    const USAGE_GRAND_TOTAL_INPUT = document.getElementById(
        "grandTotalUsageDetails",
    );
    const COST_CENTER_GRAND_TOTAL_INPUT = document.getElementById(
        "grandTotalCostCenter",
    );
    const HIDDEN_COST_CENTER_TOTAL_INPUT = document.getElementById(
        "grand_total_cost_center",
    );
    const NOMINAL_SETTLEMENT_INPUT =
        document.getElementById("nominal_settlement");

    // Track current vendor's cost center
    let currentVendorCostCenter = "";

    // === Calculation Functions ===
    const updateGrandTotal = () => {
        let total = 0;
        USAGE_TABLE_BODY.querySelectorAll("tr").forEach((row) => {
            total += parseNumber(row.querySelector(".total")?.value || "0");
        });
        const formattedTotal = formatRupiah(total);
        USAGE_GRAND_TOTAL_INPUT.value = formattedTotal;
    };

    const updateCostCenterGrandTotal = () => {
        let total = 0;
        const totalUsage = parseNumber(USAGE_GRAND_TOTAL_INPUT.value || "0");

        // Auto-calculate tax amounts
        COST_CENTER_TABLE_BODY.querySelectorAll("tr").forEach((row) => {
            const select = row.querySelector(
                ".ledger-account-select-cost-center",
            );
            const amountInput = row.querySelector(".total");
            if (!select || !amountInput) return;

            const selectedOption = select.options[select.selectedIndex];
            const taxPercent = selectedOption?.dataset?.taxPercent
                ? parseFloat(selectedOption.dataset.taxPercent)
                : 0;

            if (taxPercent > 0 && totalUsage > 0) {
                const autoAmount = -Math.floor(totalUsage * taxPercent);
                amountInput.value = formatRupiah(autoAmount);
                amountInput.readOnly = true;
            }
        });

        // Sum all cost center amounts
        COST_CENTER_TABLE_BODY.querySelectorAll("tr").forEach((row) => {
            total += parseNumber(row.querySelector(".total")?.value || "0");
        });

        const grandTotal = total + totalUsage;
        COST_CENTER_GRAND_TOTAL_INPUT.value = formatRupiah(grandTotal);
        HIDDEN_COST_CENTER_TOTAL_INPUT.value = grandTotal;

        // Update Nominal Settlement = Cost Center Grand Total
        if (NOMINAL_SETTLEMENT_INPUT)
            NOMINAL_SETTLEMENT_INPUT.value = formatRupiah(grandTotal);

        // Trigger currency conversion
        if (typeof updateConvertedCurrencyTotals === "function") {
            updateConvertedCurrencyTotals();
        }
    };

    // === Helper: Auto-fill cost center inputs ===
    const fillCostCenterInputs = (costCenter) => {
        document
            .querySelectorAll(".cost-center-input")
            .forEach((input) => {
                input.value = costCenter;
            });
    };

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

    // --- Bagian 1: Nominal Currency Formatting ---
    const setupCurrencyInputs = () => {
        NOMINAL_INPUTS.forEach((input) => {
            input.addEventListener("input", (e) => {
                let value = e.target.value.replace(/\D/g, "");
                e.target.value = formatRupiah(value);
            });
        });
    };

    // --- Bagian 2: Dynamic TomSelect ---
    const setupDynamicSelects = () => {
        if (!EXPENSE_CATEGORY_SELECT || !EXPENSE_TYPE_SELECT) return;

        const allCategoryOptions = Array.from(EXPENSE_CATEGORY_SELECT.options);
        const tomSelectType = new TomSelect(EXPENSE_TYPE_SELECT, {
            placeholder: "-- Pilih Tipe Pengeluaran --",
            allowEmptyOption: true,
            create: false,
            sortField: { field: "text", direction: "asc" },
        });
        const tomSelectCategory = new TomSelect(EXPENSE_CATEGORY_SELECT, {
            placeholder: "-- Pilih Kategori --",
            allowEmptyOption: true,
            create: false,
            sortField: { field: "text", direction: "asc" },
        });

        tomSelectCategory.disable();

        const filterCategories = () => {
            const selectedTypeId = tomSelectType.getValue();
            tomSelectCategory.clear();
            tomSelectCategory.clearOptions();

            if (selectedTypeId) {
                const filteredOptions = allCategoryOptions
                    .filter(
                        (option) =>
                            option.value === "" ||
                            option.dataset.type === selectedTypeId,
                    )
                    .map((option) => ({
                        value: option.value,
                        text: option.text,
                        type: option.dataset.type,
                    }));

                tomSelectCategory.addOptions(filteredOptions);
                tomSelectCategory.enable();
                tomSelectCategory.refreshOptions(false);
                tomSelectCategory.setValue("");
            } else {
                tomSelectCategory.disable();
            }
        };

        tomSelectType.on("change", filterCategories);
        filterCategories();
    };

    // --- Bagian 3: Section Toggling ---
    const setupSectionToggle = () => {
        if (!MAIN_TYPE_SELECT) return;

        const sections = {
            advance: document.getElementById("advance-section"),
            pr_online: document.getElementById("pr-section"),
        };

        const toggleSections = () => {
            const selectedType = MAIN_TYPE_SELECT.value;

            Object.values(sections).forEach((section) => {
                if (section) {
                    section.classList.add("d-none");
                    section
                        .querySelectorAll("input, select, textarea")
                        .forEach((input) => {
                            input.disabled = true;
                        });
                }
            });

            const activeSection = sections[selectedType];
            if (activeSection) {
                activeSection.classList.remove("d-none");
                activeSection
                    .querySelectorAll("input, select, textarea")
                    .forEach((input) => {
                        input.disabled = false;
                    });
            }
        };

        MAIN_TYPE_SELECT.addEventListener("change", toggleSections);
        toggleSections();
    };

    // --- Bagian 4: Dynamic Table Rows & Totals ---
    const setupTableInteractions = () => {
        const updateRowTotal = (row) => {
            const qty = parseFloat(row.querySelector(".qty")?.value) || 0;
            const nominal =
                parseFloat(row.querySelector(".nominal")?.value) || 0;
            row.querySelector(".total").value = formatRupiah(qty * nominal);
        };

        const addRow = (tableBody, type) => {
            const rowCount = tableBody.rows.length;
            const newRow = tableBody.insertRow();
            const isUsageRow = type === "usage";
            const namePrefix = isUsageRow ? "usage_items" : "items_costcenter";

            // Get existing GL options from the appropriate selector
            const existingGLSelect = document.querySelector(
                isUsageRow
                    ? ".ledger-account-select-usage-details"
                    : ".ledger-account-select-cost-center",
            );
            const glOptionsHtml = existingGLSelect
                ? existingGLSelect.innerHTML
                : '<option value="">-- Select GL Account --</option>';

            if (isUsageRow) {
                newRow.innerHTML = `
                    <td>${rowCount + 1}</td>
                    <td><select class="form-select form-select-sm ledger-account-select-usage-details" name="${namePrefix}[${rowCount}][ledger_account_id]">${glOptionsHtml}</select></td>
                    <td><input type="text" name="${namePrefix}[${rowCount}][description]" class="form-control form-control-sm" required></td>
                    <td><input type="number" name="${namePrefix}[${rowCount}][qty]" class="form-control form-control-sm qty" min="1" value="1"></td>
                    <td><input type="number" name="${namePrefix}[${rowCount}][nominal]" class="form-control form-control-sm nominal" min="0" value="0"></td>
                    <td><input type="text" class="form-control form-control-sm total" readonly value="0"></td>
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

            // Reset new GL select
            const newGLSelect = newRow.querySelector(
                "select[name*='ledger_account_id']",
            );
            if (newGLSelect) newGLSelect.selectedIndex = 0;

            if (isUsageRow) {
                updateRowTotal(newRow);
                updateGrandTotal();
            }
            updateCostCenterGrandTotal();
        };

        document
            .getElementById("addItemUsageDetails")
            ?.addEventListener("click", () =>
                addRow(USAGE_TABLE_BODY, "usage"),
            );
        document
            .getElementById("addItemCostCenter")
            ?.addEventListener("click", () =>
                addRow(COST_CENTER_TABLE_BODY, "costCenter"),
            );

        // Usage table input events
        USAGE_TABLE_BODY.addEventListener("input", (e) => {
            if (
                e.target.classList.contains("qty") ||
                e.target.classList.contains("nominal")
            ) {
                updateRowTotal(e.target.closest("tr"));
                updateGrandTotal();
                updateCostCenterGrandTotal();
            }
        });

        USAGE_TABLE_BODY.addEventListener("click", (e) => {
            if (e.target.classList.contains("remove-item")) {
                e.target.closest("tr").remove();
                renumberRows(USAGE_TABLE_BODY);
                updateGrandTotal();
                updateCostCenterGrandTotal();
            }
        });

        // Cost center table input events
        COST_CENTER_TABLE_BODY.addEventListener("input", (e) => {
            if (e.target.classList.contains("total")) {
                updateCostCenterGrandTotal();
            }
        });

        COST_CENTER_TABLE_BODY.addEventListener("click", (e) => {
            if (e.target.classList.contains("remove-item")) {
                e.target.closest("tr").remove();
                renumberRows(COST_CENTER_TABLE_BODY);
                updateCostCenterGrandTotal();
            }
        });
    };

    // --- Bagian 5: Vendor & Ledger Accounts ---
    const tomSelectVendor = new TomSelect(VENDOR_SELECT, {
        placeholder: "-- Select Vendor --",
        allowEmptyOption: true,
        create: false,
        sortField: { field: "text", direction: "asc" },
    });

    // Single API call — returns ALL GL accounts
    const updateLedgerAccounts = (vendorId) => {
        if (!vendorId) return;
        const url = `/admin/advance/vendor/${vendorId}/ledger-accounts`;
        fetch(url)
            .then((res) => res.json())
            .then((data) => {
                const allAccounts = data.ledger_accounts || [];

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
                currentVendorCostCenter = data.cost_center || "";
                fillCostCenterInputs(currentVendorCostCenter);

                // Recalculate
                updateCostCenterGrandTotal();
            })
            .catch((err) =>
                console.error(
                    `Failed to fetch ledger accounts for vendor ${vendorId}:`,
                    err,
                ),
            );
    };

    // GL Account change handler — auto-fill description + tax toggle
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
                updateCostCenterGrandTotal();
            }
        }
    });

    const setupVendorListeners = () => {
        if (!VENDOR_SELECT || !TYPE_SETTLEMENT_SELECT) return;

        const allVendorOptions = Array.from(
            VENDOR_SELECT.querySelectorAll("option"),
        ).map((opt) => ({
            value: opt.value,
            text: opt.text,
            type: opt.getAttribute("data-type"),
        }));

        const filterVendorOptions = () => {
            // PR-Online: type_settlement value IS the direct em_type_id (1=GAO, 2=HRO)
            const selectedTypeId = TYPE_SETTLEMENT_SELECT.value;
            tomSelectVendor.clear();
            tomSelectVendor.clearOptions();

            const filteredOptions = allVendorOptions.filter(
                (option) =>
                    option.value === "" || option.value === null ||
                    option.type == selectedTypeId,
            );

            filteredOptions.forEach((option) => {
                tomSelectVendor.addOption({
                    value: option.value,
                    text: option.text,
                    type: option.type,
                });
            });

            tomSelectVendor.refreshOptions(false);
            tomSelectVendor.setValue("");
            tomSelectVendor.disable();
            if (filteredOptions.length > 1) tomSelectVendor.enable();
        };

        TYPE_SETTLEMENT_SELECT.addEventListener("change", filterVendorOptions);
        tomSelectVendor.on("change", (vendorId) => {
            if (vendorId) updateLedgerAccounts(vendorId);
        });

        filterVendorOptions();
    };

    // --- Inisialisasi ---
    setupCurrencyInputs();
    setupDynamicSelects();
    setupSectionToggle();
    setupTableInteractions();
    setupVendorListeners();

    const dateInputAdvance = document.getElementById("submitted_date_advance");
    const dateInputSettlement = document.getElementById(
        "submitted_date_settlement",
    );

    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    const day = String(now.getDate()).padStart(2, "0");
    const hours = String(now.getHours()).padStart(2, "0");
    const minutes = String(now.getMinutes()).padStart(2, "0");
    const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`;

    dateInputAdvance.value = formattedDate;
    dateInputSettlement.value = formattedDate;
});
