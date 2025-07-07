<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-random mr-2"></i>Transfer Antar Kategori
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('tabungan') ?>">Tabungan</a></li>
        <li class="breadcrumb-item active">Transfer Antar Kategori</li>
    </ol>
</div>

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
            <div class="card-header py-2 bg-gradient-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-random mr-1"></i> Form Transfer Antar Kategori
                </h6>
            </div>
            <div class="card-body py-3">
                <?php echo form_open('tabungan/transfer_kategori'); ?>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="santri_id" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Pilih Santri <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control form-control-sm select2" id="santri_id" name="santri_id" style="width: 100%;" required>
                                <option value="">Pilih Santri...</option>
                                <?php foreach($santri as $s): ?>
                                    <option value="<?= $s->id ?>" 
                                            data-saldo-tabungan="<?= $s->saldo_tabungan ?>"
                                            data-saldo-jajan="<?= $s->saldo_jajan ?>">
                                        <?= $s->nama ?> (<?= $s->nomor_induk ?>) - <?= $s->kelas ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error('santri_id', '<small class="text-danger">', '</small>') ?>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="dari_kategori" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Dari Kategori <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select name="dari_kategori" id="dari_kategori" class="form-control form-control-sm" required>
                                <option value="">Pilih Kategori Asal</option>
                                <option value="tabungan">Tabungan</option>
                                <option value="jajan">Jajan</option>
                            </select>
                            <?= form_error('dari_kategori', '<small class="text-danger">', '</small>') ?>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="ke_kategori" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Ke Kategori <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select name="ke_kategori" id="ke_kategori" class="form-control form-control-sm" required>
                                <option value="">Pilih Kategori Tujuan</option>
                                <option value="tabungan">Tabungan</option>
                                <option value="jajan">Jajan</option>
                            </select>
                            <?= form_error('ke_kategori', '<small class="text-danger">', '</small>') ?>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="jumlah" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Jumlah Transfer <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="jumlah" id="jumlah" class="form-control form-control-sm rupiah-input" required placeholder="Rp 0">
                            </div>
                            <?= form_error('jumlah', '<small class="text-danger">', '</small>') ?>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary btn-sm mr-2">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="<?php echo site_url('tabungan'); ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <div class="alert alert-info" id="saldo-info">
                                <i class="fas fa-info-circle"></i> Pilih santri dan kategori asal untuk melihat saldo
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" id="saldo-detail" style="display: none;">
                        <div class="col-sm-12">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Informasi Saldo</div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Saldo Tabungan</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Rp</span>
                                                            </div>
                                                            <input type="text" class="form-control" id="saldo-tabungan" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Saldo Jajan</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Rp</span>
                                                            </div>
                                                            <input type="text" class="form-control" id="saldo-jajan" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Total Saldo</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Rp</span>
                                                            </div>
                                                            <input type="text" class="form-control" id="total-saldo" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-2 bg-gradient-info">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-info-circle mr-1"></i> Informasi
                </h6>
            </div>
            <div class="card-body py-3">
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-lightbulb mr-1"></i>Petunjuk:</h6>
                    <ul class="mb-0 small">
                        <li>Pilih santri yang akan ditransfer</li>
                        <li>Pilih kategori asal dan tujuan</li>
                        <li>Masukkan jumlah transfer</li>
                        <li>Saldo akan berpindah antar kategori</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Auto-hide flash messages after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
    
    function updateSaldoInfo() {
        var selected = $('#santri_id option:selected');
        var dariKategori = $('#dari_kategori').val();
        
        if (selected.val() && dariKategori) {
            var saldoTabungan = parseInt(selected.data('saldo-tabungan')) || 0;
            var saldoJajan = parseInt(selected.data('saldo-jajan')) || 0;
            var totalSaldo = saldoTabungan + saldoJajan;
            
            $('#saldo-info').hide();
            $('#saldo-detail').show();
            $('#saldo-tabungan').val(formatRupiah(saldoTabungan));
            $('#saldo-jajan').val(formatRupiah(saldoJajan));
            $('#total-saldo').val(formatRupiah(totalSaldo));
            
            // Set max amount untuk input jumlah
            var maxAmount = dariKategori === 'tabungan' ? saldoTabungan : saldoJajan;
            $('#jumlah').attr('max', maxAmount);
        } else {
            $('#saldo-info').show();
            $('#saldo-detail').hide();
        }
    }
    
    $('#santri_id, #dari_kategori').change(updateSaldoInfo);
    
    // Validasi kategori tujuan tidak boleh sama dengan kategori asal
    $('#ke_kategori').change(function() {
        var dariKategori = $('#dari_kategori').val();
        var keKategori = $(this).val();
        
        if (dariKategori && keKategori && dariKategori == keKategori) {
            alert('Kategori tujuan tidak boleh sama dengan kategori asal');
            $(this).val('').trigger('change');
        }
    });
});
</script> 