/**
 * Utility functions for currency and number formatting
 */

export const formatRupiah = (number) => {
    const n = Number(number) || 0;
    return new Intl.NumberFormat("id-ID").format(n);
};

export const parseNumber = (value) => {
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

export const renumberRows = (tableBody) => {
    tableBody.querySelectorAll("tr").forEach((row, index) => {
        row.querySelector("td:first-child").textContent = index + 1;
    });
};
