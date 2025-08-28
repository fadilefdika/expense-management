document.addEventListener("DOMContentLoaded", () => {
    // === DOM & Constants ===
    const NOMINAL_INPUTS = document.querySelectorAll(
        "#nominal_advance_edit, #nominal_settlement_edit"
    );
    const EXPENSE_CATEGORY_SELECT = document.getElementById(
        "expense_category_edit"
    );
    const initialCategoryValue = EXPENSE_CATEGORY_SELECT.value;

    const EXPENSE_TYPE_SELECT = document.getElementById("expense_type_edit");
    // const MAIN_TYPE_SELECT = document.getElementById("main_type");
    const VENDOR_SELECT = document.getElementById("vendor_id_edit");
    // const TYPE_SETTLEMENT_SELECT = document.getElementById("type_settlement");
    const USAGE_TABLE_BODY = document.querySelector("#rincianTableEdit tbody");
    const COST_CENTER_TABLE_BODY = document.querySelector(
        "#costCenterTableEdit tbody"
    );
    const USAGE_GRAND_TOTAL_INPUT = document.getElementById(
        "grandTotalUsageDetailsEdit"
    );
    const COST_CENTER_GRAND_TOTAL_INPUT = document.getElementById(
        "grandTotalCostCenterEdit"
    );
    const HIDDEN_COST_CENTER_TOTAL_INPUT = document.getElementById(
        "grand_total_cost_center_edit"
    );
    const NOMINAL_SETTLEMENT_INPUT = document.getElementById(
        "nominal_settlement_edit"
    );
    const USD_TOTAL_INPUT = document.getElementById("usd_total");
    const YEN_TOTAL_INPUT = document.getElementById("yen_total");
    const USD_HIDDEN_INPUT = document.getElementById("usd_settlement");
    const YEN_HIDDEN_INPUT = document.getElementById("yen_settlement");

    const updateConvertedCurrencyTotals = () => {
        if (USD_TOTAL_INPUT && USD_HIDDEN_INPUT) {
            USD_HIDDEN_INPUT.value = parseNumber(USD_TOTAL_INPUT.value || "0");
        }
        if (YEN_TOTAL_INPUT && YEN_HIDDEN_INPUT) {
            YEN_HIDDEN_INPUT.value = parseNumber(YEN_TOTAL_INPUT.value || "0");
        }
    };

    // === Helper Functions ===

    /**
     * @param {number|string} number
     * @returns {string}
     */
    const formatRupiah = (number) => {
        const n = Number(number) || 0;
        return new Intl.NumberFormat("id-ID").format(n);
    };

    /**
     * @param {string} str
     * @returns {number}
     */
    const parseNumber = (value) => {
        if (!value) return 0;

        value = value.toString();

        // Hilangkan semua karakter kecuali digit, minus, titik, koma
        value = value.replace(/[^0-9.,-]/g, "");

        // Hilangkan pemisah ribuan (titik)
        value = value.replace(/\./g, "");

        // Ganti koma dengan titik (untuk desimal)
        value = value.replace(/,/g, ".");

        return parseFloat(value) || 0;
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
        const totalUsageRaw = USAGE_GRAND_TOTAL_INPUT.value;
        const totalUsage = parseNumber(totalUsageRaw);

        console.log("===== DEBUG updateCostCenterGrandTotal =====");
        console.log("🔍 USAGE_GRAND_TOTAL_INPUT.value (raw):", totalUsageRaw);
        console.log("🔍 totalUsage (parsed):", totalUsage);

        COST_CENTER_TABLE_BODY.querySelectorAll("tr").forEach((row, idx) => {
            const inputEl = row.querySelector(".total");
            const rowValueRaw = inputEl?.value || "0";
            const rowValueParsed = parseNumber(rowValueRaw);

            console.log(
                `🔍 Row ${idx + 1} total raw:`,
                rowValueRaw,
                "| after parseNumber():",
                rowValueParsed
            );

            total += rowValueParsed;

            // 🔑 Reformat tampilan ke user biar tetap ada titik ribuan
            inputEl.value = formatRupiah(rowValueParsed);
        });

        console.log("🔍 Sum of Cost Center totals (before usage):", total);

        const grandTotal = total + totalUsage;
        console.log("✅ Grand Total (Cost Center + Usage):", grandTotal);

        // Format output
        const formattedGrandTotal = formatRupiah(grandTotal);
        COST_CENTER_GRAND_TOTAL_INPUT.value = formattedGrandTotal;
        HIDDEN_COST_CENTER_TOTAL_INPUT.value = grandTotal;

        console.log(
            "📌 COST_CENTER_GRAND_TOTAL_INPUT (formatted):",
            formattedGrandTotal
        );
        console.log("📌 HIDDEN_COST_CENTER_TOTAL_INPUT (raw):", grandTotal);

        updateConvertedCurrencyTotals();
        console.log("===== END DEBUG =====");
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

                // 🔑 kalau ada default value dari server, set itu
                if (initialCategoryValue) {
                    tomSelectCategory.setValue(initialCategoryValue);
                } else {
                    tomSelectCategory.setValue("");
                }
            } else {
                tomSelectCategory.disable();
            }
        };

        tomSelectType.on("change", filterCategories);
        filterCategories(); // Initial call
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
                                    ["22101104", "11701201"].includes(
                                        ledger_account
                                    )
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
        if (!VENDOR_SELECT) return;

        tomSelectVendor.on("change", (vendorId) => {
            if (vendorId) {
                updateLedgerAccounts(vendorId);
            }
        });

        // kalau sudah ada vendor selected saat load awal → langsung fetch
        const initialVendorId = tomSelectVendor.getValue();
        if (initialVendorId) {
            updateLedgerAccounts(initialVendorId);
        }
    };

    // --- Inisialisasi ---
    setupCurrencyInputs();
    setupDynamicSelects();
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
    if (dateInputAdvance) dateInputAdvance.value = formattedDate;
    if (dateInputSettlement) dateInputSettlement.value = formattedDate;
});
