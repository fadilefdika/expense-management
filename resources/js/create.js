document.addEventListener("DOMContentLoaded", () => {
    // === DOM & Constants ===
    const NOMINAL_INPUTS = document.querySelectorAll(
        "#nominal_advance, #nominal_settlement"
    );
    const EXPENSE_CATEGORY_SELECT = document.getElementById("expense_category");
    const EXPENSE_TYPE_SELECT = document.getElementById("expense_type");
    const MAIN_TYPE_SELECT = document.getElementById("main_type");
    const VENDOR_SELECT = document.getElementById("vendor_id");
    const TYPE_SETTLEMENT_SELECT = document.getElementById("type_settlement");
    const USAGE_TABLE_BODY = document.querySelector("#rincianTable tbody");
    const COST_CENTER_TABLE_BODY = document.querySelector(
        "#costCenterTable tbody"
    );
    const USAGE_GRAND_TOTAL_INPUT = document.getElementById(
        "grandTotalUsageDetails"
    );
    const COST_CENTER_GRAND_TOTAL_INPUT = document.getElementById(
        "grandTotalCostCenter"
    );
    const HIDDEN_COST_CENTER_TOTAL_INPUT = document.getElementById(
        "grand_total_cost_center"
    );
    const NOMINAL_SETTLEMENT_INPUT =
        document.getElementById("nominal_settlement");

    // === Helper Functions ===

    /**
     * @param {number|string} number
     * @returns {string}
     */
    const formatRupiah = (number) => {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    };

    /**
     * @param {string} str
     * @returns {number}
     */
    const parseNumber = (str) => {
        return parseFloat(str.replace(/\./g, "")) || 0;
    };

    const renumberRows = (tableBody) => {
        tableBody.querySelectorAll("tr").forEach((row, index) => {
            row.querySelector("td:first-child").textContent = index + 1;
        });
    };

    const updateGrandTotal = () => {
        let total = 0;
        USAGE_TABLE_BODY.querySelectorAll("tr").forEach((row) => {
            total += parseNumber(row.querySelector(".total")?.value || "0");
        });
        const formattedTotal = formatRupiah(total);
        USAGE_GRAND_TOTAL_INPUT.value = formattedTotal;
        NOMINAL_SETTLEMENT_INPUT.value = formattedTotal;
    };

    const updateCostCenterGrandTotal = () => {
        let total = 0;
        const totalUsage = parseNumber(USAGE_GRAND_TOTAL_INPUT.value);
        COST_CENTER_TABLE_BODY.querySelectorAll("tr").forEach((row) => {
            total += parseNumber(row.querySelector(".total")?.value || "0");
        });

        const grandTotal = total + totalUsage;
        COST_CENTER_GRAND_TOTAL_INPUT.value = formatRupiah(grandTotal);
        HIDDEN_COST_CENTER_TOTAL_INPUT.value = grandTotal;
        updateConvertedCurrencyTotals();
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
                            option.dataset.type === selectedTypeId
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
        filterCategories(); // Initial call
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

            // Hide all sections and disable their inputs
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

            // Show relevant section and enable its inputs
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
            const selectClass = isUsageRow
                ? "ledger-account-select-usage-details"
                : "ledger-account-select-cost-center";
            const namePrefix = isUsageRow ? "usage_items" : "items_costcenter";
            const inputAmountType = isUsageRow ? "number" : "text";
            const readonlyAttribute = isUsageRow ? "" : "readonly";
            const qtyCol = isUsageRow
                ? `<td><input type="number" name="${namePrefix}[${rowCount}][qty]" class="form-control form-control-sm qty" min="1" value="1"></td>`
                : "";
            const nominalCol = isUsageRow
                ? `<td><input type="number" name="${namePrefix}[${rowCount}][nominal]" class="form-control form-control-sm nominal" min="0" value="0"></td>`
                : "";
            const costCenterCol = isUsageRow
                ? ""
                : `<td><input type="text" name="${namePrefix}[${rowCount}][cost_center]" class="form-control form-control-sm"></td>`;

            newRow.innerHTML = `
                <td>${rowCount + 1}</td>
                ${costCenterCol}
                <td>
                    <select class="form-select form-select-sm ${selectClass}" name="${namePrefix}[${rowCount}][ledger_account_id]">
                        <option value="">-- Select GL Account --</option>
                    </select>
                </td>
                <td><input type="text" name="${namePrefix}[${rowCount}][description]" class="form-control form-control-sm" required></td>
                ${qtyCol}
                ${nominalCol}
                <td><input type="${inputAmountType}" class="form-control form-control-sm total" name="${namePrefix}[${rowCount}][amount]" ${readonlyAttribute} value="0"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
            `;

            if (isUsageRow) {
                updateRowTotal(newRow);
                updateGrandTotal();
            }
            updateCostCenterGrandTotal();

            // Panggil fungsi untuk update ledger accounts jika vendor sudah dipilih
            const vendorId = tomSelectVendor.getValue();
            if (vendorId) {
                updateLedgerAccounts(vendorId);
            }
        };

        document
            .getElementById("addItemUsageDetails")
            ?.addEventListener("click", () =>
                addRow(USAGE_TABLE_BODY, "usage")
            );
        document
            .getElementById("addItemCostCenter")
            ?.addEventListener("click", () =>
                addRow(COST_CENTER_TABLE_BODY, "costCenter")
            );

        // Event delegation untuk tabel usage
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

        // Event delegation untuk tabel cost center
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

    const updateLedgerAccounts = (vendorId) => {
        // Fetch and update for Usage Details (without tax)
        fetchLedgerAccounts(
            vendorId,
            "without_tax",
            ".ledger-account-select-usage-details",
            "-- Select GL Account --"
        );
        // Fetch and update for Cost Center (with tax)
        fetchLedgerAccounts(
            vendorId,
            "with_tax",
            ".ledger-account-select-cost-center",
            "-- Select GL Account --"
        );
    };

    const fetchLedgerAccounts = (
        vendorId,
        taxFilter,
        selectSelector,
        placeholder
    ) => {
        const url = `/admin/advance/vendor/${vendorId}/ledger-accounts?tax_filter=${taxFilter}`;
        fetch(url)
            .then((res) => res.json())
            .then((data) => {
                document.querySelectorAll(selectSelector).forEach((select) => {
                    const previousSelectedValue = select.value;
                    select.innerHTML = `<option value="">${placeholder}</option>`;

                    data.ledger_accounts?.forEach(
                        ({ id, ledger_account, desc_coa, tax_percent }) => {
                            const option = document.createElement("option");
                            option.value = id;
                            option.textContent = `${ledger_account} - ${desc_coa}`;
                            if (taxFilter === "with_tax") {
                                option.dataset.taxPercent = tax_percent ?? "";
                                if (
                                    tax_percent !== null &&
                                    !isNaN(parseFloat(tax_percent)) &&
                                    parseFloat(tax_percent) > 0
                                ) {
                                    option.dataset.autoCalculate = "true";
                                }
                            }
                            select.appendChild(option);
                        }
                    );

                    if (previousSelectedValue) {
                        select.value = previousSelectedValue;
                    }

                    if (taxFilter === "with_tax") {
                        select.addEventListener("change", () => {
                            const selectedOption =
                                select.options[select.selectedIndex];
                            const taxPercent =
                                parseFloat(selectedOption.dataset.taxPercent) ||
                                0;
                            const autoCalculate =
                                selectedOption.dataset.autoCalculate;
                            const totalUsage = parseNumber(
                                USAGE_GRAND_TOTAL_INPUT.value
                            );
                            const amountInput = select
                                .closest("tr")
                                ?.querySelector(".total");

                            if (
                                amountInput &&
                                autoCalculate &&
                                taxPercent > 0
                            ) {
                                amountInput.value = -Math.floor(
                                    totalUsage * (taxPercent / 100)
                                );
                            }
                            updateCostCenterGrandTotal();
                        });
                        select.dispatchEvent(new Event("change"));
                    }
                });

                if (data.cost_center) {
                    document
                        .querySelectorAll(
                            'input[name^="items"][name$="[cost_center]"]'
                        )
                        .forEach((input) => {
                            input.value = data.cost_center;
                        });
                }
            })
            .catch((err) =>
                console.error(
                    `Failed to fetch ledger accounts for vendor ${vendorId}:`,
                    err
                )
            );
    };

    const setupVendorListeners = () => {
        if (!VENDOR_SELECT || !TYPE_SETTLEMENT_SELECT) return;

        const allVendorOptions = Array.from(VENDOR_SELECT.options);
        const filterVendorOptions = () => {
            const selectedTypeId = TYPE_SETTLEMENT_SELECT.value;
            tomSelectVendor.clear();
            tomSelectVendor.clearOptions();

            const filteredOptions = allVendorOptions.filter(
                (option) =>
                    option.value === "" ||
                    option.dataset.type === selectedTypeId
            );

            filteredOptions.forEach((option) => {
                tomSelectVendor.addOption({
                    value: option.value,
                    text: option.text,
                    type: option.dataset.type,
                });
            });

            tomSelectVendor.refreshOptions(false);
            tomSelectVendor.setValue("");
            tomSelectVendor.disable();
            if (filteredOptions.length > 1) tomSelectVendor.enable();
        };

        TYPE_SETTLEMENT_SELECT.addEventListener("change", filterVendorOptions);
        tomSelectVendor.on("change", (vendorId) => {
            if (vendorId) {
                updateLedgerAccounts(vendorId);
            }
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
        "submitted_date_settlement"
    );

    // Ambil waktu saat ini dan format jadi YYYY-MM-DDTHH:MM
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    const day = String(now.getDate()).padStart(2, "0");
    const hours = String(now.getHours()).padStart(2, "0");
    const minutes = String(now.getMinutes()).padStart(2, "0");
    const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`;

    // Set sebagai nilai default
    dateInputAdvance.value = formattedDate;
    dateInputSettlement.value = formattedDate;
});
