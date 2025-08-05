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
                // Temukan semua select ledger account (dalam tabel)
                const selects = document.querySelectorAll(
                    "#ledger-account-select"
                );

                selects.forEach((select) => {
                    select.innerHTML =
                        '<option value="">-- Pilih Ledger Account --</option>';

                    data.forEach((item) => {
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
    const tableBody = document.querySelector("#rincianTable tbody");
    const nominalSettlementInput =
        document.getElementById("nominal_settlement");
    const grandTotalInput = document.getElementById("grandTotal");
    const addItemBtn = document.getElementById("addItem");

    function formatRupiah(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseNumber(str) {
        return parseFloat(str.replace(/\./g, "")) || 0;
    }

    function updateRowTotal(row) {
        const qty = parseFloat(row.querySelector(".qty")?.value) || 0;
        const nominal = parseFloat(row.querySelector(".nominal")?.value) || 0;
        const total = qty * nominal;
        row.querySelector(".total").value = formatRupiah(total);
    }

    function updateGrandTotal() {
        let totalAll = 0;
        const rows = tableBody.querySelectorAll("tr");

        rows.forEach((row) => {
            const total = parseNumber(
                row.querySelector(".total")?.value || "0"
            );
            totalAll += total;
        });

        const formattedTotal = formatRupiah(totalAll);

        // Update both input fields
        grandTotalInput.value = formattedTotal;
        nominalSettlementInput.value = formattedTotal;
    }

    function renumberRows() {
        const rows = tableBody.querySelectorAll("tr");
        rows.forEach((row, index) => {
            row.querySelector("td:first-child").textContent = index + 1;
        });
    }

    function addRow() {
        const rowCount = tableBody.rows.length;
        const newRow = tableBody.insertRow();
        newRow.innerHTML = `
                <td>${rowCount + 1}</td>
                <td><input type="text" name="items[${rowCount}][description]" class="form-control form-control-sm"></td>
                <td><input type="number" name="items[${rowCount}][qty]" class="form-control form-control-sm qty" min="1" value="1"></td>
                <td><input type="number" name="items[${rowCount}][nominal]" class="form-control form-control-sm nominal" min="0" value="0"></td>
                <td><input type="text" class="form-control form-control-sm total" readonly value="0"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-item">&times;</button></td>
            `;

        // Trigger calculation for the new row
        updateRowTotal(newRow);
        updateGrandTotal();
    }

    // Initial bindings
    addItemBtn.addEventListener("click", function () {
        addRow();
    });

    tableBody.addEventListener("input", function (e) {
        if (
            e.target.classList.contains("qty") ||
            e.target.classList.contains("nominal")
        ) {
            const row = e.target.closest("tr");
            updateRowTotal(row);
            updateGrandTotal();
        }
    });

    tableBody.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-item")) {
            e.target.closest("tr").remove();
            renumberRows();
            updateGrandTotal();
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
