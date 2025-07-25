// Helper untuk format input Rupiah otomatis
function formatRupiah(angka) {
    if (!angka) return "";
    let number = angka.toString().replace(/[^\d]/g, "");
    if (!number) return "";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(number);
}

function unformatRupiah(rupiahString) {
    if (!rupiahString) return 0;
    return parseInt(rupiahString.replace(/[^\d]/g, ""));
}

function initRupiahFormatter() {
    $(".rupiah-input").on("input", function () {
        let value = $(this).val();
        let cursorPos = this.selectionStart;
        let nonDigitsBeforeCursor = 0;

        for (let i = 0; i < cursorPos; i++) {
            if (value[i] && !/\d/.test(value[i])) {
                nonDigitsBeforeCursor++;
            }
        }

        let formatted = formatRupiah(value);
        $(this).val(formatted);

        let newNonDigitsBeforeCursor = 0;
        for (let i = 0; i < cursorPos; i++) {
            if (formatted[i] && !/\d/.test(formatted[i])) {
                newNonDigitsBeforeCursor++;
            }
        }

        let newCursorPos = cursorPos + (newNonDigitsBeforeCursor - nonDigitsBeforeCursor);
        this.setSelectionRange(newCursorPos, newCursorPos);
    });

    $(".rupiah-input").on("focus", function () {
        let value = $(this).val();
        if (!value || value === "Rp 0") {
            $(this).val("Rp 0");
        }
    });

    $(".rupiah-input").on("blur", function () {
        let value = $(this).val();
        if (value === "Rp 0") {
            $(this).val("");
        }
    });

    $(".rupiah-input").each(function () {
        let value = $(this).val();
        if (value) {
            $(this).val(formatRupiah(value));
        }
    });
}

function getRupiahValue(selector) {
    let value = $(selector).val();
    return unformatRupiah(value);
}

function setRupiahValue(selector, amount) {
    $(selector).val(formatRupiah(amount));
}

$(document).ready(function () {
    initRupiahFormatter();
});

window.RupiahFormatter = {
    format: formatRupiah,
    unformat: unformatRupiah,
    setValue: setRupiahValue,
    init: initRupiahFormatter,
};
