document.addEventListener("DOMContentLoaded", function () {
    const formatNominal = (input) => {
        input.addEventListener("input", function () {
            let value = this.value.replace(/\D/g, "");
            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    };

    const nominalAdvance = document.getElementById("nominal_advance");
    const nominalSettlement = document.getElementById("nominal_settlement");

    if (nominalAdvance) formatNominal(nominalAdvance);
    if (nominalSettlement) formatNominal(nominalSettlement);
});

document.addEventListener("DOMContentLoaded", function () {
    const rawCategorySelect = document.getElementById("expense_category");
    const rawTypeSelect = document.getElementById("expense_type");

    const allCategoryOptions = Array.from(rawCategorySelect.options);

    // Inisialisasi Tom Select untuk Type
    const tomSelectType = new TomSelect(rawTypeSelect, {
        placeholder: "-- Select Type --",
        allowEmptyOption: true,
        create: false,
        sortField: { field: "text", direction: "asc" },
    });

    // Inisialisasi Tom Select untuk Category
    const tomSelectCategory = new TomSelect(rawCategorySelect, {
        placeholder: "-- Select Category --",
        allowEmptyOption: true,
        create: false,
        sortField: { field: "text", direction: "asc" },
    });

    // Awalnya disable category select
    tomSelectCategory.disable();

    function filterCategories() {
        const selectedTypeId = rawTypeSelect.value;

        // Reset kategori
        tomSelectCategory.clear(); // kosongkan pilihan
        tomSelectCategory.clearOptions(); // hapus opsi

        if (!selectedTypeId) {
            tomSelectCategory.disable();
            return;
        }

        // Filter dan masukkan kembali ke dropdown
        const filteredOptions = allCategoryOptions.filter((option) => {
            return (
                option.value === "" || option.dataset.type === selectedTypeId
            );
        });

        filteredOptions.forEach((option) => {
            tomSelectCategory.addOption({
                value: option.value,
                text: option.text,
                type: option.dataset.type,
            });
        });

        tomSelectCategory.refreshOptions(false);
        tomSelectCategory.enable();
        tomSelectCategory.setValue("");
        tomSelectCategory.focus();
    }

    // Ketika expense_type berubah
    rawTypeSelect.addEventListener("change", filterCategories);

    // Jalankan filter saat pertama load (misal ada preset value)
    filterCategories();
});

document.addEventListener("DOMContentLoaded", function () {
    const mainTypeSelect = document.getElementById("main_type");
    const advanceSection = document.getElementById("advance-section");
    const prSection = document.getElementById("pr-section");

    // Fungsi untuk toggle section dan disable input
    function toggleSections() {
        const advanceInputs = advanceSection.querySelectorAll(
            "input, select, textarea"
        );
        const prInputs = prSection.querySelectorAll("input, select, textarea");

        if (mainTypeSelect.value === "advance") {
            advanceSection.classList.remove("d-none");
            prSection.classList.add("d-none");

            // enable advance, disable pr
            advanceInputs.forEach((i) => (i.disabled = false));
            prInputs.forEach((i) => (i.disabled = true));
        } else if (mainTypeSelect.value === "pr_online") {
            prSection.classList.remove("d-none");
            advanceSection.classList.add("d-none");

            // enable pr, disable advance
            prInputs.forEach((i) => (i.disabled = false));
            advanceInputs.forEach((i) => (i.disabled = true));
        } else {
            prSection.classList.add("d-none");
            advanceSection.classList.add("d-none");

            // disable all
            prInputs.forEach((i) => (i.disabled = true));
            advanceInputs.forEach((i) => (i.disabled = true));
        }
    }

    // Panggil saat halaman siap dan saat berubah
    toggleSections();
    mainTypeSelect.addEventListener("change", toggleSections);
});

document.addEventListener("DOMContentLoaded", () => {
    const vendorSelect = document.getElementById("vendor_id");
    const typeSelect = document.getElementById("type_settlement");
    const costCenterTableBody = document.querySelector(
        "#costCenterTable tbody"
    );
    const costCenterGrandTotalInput = document.getElementById(
        "grandTotalCostCenter"
    );
    const tableBody = document.querySelector("#rincianTable tbody");
    const nominalSettlementInput =
        document.getElementById("nominal_settlement");
    const grandTotalInput = document.getElementById("grandTotalUsageDetails");

    const formatRupiah = (number) =>
        number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    const parseNumber = (str) => parseFloat(str.replace(/\./g, "")) || 0;

    const updateGrandTotal = (
        tableBody,
        grandTotalInput,
        nominalSettlementInput
    ) => {
        let totalAll = 0;
        tableBody.querySelectorAll("tr").forEach((row) => {
            totalAll += parseNumber(row.querySelector(".total")?.value || "0");
        });
        const formatted = formatRupiah(totalAll);
        grandTotalInput.value = formatted;
        nominalSettlementInput.value = formatted;
    };

    const updateCostCenterGrandTotal = () => {
        let total = 0;
        const totalUsage = parseNumber(
            document.getElementById("grandTotalUsageDetails")?.value || "0"
        );

        costCenterTableBody.querySelectorAll("tr").forEach((row) => {
            total += parseNumber(row.querySelector(".total")?.value || "0");
        });

        total += totalUsage;
        costCenterGrandTotalInput.value = formatRupiah(total);

        updateConvertedCurrencyTotals();
    };

    const updateRowTotal = (row) => {
        const qty = parseFloat(row.querySelector(".qty")?.value) || 0;
        const nominal = parseFloat(row.querySelector(".nominal")?.value) || 0;
        row.querySelector(".total").value = formatRupiah(qty * nominal);
    };

    const renumberRows = (tbody) => {
        tbody.querySelectorAll("tr").forEach((row, index) => {
            row.querySelector("td:first-child").textContent = index + 1;
        });
    };

    const addRow = (tableBody, updateGrandTotalFn) => {
        const rowCount = tableBody.rows.length;
        const newRow = tableBody.insertRow();
        newRow.innerHTML = `
            <td>${rowCount + 1}</td>
            <td>
                <select class="form-select form-select-sm ledger-account-select-usage-details" name="items[${rowCount}][ledger_account_id]">
                    <option value="">-- Pilih Ledger Account --</option>
                </select>
            </td>
            <td><input type="text" name="items[${rowCount}][description]" class="form-control form-control-sm"></td>
            <td><input type="number" name="items[${rowCount}][qty]" class="form-control form-control-sm qty" min="1" value="1"></td>
            <td><input type="number" name="items[${rowCount}][nominal]" class="form-control form-control-sm nominal" min="0" value="0"></td>
            <td><input type="text" class="form-control form-control-sm total" readonly value="0"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
        `;

        updateRowTotal(newRow);
        updateGrandTotalFn(tableBody, grandTotalInput, nominalSettlementInput);

        // Tambahkan ini:
        const vendorId = tomSelectVendor.getValue();
        if (vendorId) {
            updateLedgerAccounts({
                url: `/admin/advance/vendor/${vendorId}/ledger-accounts?tax_filter=without_tax`,
                inputSelector: 'input[name^="items"][name$="[cost_center]"]',
                selectSelector: ".ledger-account-select-usage-details",
                placeholder: "-- Pilih Ledger Account --",
                mode: "without_tax",
            });
        }
    };

    const addCostCenterRow = () => {
        const rowCount = costCenterTableBody.rows.length;
        const newRow = costCenterTableBody.insertRow();
        newRow.innerHTML = `
            <td>${rowCount + 1}</td>
            <td><input type="text" name="items[${rowCount}][cost_center]" class="form-control form-control-sm"></td>
            <td>
                <select class="form-select form-select-sm ledger-account-select-cost-center" name="items[${rowCount}][ledger_account_id]">
                    <option value="">-- Pilih Ledger Account --</option>
                </select>
            </td>
            <td><input type="text" name="items[${rowCount}][description]" class="form-control form-control-sm"></td>
            <td><input type="number" class="form-control form-control-sm total" name="items[${rowCount}][amount]" min="0" value="0"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
        `;

        updateCostCenterGrandTotal();

        // Tambahkan ini:
        const vendorId = tomSelectVendor.getValue();
        if (vendorId) {
            updateLedgerAccounts({
                url: `/admin/advance/vendor/${vendorId}/ledger-accounts?tax_filter=with_tax`,
                inputSelector: 'input[name^="items"][name$="[cost_center]"]',
                selectSelector: ".ledger-account-select-cost-center",
                placeholder: "-- Pilih Ledger Account cost --",
                mode: "with_tax",
            });
        }
    };

    // Add new rows when clicking the add button
    document
        .getElementById("addItemUsageDetails")
        .addEventListener("click", () => addRow(tableBody, updateGrandTotal));
    document
        .getElementById("addItemCostCenter")
        .addEventListener("click", addCostCenterRow);

    // Listen for inputs and update the total
    tableBody.addEventListener("input", (e) => {
        if (
            e.target.classList.contains("qty") ||
            e.target.classList.contains("nominal")
        ) {
            const row = e.target.closest("tr");
            updateRowTotal(row);
            updateGrandTotal(
                tableBody,
                grandTotalInput,
                nominalSettlementInput
            );
            updateCostCenterGrandTotal();
        }
    });

    // Listen for clicks on the remove button
    tableBody.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-item")) {
            e.target.closest("tr").remove();
            renumberRows(tableBody);
            updateGrandTotal(
                tableBody,
                grandTotalInput,
                nominalSettlementInput
            );
        }
    });

    costCenterTableBody.addEventListener("input", (e) => {
        if (e.target.classList.contains("total")) {
            updateCostCenterGrandTotal();
        }
    });

    costCenterTableBody.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-item")) {
            e.target.closest("tr").remove();
            renumberRows(costCenterTableBody);
            updateCostCenterGrandTotal();
        }
    });

    // Initialize TomSelect for vendor selection
    const tomSelectVendor = new TomSelect(vendorSelect, {
        placeholder: "-- Select Vendor --",
        allowEmptyOption: true,
        create: false,
        sortField: { field: "text", direction: "asc" },
    });

    typeSelect.addEventListener("change", function () {
        const selectedTypeId = this.value;

        tomSelectVendor.clear();
        tomSelectVendor.clearOptions();

        const filteredOptions = Array.from(vendorSelect.options).filter(
            (option) =>
                option.value === "" || option.dataset.type === selectedTypeId
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
    });

    // Update ledger accounts based on vendor selection
    const updateLedgerAccounts = ({
        url,
        inputSelector,
        selectSelector,
        placeholder,
        mode,
    }) => {
        fetch(url)
            .then((res) => res.json())
            .then((data) => {
                document.querySelectorAll(inputSelector).forEach((input) => {
                    input.value = data.cost_center || "";
                });

                document.querySelectorAll(selectSelector).forEach((select) => {
                    const previousSelectedValue = select.value; // Save previously selected value
                    select.innerHTML = `<option value="">${placeholder}</option>`;

                    data.ledger_accounts?.forEach(
                        ({ id, ledger_account, desc_coa, tax_percent }) => {
                            const option = document.createElement("option");
                            option.value = id;
                            option.textContent = `${ledger_account} - ${desc_coa}`;

                            if (mode === "with_tax") {
                                option.dataset.ledger = ledger_account;
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

                    // Re-select the previous value if any
                    if (previousSelectedValue) {
                        select.value = previousSelectedValue;
                    }

                    if (mode === "with_tax") {
                        select.addEventListener("change", () => {
                            const selected =
                                select.options[select.selectedIndex];
                            const taxPercent =
                                parseFloat(selected.dataset.taxPercent) || 0;
                            const auto = selected.dataset.autoCalculate;
                            const totalUsage = parseNumber(
                                document.getElementById(
                                    "grandTotalUsageDetails"
                                )?.value || "0"
                            );
                            const row = select.closest("tr");
                            const amountInput = row?.querySelector(".total");

                            if (amountInput && auto && taxPercent > 0) {
                                amountInput.value = -Math.floor(
                                    totalUsage * taxPercent
                                );
                            }
                            updateCostCenterGrandTotal();
                        });

                        select.dispatchEvent(new Event("change"));
                    }
                });
            })
            .catch((err) => console.error("Ledger fetch failed:", err));
    };

    tomSelectVendor.on("change", (vendorId) => {
        if (!vendorId) return;

        updateLedgerAccounts({
            url: `/admin/advance/vendor/${vendorId}/ledger-accounts?tax_filter=without_tax`,
            inputSelector: 'input[name^="items"][name$="[cost_center]"]',
            selectSelector: ".ledger-account-select-usage-details",
            placeholder: "-- Pilih Ledger Account --",
            mode: "without_tax",
        });

        updateLedgerAccounts({
            url: `/admin/advance/vendor/${vendorId}/ledger-accounts?tax_filter=with_tax`,
            inputSelector: 'input[name^="items"][name$="[cost_center]"]',
            selectSelector: ".ledger-account-select-cost-center",
            placeholder: "-- Pilih Ledger Account cost --",
            mode: "with_tax",
        });
    });

    if (typeSelect.value) typeSelect.dispatchEvent(new Event("change"));
});

document.addEventListener("DOMContentLoaded", function () {
    const mainType = document.getElementById("main_type");
    const advanceSection = document.getElementById("advance-section");
    const prSection = document.getElementById("pr-section");

    function toggleSections() {
        const value = mainType.value;
        advanceSection.classList.toggle("d-none", value !== "advance");
        prSection.classList.toggle("d-none", value !== "pr_online");
    }

    mainType.addEventListener("change", toggleSections);
});

document.addEventListener("DOMContentLoaded", function () {
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
