<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Import Data Santri</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('santri') ?>">Data Santri</a></li>
                        <li class="breadcrumb-item active">Import CSV</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
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
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-upload mr-1"></i> Upload File CSV
                            </h6>
                        </div>
                        <div class="card-body py-3">
                            <form action="<?= base_url('santri/import') ?>" method="post" enctype="multipart/form-data">
                                <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                <div class="form-group">
                                    
                                    <input type="file" class="form-control-file" id="csv_file" name="csv_file" accept=".csv" required>
                                    <small class="form-text text-muted">
                                        Maksimal ukuran file: 2MB. Format yang didukung: .csv (delimiter titik koma ;)
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-upload mr-1"></i> Import Data
                                </button>
                                <a href="<?= base_url('santri') ?>" class="btn btn-secondary btn-sm ml-2">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-info">
                            <h6 class="m-0 font-weight-bold text-white">
                                <i class="fas fa-info-circle mr-1"></i> Instruksi & Format
                            </h6>
                        </div>
                        <div class="card-body py-3">
                            <p class="mb-2"><strong>Petunjuk Import Data Santri:</strong></p>
                            <ol class="mb-3 pl-3">
                                <li>Unduh template CSV dengan menekan tombol <b>Download Template CSV</b> di bawah.</li>
                                <li>Isi data santri sesuai urutan dan format kolom pada template.</li>
                                <li>Pastikan file disimpan dalam format <b>.csv</b> dengan delimiter titik koma (<b>;</b>).</li>
                                <li>Upload file CSV yang sudah diisi melalui form di samping kiri.</li>
                                <li>Ukuran file maksimal 2MB.</li>
                            </ol>
                            <div class="mb-2">
                                <b>Format kolom (wajib urut):</b><br>
                                <span class="text-primary">Nomor Induk;Nama;Angkatan;Jenis Kelamin (L/P)</span>
                            </div>
                            <div class="mb-2">
                                <b>Contoh data:</b>
                                <pre style="background:#f8f9fa;border:1px solid #ddd;padding:8px;">
Nomor Induk;Nama;Angkatan;Jenis Kelamin (L/P)
2024001;Ahmad Santri;2024;L
2024002;Fatimah Santri;2024;P
                                </pre>
                            </div>
                            <a href="<?= base_url('santri/download_template') ?>" class="btn btn-secondary btn-sm mt-2">
                                <i class="fas fa-file-download mr-1"></i> Download Template CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 