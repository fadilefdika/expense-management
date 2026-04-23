import { fetchVendorLedgerAccounts } from "../services/api";

export const setupVendorLogic = (config) => {
    const { vendorSelect, typeSettlementSelect, onLedgerAccountsUpdated } =
        config;

    if (!vendorSelect) return null;

    const tomSelectVendor = new TomSelect(vendorSelect, {
        placeholder: "-- Select Vendor --",
        allowEmptyOption: true,
        create: false,
        sortField: { field: "text", direction: "asc" },
    });

    // Simpan sebagai ARRAY OBJEK murni (Data tetap aman di sini)
    const allVendorOptions = Array.from(
        vendorSelect.querySelectorAll("option"),
    ).map((opt) => ({
        value: opt.value,
        text: opt.text,
        type: opt.getAttribute("data-type"),
    }));

    const filterVendorOptions = () => {
        if (!typeSettlementSelect) return;

        const settlementCode = typeSettlementSelect.value;
        let selectedTypeId = null;

        if (settlementCode) {
            const parts = settlementCode.split("-");
            if (parts.length > 0) {
                // Vendor em_type_id: 1=GA (GAO/GAS/GAA), 2=HR (HRO/HRS/HRA)
                const prefix = parts[0].substring(0, 2);
                const typeMapping = { GA: "1", HR: "2" };
                selectedTypeId = typeMapping[prefix];
            }
        }

        const currentVendorId = tomSelectVendor.getValue();
        const initialVendorId =
            vendorSelect.dataset.initialValue || currentVendorId;

        tomSelectVendor.clear();
        tomSelectVendor.clearOptions();

        const filteredOptions = allVendorOptions.filter((option) => {
            if (option.value === "" || option.value === null) return true;
            if (!selectedTypeId) return true;
            return option.type == selectedTypeId;
        });

        filteredOptions.forEach((option) => {
            tomSelectVendor.addOption({
                value: option.value,
                text: option.text,
                type: option.type,
            });
        });

        tomSelectVendor.refreshOptions(false);

        if (
            initialVendorId &&
            filteredOptions.some((o) => o.value == initialVendorId)
        ) {
            tomSelectVendor.setValue(initialVendorId);
        } else {
            tomSelectVendor.setValue("");
        }

        const isEnabled =
            filteredOptions.length > 1 || tomSelectVendor.getValue() !== "";
        if (isEnabled) {
            tomSelectVendor.enable();
        } else {
            tomSelectVendor.disable();
        }
    };

    // Single API call — returns ALL GL accounts with tax_percent info
    const updateLedgerAccounts = async (vendorId) => {
        if (!vendorId) return;
        try {
            const data = await fetchVendorLedgerAccounts(vendorId); // no filter
            if (typeof onLedgerAccountsUpdated === "function") {
                onLedgerAccountsUpdated({ data, vendorId });
            }
        } catch (error) {
            console.error("Error updating ledger accounts:", error);
        }
    };

    tomSelectVendor.on("change", (vendorId) => {
        if (vendorId) updateLedgerAccounts(vendorId);
    });

    filterVendorOptions();

    return {
        tomSelectVendor,
        filterVendorOptions,
        updateLedgerAccounts,
    };
};
