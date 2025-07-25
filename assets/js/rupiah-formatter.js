// Helper untuk format input Rupiah otomatis
// Gunakan class 'rupiah-input' pada input field

// Fungsi untuk format angka ke format Rupiah
function formatRupiah(angka) {
	if (!angka) return "";
	let number = angka.toString().replace(/[^\d]/g, "");
	if (!number) return "";
	return new Intl.NumberFormat("id-ID", {
		style: "currency",
		currency: "IDR",
		minimumFractionDigits: 0,
	}).format(Number(number));
}

// Fungsi untuk mengubah format Rupiah kembali ke angka
function unformatRupiah(rupiahString) {
	if (!rupiahString) return 0;
	return parseInt(rupiahString.replace(/[^\d]/g, ""));
}

// Inisialisasi format Rupiah pada semua input dengan class 'rupiah-input'
function initRupiahFormatter() {
	const inputs = document.querySelectorAll(".rupiah-input");

	inputs.forEach((input) => {
		input.addEventListener("input", function () {
			let value = input.value;

			// Jika kosong, jangan format apapun
			if (!value.replace(/[^\d]/g, "")) {
				input.value = "";
				return;
			}

			// Hitung posisi kursor (caret)
			let cursorPos = input.selectionStart;
			let rawDigits = value.replace(/[^\d]/g, "");
			let oldLength = input.value.length;

			// Format ulang
			let formatted = formatRupiah(value);
			input.value = formatted;

			// Hitung selisih panjang untuk mengatur posisi caret
			let newLength = formatted.length;
			let diff = newLength - oldLength;
			let newPos = cursorPos + diff;

			// Pastikan posisi kursor tetap di tempat logis
			input.setSelectionRange(newPos, newPos);
		});

		// Tidak perlu pakai focus/blur event agar tidak auto jadi "Rp 0"
		// Biar tetap kosong saat input dikosongkan oleh user

		// Inisialisasi jika input sudah berisi angka
		if (input.value) {
			input.value = formatRupiah(input.value);
		}
	});
}

// Fungsi untuk mendapatkan nilai numerik dari input Rupiah
function getRupiahValue(selector) {
	let element = document.querySelector(selector);
	if (!element) return 0;
	return unformatRupiah(element.value);
}

// Fungsi untuk set nilai ke input Rupiah
function setRupiahValue(selector, amount) {
	let element = document.querySelector(selector);
	if (element) {
		element.value = formatRupiah(amount);
	}
}

// Inisialisasi saat document ready
document.addEventListener("DOMContentLoaded", initRupiahFormatter);

// Export fungsi untuk penggunaan global
window.RupiahFormatter = {
	format: formatRupiah,
	unformat: unformatRupiah,
	getValue: getRupiahValue,
	setValue: setRupiahValue,
	init: initRupiahFormatter,
};
