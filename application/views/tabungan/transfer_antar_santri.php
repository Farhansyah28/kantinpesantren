<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-exchange-alt mr-2"></i>Transfer Antar Santri
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('tabungan') ?>">Tabungan</a></li>
        <li class="breadcrumb-item active">Transfer Antar Santri</li>
    </ol>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
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
                    <i class="fas fa-exchange-alt mr-1"></i> Form Transfer Antar Santri
                </h6>
            </div>
            <div class="card-body py-3">
                <?php if (empty($santri)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Belum ada data santri yang tersedia untuk transfer.
                    </div>
                <?php else: ?>
                    <form method="POST" action="<?= site_url('tabungan/transfer_antar_santri') ?>" id="transferForm">
                        <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                        <div class="form-group row mb-2 align-items-center">
                            <label for="santri_pengirim_id" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                                Santri Pengirim <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select name="santri_pengirim_id" id="santri_pengirim_id" class="form-control form-control-sm select2" required>
                                    <option value="">Pilih Santri Pengirim</option>
                                    <?php foreach ($santri as $s): ?>
                                        <option value="<?= $s->id ?>"
                                            data-saldo-tabungan="<?= $s->saldo_tabungan ?? 0 ?>"
                                            data-saldo-jajan="<?= $s->saldo_jajan ?? 0 ?>">
                                            <?= $s->nomor_induk ?> - <?= $s->nama ?> (Tabungan: Rp <?= number_format($s->saldo_tabungan ?? 0, 0, ',', '.') ?>, Jajan: Rp <?= number_format($s->saldo_jajan ?? 0, 0, ',', '.') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('santri_pengirim_id', '<small class="text-danger">', '</small>') ?>
                            </div>
                        </div>
                        <div class="form-group row mb-2 align-items-center">
                            <label for="kategori" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                                Kategori Transfer <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select name="kategori" id="kategori" class="form-control form-control-sm" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="tabungan">Tabungan</option>
                                    <option value="jajan">Jajan</option>
                                </select>
                                <?= form_error('kategori', '<small class="text-danger">', '</small>') ?>
                            </div>
                        </div>
                        <div class="form-group row mb-2 align-items-center">
                            <label for="santri_penerima_id" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                                Santri Penerima <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <select name="santri_penerima_id" id="santri_penerima_id" class="form-control form-control-sm select2" required>
                                    <option value="">Pilih Santri Penerima</option>
                                    <?php foreach ($santri as $s): ?>
                                        <option value="<?= $s->id ?>"
                                            data-saldo-tabungan="<?= $s->saldo_tabungan ?? 0 ?>"
                                            data-saldo-jajan="<?= $s->saldo_jajan ?? 0 ?>">
                                            <?= $s->nomor_induk ?> - <?= $s->nama ?> (Tabungan: Rp <?= number_format($s->saldo_tabungan ?? 0, 0, ',', '.') ?>, Jajan: Rp <?= number_format($s->saldo_jajan ?? 0, 0, ',', '.') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('santri_penerima_id', '<small class="text-danger">', '</small>') ?>
                                <small class="text-muted">Saldo penerima: <span id="saldo-penerima">Rp 0</span></small>
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
                                <small class="text-muted">Saldo tersedia: <span id="saldo-tersedia">Rp 0</span></small>
                            </div>
                        </div>
                        <div class="form-group row mb-2 align-items-center">
                            <label for="keterangan" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                                Keterangan
                            </label>
                            <div class="col-sm-9">
                                <textarea name="keterangan" id="keterangan" class="form-control form-control-sm" rows="2" placeholder="Masukkan keterangan transfer..."></textarea>
                                <?= form_error('keterangan', '<small class="text-danger">', '</small>') ?>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary btn-sm mr-2" onclick="return confirmTransfer()">
                                    <i class="fas fa-paper-plane mr-2"></i>Proses Transfer
                                </button>
                                <a href="<?= site_url('tabungan') ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times mr-2"></i>Batal
                                </a>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
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
                        <li>Pilih santri pengirim dan penerima</li>
                        <li>Pilih kategori transfer (tabungan/jajan)</li>
                        <li>Masukkan jumlah transfer</li>
                        <li>Saldo pengirim akan berkurang, saldo penerima bertambah</li>
                        <li>Keterangan bersifat opsional</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inisialisasi Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih Santri...',
            allowClear: true,
            minimumResultsForSearch: 0 // Tampilkan pencarian meskipun pilihan sedikit
        });

        // Autofocus search input ketika dropdown Select2 terbuka
        $('#santri_pengirim_id, #santri_penerima_id').on('select2:open', function() {
            setTimeout(function() {
                document.querySelector('.select2-search__field').focus();
            }, 100); // Delay supaya elemen muncul dulu
        });

        // Update saldo tersedia ketika santri pengirim atau kategori berubah
        $('#santri_pengirim_id, #kategori').on('change', function() {
            updateSaldoTersedia();
            updateSantriPenerimaOptions();
        });

        // Update saldo penerima ketika santri penerima atau kategori berubah
        $('#santri_penerima_id, #kategori').on('change', function() {
            updateSaldoPenerima();
        });

        // Filter santri penerima ketika santri pengirim berubah
        $('#santri_pengirim_id').on('change', function() {
            updateSantriPenerimaOptions();
        });

        // Validasi santri pengirim dan penerima tidak sama
        $('#santri_penerima_id').on('change', function() {
            var pengirim = $('#santri_pengirim_id').val();
            var penerima = $(this).val();

            if (pengirim && penerima && pengirim === penerima) {
                alert('Santri pengirim dan penerima tidak boleh sama!');
                $(this).val('').trigger('change');
            }
        });
    });

    function updateSaldoTersedia() {
        var santriPengirim = $('#santri_pengirim_id option:selected');
        var kategori = $('#kategori').val();

        if (santriPengirim.val() && kategori) {
            var saldoTabungan = parseInt(santriPengirim.data('saldo-tabungan')) || 0;
            var saldoJajan = parseInt(santriPengirim.data('saldo-jajan')) || 0;
            var saldoTersedia = kategori === 'tabungan' ? saldoTabungan : saldoJajan;

            $('#saldo-tersedia').text('Rp ' + saldoTersedia.toLocaleString('id-ID'));

            // Update max value untuk input jumlah
            $('#jumlah').attr('max', saldoTersedia);
        } else {
            $('#saldo-tersedia').text('Rp 0');
            $('#jumlah').removeAttr('max');
        }
    }

    function updateSantriPenerimaOptions() {
        var santriPengirim = $('#santri_pengirim_id').val();
        var kategori = $('#kategori').val();

        // Reset dropdown santri penerima
        $('#santri_penerima_id').val('').trigger('change');

        if (santriPengirim && kategori) {
            // Sembunyikan semua opsi terlebih dahulu
            $('#santri_penerima_id option').hide();

            // Tampilkan opsi default
            $('#santri_penerima_id option:first').show();

            // Filter dan tampilkan santri yang sesuai
            $('#santri_penerima_id option').each(function() {
                var option = $(this);
                var santriId = option.val();

                // Skip opsi default dan santri pengirim
                if (!santriId || santriId === santriPengirim) {
                    return;
                }

                // Tampilkan semua santri kecuali santri pengirim
                option.show();
            });
        } else {
            // Jika belum memilih santri pengirim atau kategori, tampilkan semua
            $('#santri_penerima_id option').show();
        }
    }

    function updateSaldoPenerima() {
        var santriPenerima = $('#santri_penerima_id option:selected');
        var kategori = $('#kategori').val();

        if (santriPenerima.val() && kategori) {
            var saldoTabungan = parseInt(santriPenerima.data('saldo-tabungan')) || 0;
            var saldoJajan = parseInt(santriPenerima.data('saldo-jajan')) || 0;
            var saldoPenerima = kategori === 'tabungan' ? saldoTabungan : saldoJajan;

            $('#saldo-penerima').text('Rp ' + saldoPenerima.toLocaleString('id-ID'));
        } else {
            $('#saldo-penerima').text('Rp 0');
        }
    }

    function confirmTransfer() {
        var pengirim = $('#santri_pengirim_id option:selected').text();
        var penerima = $('#santri_penerima_id option:selected').text();
        var jumlah = $('#jumlah').val();
        var kategori = $('#kategori option:selected').text();

        if (!pengirim || !penerima || !jumlah || !kategori) {
            alert('Mohon lengkapi semua data transfer!');
            return false;
        }

        var konfirmasi = confirm(
            'Konfirmasi Transfer:\n\n' +
            'Dari: ' + pengirim + '\n' +
            'Ke: ' + penerima + '\n' +
            'Kategori: ' + kategori + '\n' +
            'Jumlah: Rp ' + jumlah + '\n\n' +
            'Apakah Anda yakin ingin melakukan transfer ini?'
        );

        return konfirmasi;
    }
</script>