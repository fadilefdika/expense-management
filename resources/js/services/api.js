/**
 * API services for fetching ledger accounts and other master data
 */

export const fetchVendorLedgerAccounts = async (vendorId, taxFilter = null) => {
    try {
        let url = `/admin/advance/vendor/${vendorId}/ledger-accounts`;
        if (taxFilter) url += `?tax_filter=${taxFilter}`;
        const response = await fetch(url);
        if (!response.ok) throw new Error("Network response was not ok");
        return await response.json();
    } catch (error) {
        console.error(
            `Failed to fetch ledger accounts for vendor ${vendorId}:`,
            error,
        );
        throw error;
    }
};
