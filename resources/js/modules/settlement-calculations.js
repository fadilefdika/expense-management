import { formatRupiah, parseNumber } from "../utils/helpers";

export const setupCalculations = (config) => {
    const {
        usageTableBody,
        usageGrandTotalInput,
        nominalSettlementInput,
        nominalAdvanceInput,
        differenceInput,
        costCenterTableBody,
        costCenterGrandTotalInput,
        hiddenCostCenterTotalInput,
        updateCurrencyCallback,
    } = config;

    const updateDifference = () => {
        if (!nominalAdvanceInput || !nominalSettlementInput || !differenceInput)
            return;
        const advance = parseNumber(nominalAdvanceInput.value);
        const settlement = parseNumber(nominalSettlementInput.value);
        differenceInput.value = formatRupiah(advance - settlement);
    };

    const updateGrandTotal = () => {
        let total = 0;
        usageTableBody.querySelectorAll("tr").forEach((row) => {
            total += parseNumber(row.querySelector(".total")?.value || "0");
        });
        const formattedTotal = formatRupiah(total);
        if (usageGrandTotalInput) usageGrandTotalInput.value = formattedTotal;
        if (nominalSettlementInput)
            nominalSettlementInput.value = formattedTotal;
        updateDifference();
    };

    /**
     * Recalculate Cost Center amounts.
     * For GL accounts with tax_percent: auto-calculate as -(totalUsage × taxPercent)
     * For GL accounts without tax_percent: keep user's manual input
     */
    const updateCostCenterGrandTotal = () => {
        let total = 0;
        const totalUsage = parseNumber(usageGrandTotalInput?.value || "0");

        costCenterTableBody.querySelectorAll("tr").forEach((row) => {
            const select = row.querySelector(
                ".ledger-account-select-cost-center",
            );
            const amountInput = row.querySelector(".total");
            if (!select || !amountInput) return;

            const selectedOption = select.options[select.selectedIndex];
            const taxPercent = selectedOption?.dataset?.taxPercent
                ? parseFloat(selectedOption.dataset.taxPercent)
                : 0;

            // Auto-calculate for tax GL accounts
            if (taxPercent > 0 && totalUsage > 0) {
                const autoAmount = -Math.floor(totalUsage * taxPercent);
                amountInput.value = formatRupiah(autoAmount);
                amountInput.readOnly = true;
            }
            // Non-tax GL: keep user input, ensure it's editable
            // (Don't overwrite user input, just format what's there)
        });

        // Sum all amounts
        costCenterTableBody.querySelectorAll("tr").forEach((row) => {
            const val = parseNumber(
                row.querySelector(".total")?.value || "0",
            );
            total += val;
        });

        const grandTotal = total + totalUsage;
        if (costCenterGrandTotalInput)
            costCenterGrandTotalInput.value = formatRupiah(grandTotal);
        if (hiddenCostCenterTotalInput)
            hiddenCostCenterTotalInput.value = grandTotal;

        // Update Nominal Settlement = Cost Center Grand Total (usage + adjustments)
        if (nominalSettlementInput)
            nominalSettlementInput.value = formatRupiah(grandTotal);
        updateDifference();

        if (typeof updateCurrencyCallback === "function") {
            updateCurrencyCallback();
        }
    };

    return {
        updateGrandTotal,
        updateCostCenterGrandTotal,
        updateDifference,
    };
};
