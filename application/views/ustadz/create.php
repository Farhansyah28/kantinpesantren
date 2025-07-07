<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-plus mr-2 text-primary"></i>Tambah Ustadz/Ustadzah
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('ustadz') ?>">Ustadz/Ustadzah</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit mr-1"></i> Form Tambah Ustadz/Ustadzah
                </h6>
            </div>
            <div class="card-body">
                <?= form_open('ustadz/create', ['class' => 'needs-validation', 'novalidate' => '', 'id' => 'form-ustadz']) ?>
                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
                    
                    <div class="form-group">
                        <label for="nama" class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= form_error('nama') ? 'is-invalid' : '' ?>" 
                               id="nama" name="nama" value="<?= set_value('nama') ?>" 
                               placeholder="Masukkan nama lengkap" required minlength="3" maxlength="100"
                               pattern="[A-Za-z\s\.]+" title="Hanya huruf, spasi, dan titik diperbolehkan">
                        <?= form_error('nama', '<div class="invalid-feedback">', '</div>') ?>
                        <small class="form-text text-muted">Minimal 3 karakter, maksimal 100 karakter. Hanya huruf, spasi, dan titik.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="nomor_telepon" class="font-weight-bold">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control <?= form_error('nomor_telepon') ? 'is-invalid' : '' ?>" 
                               id="nomor_telepon" name="nomor_telepon" value="<?= set_value('nomor_telepon') ?>" 
                               placeholder="Contoh: 081234567890" required minlength="10" maxlength="15"
                               pattern="[0-9+\-\s()]+" title="Hanya angka, +, -, spasi, dan tanda kurung diperbolehkan">
                        <?= form_error('nomor_telepon', '<div class="invalid-feedback">', '</div>') ?>
                        <small class="form-text text-muted">Minimal 10 digit, maksimal 15 digit. Format: 081234567890 atau +62-812-3456-7890</small>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <a href="<?= site_url('ustadz') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Custom validation untuk form ustadz
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-ustadz');
    const namaInput = document.getElementById('nama');
    const teleponInput = document.getElementById('nomor_telepon');
    const submitBtn = document.getElementById('btn-submit');
    
    // Validasi nama
    namaInput.addEventListener('input', function() {
        const value = this.value.trim();
        const pattern = /^[A-Za-z\s\.]+$/;
        
        if (value.length < 3) {
            this.setCustomValidity('Nama minimal 3 karakter');
        } else if (value.length > 100) {
            this.setCustomValidity('Nama maksimal 100 karakter');
        } else if (!pattern.test(value)) {
            this.setCustomValidity('Hanya huruf, spasi, dan titik diperbolehkan');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Validasi nomor telepon
    teleponInput.addEventListener('input', function() {
        const value = this.value.trim();
        const pattern = /^[0-9+\-\s()]+$/;
        
        if (value.length < 10) {
            this.setCustomValidity('Nomor telepon minimal 10 digit');
        } else if (value.length > 15) {
            this.setCustomValidity('Nomor telepon maksimal 15 digit');
        } else if (!pattern.test(value)) {
            this.setCustomValidity('Hanya angka, +, -, spasi, dan tanda kurung diperbolehkan');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Prevent double submission
    form.addEventListener('submit', function(e) {
        if (form.checkValidity()) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
        }
    });
    
    // Sanitasi input real-time
    namaInput.addEventListener('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        const pattern = /[A-Za-z\s\.]/;
        if (!pattern.test(char)) {
            e.preventDefault();
        }
    });
    
    teleponInput.addEventListener('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        const pattern = /[0-9+\-\s()]/;
        if (!pattern.test(char)) {
            e.preventDefault();
        }
    });
});
</script> 