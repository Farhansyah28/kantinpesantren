<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="w-100 text-center mb-2">
    <small class="text-muted" style="font-size:1rem"><i class="fas fa-clock mr-1"></i><span id="live-clock"></span></small>
</div>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-cash-register mr-2"></i>Point of Sale (POS) Modern
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">POS Modern</li>
    </ol>
</div>

<style>
    .menu-card {
        transition: transform 0.15s cubic-bezier(.4, 2, .6, 1), box-shadow 0.15s;
    }

    .menu-card.animated {
        transform: scale(1.07);
        box-shadow: 0 0 20px 0 rgba(0, 123, 255, 0.15);
        z-index: 2;
    }

    .menu-card:hover {
        box-shadow: 0 0 10px 0 rgba(0, 123, 255, 0.10);
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 font-weight-bold text-primary">Pilih Pelanggan</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="customer-type" class="font-weight-bold">Jenis Pelanggan:</label>
                        <select class="form-control" id="customer-type" name="customer_type">
                            <option value="santri">Santri</option>
                            <option value="ustadz">Ustadz/Ustadzah</option>
                        </select>
                    </div>

                    <div id="santri-section">
                        <div class="form-group mb-0">
                            <label for="santri-select" class="font-weight-bold">Pilih Santri:</label>
                            <select class="form-control" id="santri-select" name="santri_id" style="width:100%">
                                <option></option>
                                <?php foreach ($santri as $s): ?>
                                    <option value="<?= $s->id ?>">
                                        <?= htmlspecialchars($s->nama) ?> (<?= htmlspecialchars($s->nomor_induk) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="santri-info" class="mt-2" style="display:none">
                            <div><b>Saldo Jajan:</b> <span id="saldo-jajan" class="text-success"></span></div>
                            <div><b>Pengeluaran Hari Ini:</b> <span id="pengeluaran-hari-ini"></span></div>
                            <div><b>Sisa Limit Harian (Rp 12.000):</b> <span id="sisa-limit" class="text-info font-weight-bold"></span></div>
                        </div>
                    </div>

                    <div id="ustadz-section" style="display:none">
                        <div class="form-group mb-0">
                            <label for="ustadz-select" class="font-weight-bold">Pilih Ustadz/Ustadzah:</label>
                            <select class="form-control" id="ustadz-select" name="ustadz_id" style="width:100%">
                                <option></option>
                                <?php if (isset($ustadz)): foreach ($ustadz as $u): ?>
                                        <option value="<?= $u->id ?>">
                                            <?= htmlspecialchars($u->nama) ?> (<?= htmlspecialchars($u->nomor_telepon) ?>)
                                        </option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                        <div id="ustadz-info" class="mt-2" style="display:none">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i>
                                <b>Pembayaran Tunai</b> - Ustadz/Ustadzah hanya dapat melakukan pembayaran tunai
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 font-weight-bold text-primary">Pilih Menu</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <input type="text" class="form-control" id="menu-search" placeholder="Cari menu...">
                    </div>
                    <div class="row" id="menu-list">
                        <?php
                        $max_menu = 6;
                        $count = 0;
                        foreach ($menu as $m):
                            if ($count++ >= $max_menu) break;
                        ?>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 text-center h-100 d-flex flex-column justify-content-between menu-card" style="cursor:pointer" onclick="addToCart('<?= htmlspecialchars($m->nama_menu) ?>', <?= (int)$m->harga_jual ?>, <?= (int)$m->id ?>)">
                                    <div>
                                        <div class="font-weight-bold mb-2"><?= htmlspecialchars($m->nama_menu) ?></div>
                                        <div class="text-primary font-weight-bold mb-2">Rp <?= number_format($m->harga_jual, 0, ',', '.') ?></div>
                                        <div class="mb-2">Stok: <?= (int)$m->stok ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="menu-warning" class="col-12 text-center text-muted" style="display:none"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 font-weight-bold text-primary">Ringkasan Pembelian</h5>
                </div>
                <div class="card-body">
                    <div id="cart-empty" class="text-muted mb-3">Belum ada item dipilih</div>
                    <ul class="list-group mb-3 d-none" id="cart-list"></ul>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="font-weight-bold">Total:</span>
                        <span class="font-weight-bold" id="cart-total">Rp 0</span>
                    </div>
                    <div class="form-group mt-3">
                        <label for="metode-pembayaran" class="font-weight-bold">Metode Pembayaran:</label>
                        <select class="form-control" id="metode-pembayaran" name="metode_pembayaran">
                            <option value="saldo_jajan" selected>Saldo Jajan</option>
                        </select>
                    </div>
                    <button id="process_transaction" class="btn btn-success btn-block mt-3">Proses Transaksi</button>
                    <button class="btn btn-secondary btn-block" id="btn-reset">Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];

    // Toggle customer type
    $('#customer-type').change(function() {
        const customerType = $(this).val();

        if (customerType === 'santri') {
            $('#santri-section').show();
            $('#ustadz-section').hide();
            $('#metode-pembayaran').html('<option value="saldo_jajan" selected>Saldo Jajan</option>');
            $('#menu-list').show();
            $('#menu-warning').hide();
        } else if (customerType === 'ustadz') {
            $('#santri-section').hide();
            $('#ustadz-section').show();
            $('#metode-pembayaran').html('<option value="tunai" selected>Tunai</option>');
            if ($('#ustadz-select').val()) {
                $('#ustadz-info').show();
                $('#menu-list').show();
                $('#menu-warning').hide();
            } else {
                $('#ustadz-info').hide();
                $('#menu-list').hide();
                $('#menu-warning').text('Pilih ustadz/ustadzah untuk melihat menu').show();
            }
        }
    });

    // Initialize Select2 for ustadz
    $(document).ready(function() {
        $('#ustadz-select').select2({
            placeholder: 'Pilih Ustadz/Ustadzah...',
            allowClear: true
        });

        // Show ustadz info when selected
        $('#ustadz-select').change(function() {
            if ($(this).val()) {
                $('#ustadz-info').show();
                $('#menu-list').show();
                $('#menu-warning').hide();
            } else {
                $('#ustadz-info').hide();
                $('#menu-list').hide();
                $('#menu-warning').text('Pilih ustadz/ustadzah untuk melihat menu').show();
            }
        });
    });

    function addToCart(nama, harga, menu_id) {
        // Animasi kartu
        const cards = document.querySelectorAll('.menu-card');
        cards.forEach(card => {
            if (card.onclick && card.onclick.toString().includes(nama)) {
                card.classList.add('animated');
                setTimeout(() => card.classList.remove('animated'), 200);
            }
        });

        // Pastikan menu_id ada, jika tidak gunakan 0 sebagai fallback
        if (typeof menu_id === 'undefined' || menu_id === null) {
            menu_id = 0;
        }

        const idx = cart.findIndex(item => item.menu_name === nama);
        if (idx > -1) {
            cart[idx].quantity += 1;
        } else {
            cart.push({
                menu_id: menu_id,
                menu_name: nama,
                harga: harga,
                quantity: 1
            });
        }
        renderCart();
    }

    function renderCart() {
        const cartList = document.getElementById('cart-list');
        const cartEmpty = document.getElementById('cart-empty');
        const cartTotal = document.getElementById('cart-total');
        cartList.innerHTML = '';
        let total = 0;
        if (cart.length === 0) {
            cartList.classList.add('d-none');
            cartEmpty.classList.remove('d-none');
            cartTotal.textContent = 'Rp 0';
            return;
        }
        cartList.classList.remove('d-none');
        cartEmpty.classList.add('d-none');
        cart.forEach((item, i) => {
            total += item.harga * item.quantity;
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `
            <span>${item.menu_name} <span class="badge badge-primary badge-pill ml-2">x${item.quantity}</span></span>
            <span>Rp ${(item.harga * item.quantity).toLocaleString('id-ID')}</span>
            <button class="btn btn-sm btn-danger ml-2" onclick="removeFromCart(${i})">&times;</button>
        `;
            cartList.appendChild(li);
        });
        cartTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function removeFromCart(idx) {
        if (cart[idx].quantity > 1) {
            cart[idx].quantity -= 1; // Kurangi quantity 1
        } else {
            cart.splice(idx, 1); // Hapus item jika quantity sudah 1
        }
        renderCart();
    }

    document.getElementById('btn-reset').onclick = function() {
        cart = [];
        renderCart();
    };

    $('#process_transaction').click(function() {
        const customerType = $('#customer-type').val();
        const metode = $('#metode-pembayaran').val();

        if (customerType === 'santri') {
            var santriId = $('#santri-select').val();
            if (!santriId) {
                alert('Pilih santri terlebih dahulu!');
                return;
            }
            if (cart.length === 0) {
                alert('Keranjang masih kosong!');
                return;
            }
            // Ambil info saldo & limit dari tampilan
            var sisaLimit = parseInt($('#sisa-limit').text().replace(/[^\d]/g, ''));
            var saldoJajan = parseInt($('#saldo-jajan').text().replace(/[^\d]/g, ''));
            var total = cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
            if (metode === 'saldo_jajan') {
                if (saldoJajan < total) {
                    alert('Saldo jajan tidak mencukupi!');
                    return;
                }
                if (sisaLimit < total) {
                    alert('Transaksi melebihi limit harian!');
                    return;
                }
            }
            // Proses transaksi santri
            processSantriTransaction(santriId, total);
        } else if (customerType === 'ustadz') {
            var ustadzId = $('#ustadz-select').val();
            if (!ustadzId) {
                alert('Pilih ustadz/ustadzah terlebih dahulu!');
                return;
            }
            if (cart.length === 0) {
                alert('Keranjang masih kosong!');
                return;
            }
            var total = cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
            // Proses transaksi ustadz
            processUstadzTransaction(ustadzId, total);
        }
    });

    function processSantriTransaction(santriId, total) {
        console.log('POS Modern - Processing santri transaction:', {
            santriId: santriId,
            cart: cart,
            total: total,
            metode: $('#metode-pembayaran').val()
        });

        $.ajax({
            url: '<?= site_url("pos/process_transaction") ?>',
            type: 'POST',
            data: {
                santri_id: santriId,
                cart: JSON.stringify(cart),
                metode_pembayaran: $('#metode-pembayaran').val()
            },
            dataType: 'json',
            success: function(response) {
                console.log('POS Modern - Transaction response:', response);
                if (response.success) {
                    alert('Transaksi berhasil!');
                    cart = [];
                    renderCart();
                    $('#santri-select').val('').trigger('change');
                    $('#santri-info').hide();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('POS Modern - Transaction error:', {
                    xhr: xhr,
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                alert('Terjadi kesalahan sistem. Silakan coba lagi.');
            }
        });
    }

    function processUstadzTransaction(ustadzId, total) {
        console.log('POS Modern - Processing ustadz transaction:', {
            ustadzId: ustadzId,
            cart: cart,
            total: total
        });

        $.ajax({
            url: '<?= site_url("pos/process_ustadz_transaction") ?>',
            type: 'POST',
            data: {
                ustadz_id: ustadzId,
                cart: JSON.stringify(cart),
                metode_pembayaran: 'tunai'
            },
            dataType: 'json',
            success: function(response) {
                console.log('POS Modern - Ustadz transaction response:', response);
                if (response.success) {
                    alert('Transaksi ustadz/ustadzah berhasil!');
                    cart = [];
                    renderCart();
                    $('#ustadz-select').val('').trigger('change');
                    $('#ustadz-info').hide();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('POS Modern - Ustadz transaction error:', {
                    xhr: xhr,
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                alert('Terjadi kesalahan sistem. Silakan coba lagi.');
            }
        });
    }

    $(document).ready(function() {
        $('#santri-select').select2({
            placeholder: 'Pilih santri...',
            allowClear: true,
            minimumResultsForSearch: 0
        });

        $('#santri-select').on('select2:open', function() {
            setTimeout(function() {
                document.querySelector('.select2-search__field').focus();
            }, 100);
        });

        $('#santri-select').on('change', function() {
            var santriId = $(this).val();
            console.log('POS Modern - Santri selected:', santriId);

            if (!santriId) {
                $('#santri-info').hide();
                $('#menu-list').html('<div class="col-12 text-center text-muted">Pilih santri untuk melihat menu</div>');
                return;
            }

            // Tampilkan loading state
            $('#santri-info').hide();
            $('#menu-list').html('<div class="col-12 text-center text-muted">Memuat data santri...</div>');

            console.log('POS Modern - Sending request to:', '<?= site_url('pos/get_santri_info') ?>');

            $.post('<?= site_url('pos/get_santri_info') ?>', {
                santri_id: santriId
            }, function(res) {
                console.log('POS Modern - Santri info response:', res);

                if (res.error) {
                    $('#santri-info').hide();
                    $('#menu-list').html('<div class="col-12 text-center text-danger">Error: ' + res.error + '</div>');
                    return;
                }

                function formatIDR(num) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(num);
                }

                $('#saldo-jajan').text(formatIDR(res.saldo_jajan));
                $('#pengeluaran-hari-ini').text(formatIDR(res.pengeluaran_hari_ini));
                $('#sisa-limit').text(formatIDR(res.sisa_limit));

                if (res.sisa_limit <= 0) {
                    $('#sisa-limit').removeClass('text-info').addClass('text-danger font-weight-bold');
                } else {
                    $('#sisa-limit').removeClass('text-danger').addClass('text-info font-weight-bold');
                }

                $('#santri-info').show();

                // Ambil menu sesuai gender
                if (res.jenis_kelamin) {
                    console.log('POS Modern - Loading menu for gender:', res.jenis_kelamin);
                    $('#menu-list').html('<div class="col-12 text-center text-muted">Memuat menu...</div>');

                    $.post('<?= site_url('pos/get_menu_by_gender') ?>', {
                        gender: res.jenis_kelamin
                    }, function(menuRes) {
                        console.log('POS Modern - Menu response:', menuRes);

                        if (menuRes.success) {
                            renderMenuList(menuRes.menu);
                        } else {
                            $('#menu-list').html('<div class="col-12 text-center text-danger">Error: ' + (menuRes.message || 'Unknown error') + '</div>');
                        }
                    }, 'json').fail(function(xhr, status, error) {
                        console.error('POS Modern - Menu request failed:', xhr, status, error);
                        $('#menu-list').html('<div class="col-12 text-center text-danger">Gagal memuat menu</div>');
                    });
                } else {
                    $('#menu-list').html('<div class="col-12 text-center text-muted">Jenis kelamin tidak tersedia</div>');
                }
            }, 'json').fail(function(xhr, status, error) {
                console.error('POS Modern - Santri info request failed:', xhr, status, error);
                console.error('POS Modern - Response text:', xhr.responseText);
                $('#santri-info').hide();
                $('#menu-list').html('<div class="col-12 text-center text-danger">Gagal memuat data santri</div>');
            });
        });
    });

    function renderMenuList(menuArr) {
        let html = '';
        if (!menuArr || menuArr.length === 0) {
            html = '<div class="col-12 text-center text-muted">Menu tidak tersedia</div>';
        } else {
            menuArr.forEach(function(m) {
                let labelKantin = '';
                if (m.nama_kantin) {
                    labelKantin = `<div class=\"small text-secondary\">Kantin: ${m.nama_kantin}</div>`;
                }
                html += `<div class=\"col-md-4 mb-3\">
        <div class=\"border rounded p-3 text-center h-100 d-flex flex-column justify-content-between menu-card\" style=\"cursor:pointer\" onclick=\"addToCart('${m.nama_menu.replace(/'/g, "\\'")}', ${m.harga_jual}, ${m.id})\">
          <div>
            <div class=\"font-weight-bold mb-2\">${m.nama_menu}</div>
            <div class=\"text-primary font-weight-bold mb-2\">Rp ${Number(m.harga_jual).toLocaleString('id-ID')}</div>
            <div class=\"mb-2\">Stok: ${m.stok}</div>
            ${labelKantin}
          </div>
        </div>
      </div>`;
            });
        }
        $('#menu-list').html(html);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Deteksi mobile (lebar layar <= 768px)
        if (window.innerWidth <= 768) {
            var sidebar = document.getElementById('accordionSidebar');
            if (sidebar && !sidebar.classList.contains('toggled')) {
                document.body.classList.add('sidebar-toggled');
                sidebar.classList.add('toggled');
            }
        }
    });

    document.getElementById('menu-search').addEventListener('input', function() {
        var keyword = this.value.toLowerCase();
        var cards = document.querySelectorAll('#menu-list .menu-card');
        cards.forEach(function(card) {
            var nama = card.querySelector('.font-weight-bold').textContent.toLowerCase();
            if (nama.includes(keyword)) {
                card.parentElement.style.display = '';
            } else {
                card.parentElement.style.display = 'none';
            }
        });
    });

    // Tambahkan live clock
    function updateClock() {
        const now = new Date();
        let h = now.getHours();
        let m = now.getMinutes();
        let s = now.getSeconds();
        if (h < 10) h = '0' + h;
        if (m < 10) m = '0' + m;
        if (s < 10) s = '0' + s;
        document.getElementById('live-clock').textContent = h + ':' + m + ':' + s;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>