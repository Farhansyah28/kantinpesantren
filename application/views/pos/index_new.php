<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-cash-register mr-2"></i>POS Kantin
                    </h1>
                </div>
                <div class="col-sm-6 text-right align-self-center">
                    <span id="pos-clock" style="font-size:1.1em;font-weight:500;"><i class="fas fa-clock mr-1"></i><span id="clock-time"></span></span>
                </div>
            </div>
            <!-- Action Buttons -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('menu/stok_management') ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-boxes mr-1"></i> Manajemen Stok
                        </a>
                        <a href="<?= base_url('menu/riwayat_stok') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-history mr-1"></i> Riwayat Stok
                        </a>
                        <a href="<?= base_url('pos/riwayat_hari_ini') ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-clock mr-1"></i> Riwayat Hari Ini
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Kolom Kiri - Form Transaksi -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-gradient-primary">
                            <h3 class="card-title">
                                <i class="fas fa-shopping-cart mr-1"></i> Transaksi Baru
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Pilih Santri -->
                            <div class="form-group">
                                <label for="santri_select">
                                    <i class="fas fa-user mr-1"></i>Pilih Santri <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" id="santri_select" style="width: 100%;">
                                    <option value="">Cari santri...</option>
                                </select>
                            </div>

                            <!-- Info Santri -->
                            <div id="santri_info" class="alert alert-info" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Nama:</strong> <span id="santri_nama"></span><br>
                                        <strong>NIS:</strong> <span id="santri_nis"></span><br>
                                        <strong>Kelas:</strong> <span id="santri_kelas"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Saldo Jajan:</strong> <span id="santri_saldo" class="text-success"></span><br>
                                        <strong>Pengeluaran Hari Ini:</strong> <span id="santri_pengeluaran" class="text-warning"></span><br>
                                        <strong>Sisa Limit:</strong> <span id="santri_sisa_limit" class="text-info"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pilih Menu -->
                            <div class="form-group">
                                <label for="menu_select">
                                    <i class="fas fa-utensils mr-1"></i>Pilih Menu <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" id="menu_select" style="width: 100%;">
                                    <option value="">Cari menu...</option>
                                </select>
                            </div>

                            <!-- Info Menu -->
                            <div id="menu_info" class="alert alert-success" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Menu:</strong> <span id="menu_nama"></span><br>
                                        <strong>Pemilik:</strong> <span id="menu_pemilik"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Harga:</strong> <span id="menu_harga" class="text-success"></span><br>
                                        <strong>Stok:</strong> <span id="menu_stok" class="text-info"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Jumlah -->
                            <div class="form-group">
                                <label for="quantity">
                                    <i class="fas fa-hashtag mr-1"></i>Jumlah
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="quantity" min="1" value="1">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="add_item">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Item -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="cart_table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Menu</th>
                                            <th width="100">Harga</th>
                                            <th width="80">Jumlah</th>
                                            <th width="120">Subtotal</th>
                                            <th width="80">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart_items">
                                        <!-- Item akan ditambahkan di sini -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Total -->
                            <div class="row">
                                <div class="col-md-6 offset-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Total:</strong></td>
                                            <td class="text-right"><strong id="total_amount">Rp 0</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-warning btn-lg" id="clear_cart">
                                        <i class="fas fa-trash mr-1"></i> Kosongkan
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-success btn-lg" id="process_transaction">
                                        <i class="fas fa-check mr-1"></i> Proses Transaksi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan - Menu Cepat -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-gradient-info">
                            <h3 class="card-title">
                                <i class="fas fa-fire mr-1"></i> Menu Populer
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row" id="popular_menu">
                                <!-- Menu populer akan ditampilkan di sini -->
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-gradient-secondary">
                            <h3 class="card-title">
                                <i class="fas fa-clock mr-1"></i> Transaksi Terakhir
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="recent_transactions">
                                <!-- Transaksi terakhir akan ditampilkan di sini -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}
.cart-item {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
}
.menu-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 10px;
    text-align: center;
}
.menu-card:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.2);
}
.menu-card img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 5px;
    margin-bottom: 5px;
}
.menu-card .menu-name {
    font-weight: bold;
    font-size: 12px;
    margin-bottom: 5px;
}
.menu-card .menu-price {
    color: #28a745;
    font-weight: bold;
    font-size: 11px;
}
.recent-transaction {
    border-bottom: 1px solid #dee2e6;
    padding: 8px 0;
}
.recent-transaction:last-child {
    border-bottom: none;
}
.recent-transaction .time {
    font-size: 11px;
    color: #6c757d;
}
.recent-transaction .amount {
    font-weight: bold;
    color: #28a745;
}
</style>

<script>
$(document).ready(function() {
    let selectedSantri = null;
    let selectedMenu = null;
    let cart = [];
    
    // Inisialisasi Select2 untuk santri
    $('#santri_select').select2({
        ajax: {
            url: "<?= base_url('pos/search_santri') ?>",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        placeholder: 'Cari nama atau NIS santri...',
        minimumInputLength: 2,
        allowClear: true,
        minimumResultsForSearch: 0 // Tampilkan pencarian meskipun pilihan sedikit
    }).on('select2:open', function () {
        setTimeout(function () {
            document.querySelector('.select2-search__field').focus();
        }, 100); // Delay supaya elemen muncul dulu
    }).on('select2:select', function (e) {
        var data = e.params.data;
        
        // Ambil info lengkap santri via AJAX
        $.post('<?= base_url("pos/get_santri_info") ?>', { santri_id: data.id }, function(response) {
            if (response.error) {
                alert(response.error);
                selectedSantri = null; // Reset jika ada error
                return;
            }
            
            // Simpan data santri yang dipilih ke variabel
            selectedSantri = response;
            
            $('#santri_info').show();
            $('#santri_nama').text(response.nama);
            $('#santri_nis').text(response.nomor_induk);
            $('#santri_kelas').text(response.kelas);

            // Format dan tampilkan saldo & limit
            const formatIDR = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);

            $('#santri_saldo').text(formatIDR(response.saldo_jajan)).addClass('font-weight-bold');
            $('#santri_pengeluaran').text(formatIDR(response.pengeluaran_hari_ini));
            $('#santri_sisa_limit').text(formatIDR(response.sisa_limit));

            // Beri warna pada sisa limit
            if (response.status_limit === 'habis') {
                $('#santri_sisa_limit').removeClass('text-info').addClass('text-danger font-weight-bold');
            } else {
                $('#santri_sisa_limit').removeClass('text-danger').addClass('text-info font-weight-bold');
            }

        }, 'json');
    });

    // Hapus info santri jika pilihan dikosongkan
    $('#santri_select').on('select2:unselect', function (e) {
        $('#santri_info').hide();
        selectedSantri = null; // Reset variabel saat pilihan dikosongkan
    });

    // Inisialisasi Select2 untuk menu
    $('#menu_select').select2({
        placeholder: 'Cari menu...',
        ajax: {
            url: '<?= base_url("pos/search_menu") ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    // Event ketika menu dipilih
    $('#menu_select').on('select2:select', function(e) {
        const data = e.params.data;
        selectedMenu = data;
        
        // Tampilkan info menu
        $('#menu_nama').text(data.nama_menu);
        $('#menu_pemilik').text(data.pemilik);
        $('#menu_harga').text('Rp ' + numberFormat(data.harga));
        $('#menu_stok').text(data.stok);
        
        $('#menu_info').show();
    });

    // Event tambah item ke keranjang
    $('#add_item').click(function() {
        if (!selectedSantri) {
            alert('Pilih santri terlebih dahulu!');
            return;
        }
        
        if (!selectedMenu) {
            alert('Pilih menu terlebih dahulu!');
            return;
        }
        
        const quantity = parseInt($('#quantity').val());
        if (quantity <= 0) {
            alert('Jumlah harus lebih dari 0!');
            return;
        }
        
        if (quantity > selectedMenu.stok) {
            alert('Stok tidak mencukupi!');
            return;
        }
        
        // Cek apakah menu sudah ada di keranjang
        const existingItem = cart.find(item => item.menu_id === selectedMenu.id);
        if (existingItem) {
            existingItem.quantity += quantity;
            existingItem.subtotal = existingItem.quantity * existingItem.harga;
        } else {
            cart.push({
                menu_id: selectedMenu.id,
                menu_name: selectedMenu.nama_menu,
                harga: selectedMenu.harga,
                quantity: quantity,
                subtotal: selectedMenu.harga * quantity
            });
        }
        
        updateCartDisplay();
        clearMenuSelection();
    });

    // Event kosongkan keranjang
    $('#clear_cart').click(function() {
        if (confirm('Yakin ingin mengosongkan keranjang?')) {
            cart = [];
            updateCartDisplay();
        }
    });

    // Event proses transaksi - LANGSUNG PROSES TANPA MODAL
    $('#process_transaction').click(function() {
        if (!selectedSantri) {
            alert('Pilih santri terlebih dahulu!');
            return;
        }
        
        if (cart.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }
        
        // Konfirmasi langsung tanpa modal
        const total = cart.reduce((sum, item) => sum + item.subtotal, 0);
        if (!confirm('Konfirmasi transaksi ini?\n\nSantri: ' + selectedSantri.nama + '\nTotal: Rp ' + numberFormat(total))) {
            return;
        }
        
        // Tampilkan loading
        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');
        
        // Proses transaksi langsung
        $.post('<?= base_url("pos/process_transaction") ?>', {
            santri_id: selectedSantri.id,
            items: JSON.stringify(cart)
        }, function(response) {
            if (response.success) {
                alert('Transaksi berhasil!\n\nTotal: Rp ' + numberFormat(total));
                // Reset form
                cart = [];
                updateCartDisplay();
                clearSantriSelection();
                loadRecentTransactions();
            } else {
                alert('Gagal: ' + response.message);
            }
        }, 'json').fail(function() {
            alert('Terjadi kesalahan saat memproses transaksi');
        }).always(function() {
            // Restore button
            btn.prop('disabled', false).html(originalText);
        });
    });

    // Fungsi untuk update tampilan keranjang
    function updateCartDisplay() {
        const tbody = $('#cart_items');
        tbody.empty();
        
        let total = 0;
        
        cart.forEach((item, index) => {
            tbody.append(`
                <tr>
                    <td>${item.menu_name}</td>
                    <td class="text-right">Rp ${numberFormat(item.harga)}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-right">Rp ${numberFormat(item.subtotal)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
            total += item.subtotal;
        });
        
        $('#total_amount').text('Rp ' + numberFormat(total));
    }

    // Fungsi untuk membersihkan pilihan menu
    function clearMenuSelection() {
        $('#menu_select').val(null).trigger('change');
        $('#menu_info').hide();
        selectedMenu = null;
    }

    // Fungsi untuk membersihkan pilihan santri
    function clearSantriSelection() {
        $('#santri_select').val(null).trigger('change');
        $('#santri_info').hide();
        selectedSantri = null;
    }

    // Fungsi untuk format angka
    function numberFormat(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Fungsi untuk menghapus item dari keranjang
    window.removeItem = function(index) {
        cart.splice(index, 1);
        updateCartDisplay();
    };

    // Load menu populer
    function loadPopularMenu() {
        $.get('<?= base_url("pos/search_menu") ?>', {q: ''}, function(data) {
            const popularMenu = $('#popular_menu');
            popularMenu.empty();
            
            data.slice(0, 6).forEach(menu => {
                popularMenu.append(`
                    <div class="col-6">
                        <div class="menu-card" onclick="addToCart(${menu.id}, '${menu.nama_menu}', ${menu.harga})">
                            <div class="menu-name">${menu.nama_menu}</div>
                            <div class="menu-price">Rp ${numberFormat(menu.harga)}</div>
                        </div>
                    </div>
                `);
            });
        }, 'json');
    }

    // Fungsi untuk menambah ke keranjang dari menu populer
    window.addToCart = function(menuId, menuName, harga) {
        if (!selectedSantri) {
            alert('Pilih santri terlebih dahulu!');
            return;
        }
        
        const existingItem = cart.find(item => item.menu_id === menuId);
        if (existingItem) {
            existingItem.quantity += 1;
            existingItem.subtotal = existingItem.quantity * existingItem.harga;
        } else {
            cart.push({
                menu_id: menuId,
                menu_name: menuName,
                harga: harga,
                quantity: 1,
                subtotal: harga
            });
        }
        
        updateCartDisplay();
    };

    // Load transaksi terakhir
    function loadRecentTransactions() {
        // Implementasi untuk load transaksi terakhir
    }

    // Load data awal
    loadPopularMenu();
    loadRecentTransactions();
});

function updateClock() {
    const now = new Date();
    const jam = now.getHours().toString().padStart(2, '0');
    const menit = now.getMinutes().toString().padStart(2, '0');
    const detik = now.getSeconds().toString().padStart(2, '0');
    document.getElementById('clock-time').textContent = jam + ':' + menit + ':' + detik;
}
setInterval(updateClock, 1000);
updateClock();
</script> 