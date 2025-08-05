document.addEventListener("DOMContentLoaded", function () {
    const vendorSelect = document.getElementById("vendor_id");
    const typeSelect = document.getElementById("type_settlement");

    // Init Tom Select untuk vendor
    const allVendorOptions = Array.from(vendorSelect.options);
    const tomSelectVendor = new TomSelect(vendorSelect, {
        placeholder: "-- Select Vendor --",
        allowEmptyOption: true,
        create: false,
        sortField: { field: "text", direction: "asc" },
    });

    // Filter vendor berdasarkan type_settlement
    typeSelect.addEventListener("change", function () {
        const selectedTypeId = this.value;

        tomSelectVendor.clear();
        tomSelectVendor.clearOptions();

        const matchingOptions = allVendorOptions.filter((option) => {
            return (
                option.value === "" || option.dataset.type === selectedTypeId
            );
        });

        matchingOptions.forEach((option) => {
            tomSelectVendor.addOption({
                value: option.value,
                text: option.text,
                type: option.dataset.type,
            });
        });

        tomSelectVendor.refreshOptions(false);
        tomSelectVendor.setValue("");

        tomSelectVendor.disable();
        if (matchingOptions.length > 1) {
            tomSelectVendor.enable();
        }
    });

    // === 💡 Poin utama: Saat vendor berubah, ambil ledger accounts
    vendorSelect.addEventListener("change", function () {
        const vendorId = this.value;

        if (!vendorId) return;

        fetch(`/admin/advance/vendor/${vendorId}/ledger-accounts`)
            .then((response) => response.json())
            .then((data) => {
                // 💡 Isi semua cost center
                const costCenterInputs = document.querySelectorAll(
                    'input[name^="items"][name$="[cost_center]"]'
                );
                costCenterInputs.forEach((input) => {
                    input.value = data.cost_center || "";
                });

                // 💡 Update ledger accounts
                const selects = document.querySelectorAll(
                    ".ledger-account-select"
                );
                selects.forEach((select) => {
                    select.innerHTML =
                        '<option value="">-- Pilih Ledger Account --</option>';

                    // ⛔️ Pastikan ini yang pakai forEach
                    (data.ledger_accounts || []).forEach((item) => {
                        const option = document.createElement("option");
                        option.value = item.id;
                        option.text = `${item.ledger_account} - ${item.desc_coa}`;
                        select.appendChild(option);
                    });
                });
            });
    });
});

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

document.addEventListener("DOMContentLoaded", function () {
    // Utilities
    const formatRupiah = (number) =>
        number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    const parseNumber = (str) => parseFloat(str.replace(/\./g, "")) || 0;

    // Elements
    const tableBody = document.querySelector("#rincianTable tbody");
    const costCenterTableBody = document.querySelector(
        "#costCenterTable tbody"
    );
    const nominalSettlementInput =
        document.getElementById("nominal_settlement");
    const grandTotalInput = document.getElementById("grandTotalUsageDetails");
    const costCenterGrandTotalInput = document.getElementById(
        "grandTotalCostCenter"
    );
    const addItemBtn = document.getElementById("addItemUsageDetails");
    const addCostCenterItemBtn = document.getElementById("addItemCostCenter");

    // === RINCIAN TABLE ===
    function updateRowTotal(row) {
        const qty = parseFloat(row.querySelector(".qty")?.value) || 0;
        const nominal = parseFloat(row.querySelector(".nominal")?.value) || 0;
        row.querySelector(".total").value = formatRupiah(qty * nominal);
    }

    function updateGrandTotal() {
        let totalAll = 0;
        tableBody.querySelectorAll("tr").forEach((row) => {
            totalAll += parseNumber(row.querySelector(".total")?.value || "0");
        });
        const formatted = formatRupiah(totalAll);
        grandTotalInput.value = formatted;
        nominalSettlementInput.value = formatted;
    }

    function updateGrandTotalCostCenter() {
        const grandTotal = parseNumber(
            document.getElementById("grandTotalUsageDetails")?.value || "0"
        );

        let totalPotongan = 0;
        const rows = document.querySelectorAll("tbody tr");

        rows.forEach((row) => {
            const amountInput = row.querySelector(
                'input[name^="items"][name$="[amount]"]'
            );
            const value = parseNumber(amountInput?.value || "0");
            if (value < 0) {
                totalPotongan += Math.abs(value); // potongan dikumpulkan
            }
        });

        const finalTotal = grandTotal - totalPotongan;
        const costCenterInput = document.getElementById("grandTotalCostCenter");
        if (costCenterInput) {
            costCenterInput.value = formatRupiah(finalTotal);
        }
    }

    function renumberRows(tbody) {
        tbody.querySelectorAll("tr").forEach((row, index) => {
            row.querySelector("td:first-child").textContent = index + 1;
        });
    }

    function addRow() {
        const rowCount = tableBody.rows.length;
        const newRow = tableBody.insertRow();

        newRow.innerHTML = `
            <td>${rowCount + 1}</td>
            <td>
                <select class="form-select form-select-sm ledger-account-select" name="items[${rowCount}][ledger_account_id]">
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
        updateGrandTotal();

        // Fetch ledger accounts
        const vendorId = document.getElementById("vendor_id")?.value;
        if (vendorId) {
            fetch(`/admin/advance/vendor/${vendorId}/ledger-accounts`)
                .then((res) => res.json())
                .then((data) => {
                    const select = newRow.querySelector(
                        ".ledger-account-select"
                    );
                    select.innerHTML =
                        '<option value="">-- Pilih Ledger Account --</option>';
                    (data.ledger_accounts || []).forEach((item) => {
                        const option = document.createElement("option");
                        option.value = item.id;
                        option.text = `${item.ledger_account} - ${item.desc_coa}`;
                        select.appendChild(option);
                    });
                });
        }
    }

    addItemBtn.addEventListener("click", addRow);

    tableBody.addEventListener("input", (e) => {
        if (
            e.target.classList.contains("qty") ||
            e.target.classList.contains("nominal")
        ) {
            const row = e.target.closest("tr");
            updateRowTotal(row);
            updateGrandTotal();
            updateGrandTotalCostCenter();
        }
    });

    tableBody.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-item")) {
            e.target.closest("tr").remove();
            renumberRows(tableBody);
            updateGrandTotal();
        }
    });

    // === COST CENTER TABLE ===
    function updateCostCenterGrandTotal() {
        let total = 0;
        costCenterTableBody.querySelectorAll("tr").forEach((row) => {
            total += parseNumber(row.querySelector(".total")?.value || "0");
        });
        costCenterGrandTotalInput.value = formatRupiah(total);
    }

    function addCostCenterRow() {
        const rowCount = costCenterTableBody.rows.length;
        const newRow = costCenterTableBody.insertRow();

        newRow.innerHTML = `
            <td>${rowCount + 1}</td>
            <td><input type="text" name="items[${rowCount}][cost_center]" class="form-control form-control-sm"></td>
            <td>
                <select class="form-select form-select-sm ledger-account-select" name="items[${rowCount}][ledger_account_id]">
                    <option value="">-- Pilih Ledger Account --</option>
                </select>
            </td>
            <td><input type="text" name="items[${rowCount}][description]" class="form-control form-control-sm"></td>
            <td><input type="number" class="form-control form-control-sm total" name="items[${rowCount}][amount]" min="0" value="0"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
        `;

        updateCostCenterGrandTotal();

        const vendorId = document.getElementById("vendor_id")?.value;
        if (vendorId) {
            fetch(`/admin/advance/vendor/${vendorId}/ledger-accounts`)
                .then((res) => res.json())
                .then((data) => {
                    console.log("✅ Fetch berhasil, data ledger:", data); // 🟢 DEBUG 1

                    const select = newRow.querySelector(
                        ".ledger-account-select"
                    );

                    select.innerHTML =
                        '<option value="">-- Pilih Ledger Account --</option>';

                    (data.ledger_accounts || []).forEach((item) => {
                        const option = document.createElement("option");
                        option.value = item.id;
                        option.setAttribute("data-ledger", item.ledger_account);
                        option.text = `${item.ledger_account} - ${item.desc_coa}`;

                        // AUTO SELECT
                        if (
                            item.ledger_account === "22101104" ||
                            item.ledger_account === "11701201"
                        ) {
                            option.setAttribute("data-auto-calculate", "true");

                            console.log(
                                "🎯 Ledger account cocok auto-select:",
                                item.ledger_account
                            ); // 🟢 DEBUG 2

                            // Pilih langsung jika belum ada yang dipilih
                            if (!select.querySelector("option[selected]")) {
                                option.selected = true;
                                console.log(
                                    "✅ Option selected:",
                                    option.value
                                ); // 🟢 DEBUG 3
                            }
                        }

                        select.appendChild(option);
                    });

                    const costCenterInput = newRow.querySelector(
                        `input[name="items[${rowCount}][cost_center]"]`
                    );
                    if (costCenterInput) {
                        costCenterInput.value = data.cost_center || "";
                    }

                    // Tambahkan event listener
                    select.addEventListener("change", function () {
                        const selectedOption =
                            select.options[select.selectedIndex];
                        const ledgerCode =
                            selectedOption.getAttribute("data-ledger");
                        const auto = selectedOption.getAttribute(
                            "data-auto-calculate"
                        );

                        console.log("🟡 Dropdown changed"); // 🟡 DEBUG 4
                        console.log("📦 Selected ledger code:", ledgerCode); // 🟡 DEBUG 5
                        console.log("📦 Auto calculate?", auto); // 🟡 DEBUG 6

                        const totalUsage = parseNumber(
                            document.getElementById("grandTotalUsageDetails")
                                ?.value || "0"
                        );
                        console.log("📦 Total usage parsed:", totalUsage); // 🟡 DEBUG 7

                        let amount = 0;
                        if (ledgerCode === "22101104") {
                            amount = Math.floor(totalUsage * 0.02);
                        } else if (ledgerCode === "11701201") {
                            amount = Math.floor(totalUsage * 0.1);
                        }

                        const amountInput = newRow.querySelector(
                            `input[name="items[${rowCount}][amount]"]`
                        );
                        if (amountInput && auto) {
                            amountInput.value = -amount;
                            console.log("✅ Amount diisi otomatis:", -amount); // 🟢 DEBUG 8
                        }

                        updateGrandTotalCostCenter();
                    });

                    // Trigger change
                    console.log("🔁 Triggering change event..."); // 🟢 DEBUG 9
                    select.dispatchEvent(new Event("change"));
                });
        }
    }

    addCostCenterItemBtn.addEventListener("click", addCostCenterRow);

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
