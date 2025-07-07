<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-upload mr-2 text-primary"></i>Import Data Ustadz/Ustadzah
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('ustadz') ?>">Ustadz/Ustadzah</a></li>
        <li class="breadcrumb-item active">Import</li>
    </ol>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-file-csv mr-1"></i> Upload File CSV
                </h6>
            </div>
            <div class="card-body">
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-1"></i>
                        <?= $this->session->flashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>

                    <div class="form-group">
                        <label for="csv_file" class="font-weight-bold">
                            <i class="fas fa-file-upload mr-1"></i>Pilih File CSV <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control-file" id="csv_file" name="csv_file"
                            accept=".csv" required>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Format file: CSV dengan separator semicolon (;). Maksimal ukuran file: 5MB.
                        </small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            <i class="fas fa-upload mr-1"></i> Import Data
                        </button>
                        <a href="<?= site_url('ustadz/download_template') ?>" class="btn btn-success ml-2">
                            <i class="fas fa-download mr-1"></i> Download Template
                        </a>
                        <a href="<?= site_url('ustadz/export_csv') ?>" class="btn btn-info ml-2">
                            <i class="fas fa-download mr-1"></i> Export Data
                        </a>
                        <a href="<?= site_url('ustadz') ?>" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Format CSV Info -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-1"></i> Informasi Format CSV
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">Format Kolom:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Kolom 1:</strong> Nama Lengkap (3-100 karakter)</li>
                            <li><strong>Kolom 2:</strong> Nomor Telepon (10-15 digit)</li>
                        </ul>

                        <h6 class="font-weight-bold text-primary mt-3">Contoh Format:</h6>
                        <div class="bg-light p-3 rounded">
                            <code>
                                Nama Lengkap;Nomor Telepon<br>
                                Ust. Ahmad;081234567890<br>
                                Usth. Fatimah;081234567891<br>
                                Ust. Muhammad;+62-812-3456-7892
                            </code>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-warning">Aturan Validasi:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-1"></i> Nama minimal 3 karakter, maksimal 100 karakter</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Nama hanya boleh huruf, spasi, dan titik</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Nomor telepon minimal 10 digit, maksimal 15 digit</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Nomor telepon boleh angka, +, -, spasi, dan tanda kurung</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Nama ustadz/ustadzah harus unik</li>
                        </ul>

                        <h6 class="font-weight-bold text-info mt-3">Tips:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-lightbulb text-warning mr-1"></i> Gunakan template yang disediakan</li>
                            <li><i class="fas fa-lightbulb text-warning mr-1"></i> Pastikan format nomor telepon konsisten</li>
                            <li><i class="fas fa-lightbulb text-warning mr-1"></i> Periksa data sebelum upload</li>
                        </ul>
                    </div>
                </div>
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
                    } else {
                        // Disable submit button to prevent double submission
                        var submitBtn = document.getElementById('btn-submit');
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengimport...';
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    // File validation
    document.getElementById('csv_file').addEventListener('change', function(e) {
        var file = e.target.files[0];
        var submitBtn = document.getElementById('btn-submit');

        if (file) {
            // Check file extension
            var fileName = file.name.toLowerCase();
            if (!fileName.endsWith('.csv')) {
                alert('File harus berformat .csv');
                this.value = '';
                submitBtn.disabled = true;
                return;
            }

            // Check file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 5MB.');
                this.value = '';
                submitBtn.disabled = true;
                return;
            }

            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    });
</script>