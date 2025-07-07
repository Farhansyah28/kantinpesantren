<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-plus-circle mr-2"></i>Tambah Stok Menu
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('menu') ?>">Menu</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('menu/stok_management') ?>">Manajemen Stok</a></li>
        <li class="breadcrumb-item active">Tambah Stok</li>
    </ol>
</div>

<!-- Flash Messages -->
<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-gradient-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-boxes mr-1"></i> Form Tambah Stok
                </h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url('menu/tambah_stok') ?>" method="post" id="form-tambah-stok">
                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                    <input type="hidden" name="menu_id" value="<?= $menu->id ?>">
                    
                    <!-- Info Menu -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700">Nama Menu</label>
                                <input type="text" class="form-control" value="<?= $menu->nama_menu ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700">Pemilik</label>
                                <input type="text" class="form-control" value="<?= $menu->pemilik ?>" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700">Stok Saat Ini</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-center font-weight-bold" 
                                           value="<?= $menu->stok ?>" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fas fa-box"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700">Harga Jual</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control text-right" 
                                           value="<?= number_format($menu->harga_jual, 0, ',', '.') ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Form Input -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumlah_tambah" class="font-weight-bold text-gray-700">
                                    Jumlah yang Ditambahkan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control <?= form_error('jumlah_tambah') ? 'is-invalid' : '' ?>" 
                                           id="jumlah_tambah" 
                                           name="jumlah_tambah" 
                                           min="1" 
                                           value="<?= set_value('jumlah_tambah', 1, TRUE) ?>" 
                                           required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                    </div>
                                    <?= form_error('jumlah_tambah', '<div class="invalid-feedback">', '</div>') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga_beli_baru" class="font-weight-bold text-gray-700">
                                    Harga Beli per Unit <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" 
                                           class="form-control rupiah-input <?= form_error('harga_beli_baru') ? 'is-invalid' : '' ?>" 
                                           id="harga_beli_baru" 
                                           name="harga_beli_baru" 
                                           value="<?= set_value('harga_beli_baru', 'Rp ' . number_format($menu->harga_beli, 0, ',', '.'), TRUE) ?>" 
                                           required
                                           placeholder="Rp 0">
                                    <?= form_error('harga_beli_baru', '<div class="invalid-feedback">', '</div>') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="keterangan" class="font-weight-bold text-gray-700">
                            Keterangan <span class="text-muted">(Opsional)</span>
                        </label>
                        <textarea class="form-control" 
                                  id="keterangan" 
                                  name="keterangan" 
                                  rows="3" 
                                  placeholder="Contoh: Pembelian dari supplier ABC, stok untuk persiapan ramadhan, dll..."><?= set_value('keterangan', '', TRUE) ?></textarea>
                    </div>
                    
                    <!-- Preview Total -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-left-info">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Investasi
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-investasi">
                                                Rp 0
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Stok Setelah Penambahan
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stok-setelah">
                                                <?= $menu->stok ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?= base_url('menu/stok_management') ?>" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save mr-1"></i> Simpan Stok
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Info Kantin -->
        <div class="card shadow mb-4">
            <div class="card-header bg-gradient-info">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-store mr-1"></i> Info Kantin
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-store fa-3x text-info"></i>
                </div>
                <h5 class="text-center font-weight-bold text-gray-800">
                    <?= isset($kantin_info->nama) ? html_escape($kantin_info->nama) : 'Semua Kantin' ?>
                </h5>
                <p class="text-center text-muted mb-0">
                    <?= isset($kantin_info->jenis) ? ($kantin_info->jenis == 'putra' ? 'Kantin Putra' : 'Kantin Putri') : 'Akses Semua Kantin' ?>
                </p>
            </div>
        </div>
        
        <!-- Tips -->
        <div class="card shadow">
            <div class="card-header bg-gradient-warning">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-lightbulb mr-1"></i> Tips
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Pastikan harga beli akurat untuk perhitungan profit
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Isi keterangan untuk tracking yang lebih baik
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Periksa stok secara berkala
                    </li>
                    <li>
                        <i class="fas fa-check text-success mr-2"></i>
                        Update stok sebelum jam kantin dimulai
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide flash messages after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
    
    // Calculate total investment and new stock
    function calculateTotal() {
        var jumlah = parseInt(document.getElementById('jumlah_tambah').value) || 0;
        var hargaBeli = unformatRupiah(document.getElementById('harga_beli_baru').value) || 0;
        var stokSekarang = <?= $menu->stok ?>;
        
        var totalInvestasi = jumlah * hargaBeli;
        var stokSetelah = stokSekarang + jumlah;
        
        document.getElementById('total-investasi').textContent = 'Rp ' + totalInvestasi.toLocaleString('id-ID');
        document.getElementById('stok-setelah').textContent = stokSetelah;
    }
    
    // Calculate on input change
    document.getElementById('jumlah_tambah').addEventListener('input', calculateTotal);
    document.getElementById('harga_beli_baru').addEventListener('input', calculateTotal);
    
    // Initial calculation
    calculateTotal();
    
    // Form validation
    document.getElementById('form-tambah-stok').addEventListener('submit', function(e) {
        var jumlah = parseInt(document.getElementById('jumlah_tambah').value) || 0;
        var hargaBeli = unformatRupiah(document.getElementById('harga_beli_baru').value) || 0;
        
        if (jumlah <= 0) {
            e.preventDefault();
            alert('Jumlah harus lebih dari 0!');
            return false;
        }
        
        if (hargaBeli <= 0) {
            e.preventDefault();
            alert('Harga beli harus lebih dari 0!');
            return false;
        }
        
        return true;
    });
});
</script> 