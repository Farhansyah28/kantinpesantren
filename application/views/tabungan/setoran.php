<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-money-bill-wave mr-2"></i>Setoran Tabungan
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('tabungan') ?>">Tabungan</a></li>
        <li class="breadcrumb-item active">Setoran</li>
    </ol>
</div>

<!-- Flash Messages -->
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
            <div class="card-header py-2 bg-gradient-success">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-plus-circle mr-1"></i> Form Setoran Tabungan
                </h6>
            </div>
            <div class="card-body py-3">
                <?= form_open('tabungan/setoran') ?>
                <div class="form-group row mb-2 align-items-center">
                    <label for="santri_id" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                        Pilih Santri <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control form-control-sm select2" id="santri_id" name="santri_id" style="width: 100%;" required<?= (isset($_GET['santri_id']) && $_GET['santri_id']) ? ' disabled' : '' ?>>
                            <option value="">-- Pilih Santri --</option>
                            <?php foreach ($santri as $s): ?>
                                <option value="<?= $s->id ?>" <?= (isset($_GET['santri_id']) && $_GET['santri_id'] == $s->id) ? ' selected' : '' ?>><?= $s->nomor_induk ?> - <?= $s->nama ?> (<?= $s->kelas ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('santri_id', '<small class="text-danger">', '</small>') ?>
                    </div>
                </div>

                <!-- Informasi Saldo -->
                <div class="row mb-3" id="saldo-info" style="display: none;">
                    <div class="col-12">
                        <table class="table table-bordered mb-2">
                            <tbody>
                                <tr>
                                    <th class="text-center align-middle" style="width:33%">Saldo Tabungan</th>
                                    <th class="text-center align-middle" style="width:33%">Saldo Jajan</th>
                                    <th class="text-center align-middle" style="width:34%">Total Saldo</th>
                                </tr>
                                <tr>
                                    <td class="text-center align-middle font-weight-bold text-success" id="saldo-tabungan-display">Rp 0</td>
                                    <td class="text-center align-middle font-weight-bold text-info" id="saldo-jajan-display">Rp 0</td>
                                    <td class="text-center align-middle font-weight-bold text-primary" id="total-saldo-display">Rp 0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Hidden inputs untuk form -->
                <input type="hidden" id="saldo-tabungan" name="saldo_tabungan">
                <input type="hidden" id="saldo-jajan" name="saldo_jajan">
                <input type="hidden" id="total-saldo" name="total_saldo">

                <hr class="my-3">

                <div class="form-group row mb-2 align-items-center">
                    <label for="jumlah_tabungan" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                        Jumlah Tabungan
                    </label>
                    <div class="col-sm-9">
                        <input type="text"
                            class="form-control form-control-sm rupiah-input"
                            id="jumlah_tabungan"
                            name="jumlah_tabungan"
                            value="0"
                            placeholder="Rp 0">
                    </div>
                </div>

                <div class="form-group row mb-2 align-items-center">
                    <label for="jumlah_jajan" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                        Jumlah Jajan
                    </label>
                    <div class="col-sm-9">
                        <input type="text"
                            class="form-control form-control-sm rupiah-input"
                            id="jumlah_jajan"
                            name="jumlah_jajan"
                            value="0"
                            placeholder="Rp 0">
                    </div>
                </div>

                <div class="form-group row mb-2 align-items-center">
                    <label for="keterangan_template" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                        Template Keterangan
                    </label>
                    <div class="col-sm-9">
                        <select class="form-control form-control-sm" id="keterangan_template">
                            <option value="">-- Pilih Template --</option>
                            <option value="Cash">Cash</option>
                            <option value="Transfer">Transfer</option>
                            <option value="Santri">Santri</option>
                            <option value="Isi sendiri">Isi sendiri</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-2 align-items-center">
                    <label for="keterangan" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                        Keterangan
                    </label>
                    <div class="col-sm-9">
                        <textarea class="form-control form-control-sm"
                            id="keterangan"
                            name="keterangan"
                            rows="2"
                            placeholder="Masukkan keterangan setoran..."></textarea>
                    </div>
                </div>

                <div class="form-group row mt-2">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-success btn-sm mr-2">
                            <i class="fas fa-save mr-1"></i> Simpan Setoran
                        </button>
                        <a href="<?= base_url('tabungan') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <?= form_close() ?>
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
                        <li>Pilih santri yang akan melakukan setoran</li>
                        <li>Masukkan jumlah setoran untuk tabungan dan/atau jajan</li>
                        <li>Saldo akan otomatis terupdate setelah setoran</li>
                        <li>Keterangan bersifat opsional</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Auto-hide only success and error alerts after 5 seconds
        setTimeout(function() {
            $('.alert-success, .alert-danger').fadeOut('slow');
        }, 5000);

        // Inisialisasi Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih Santri...',
            allowClear: true,
            minimumResultsForSearch: 0 // Tampilkan pencarian meskipun pilihan sedikit
        });

        // Jika sudah ada santri_id terpilih (dari URL), trigger change setelah Select2 siap
        setTimeout(function() {
            if ($('#santri_id').val()) {
                $('#santri_id').trigger('change');
            }
        }, 300);

        // Autofocus search input ketika dropdown Select2 terbuka
        $('#santri_id').on('select2:open', function() {
            setTimeout(function() {
                document.querySelector('.select2-search__field').focus();
            }, 100); // Delay supaya elemen muncul dulu
        });

        // Load saldo saat santri dipilih
        $('#santri_id').change(function() {
            var santriId = $(this).val();
            if (santriId) {
                // Show loading state
                $('#saldo-info').hide();
                $('#saldo-tabungan-display').text('Loading...');
                $('#saldo-jajan-display').text('Loading...');
                $('#total-saldo-display').text('Loading...');

                $.ajax({
                    url: '<?= base_url('tabungan/get_saldo/') ?>' + santriId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // Format currency tanpa animasi
                        $('#saldo-tabungan-display').text(RupiahFormatter.format(response.saldo_tabungan));
                        $('#saldo-jajan-display').text(RupiahFormatter.format(response.saldo_jajan));
                        $('#total-saldo-display').text(RupiahFormatter.format(response.total_saldo));

                        // Set hidden values
                        $('#saldo-tabungan').val(response.saldo_tabungan);
                        $('#saldo-jajan').val(response.saldo_jajan);
                        $('#total-saldo').val(response.total_saldo);

                        // Show info
                        $('#saldo-info').show();
                    },
                    error: function() {
                        $('#saldo-tabungan-display').text('Error');
                        $('#saldo-jajan-display').text('Error');
                        $('#total-saldo-display').text('Error');
                        $('#saldo-info').show();
                    }
                });
            } else {
                $('#saldo-tabungan-display').text('Rp 0');
                $('#saldo-jajan-display').text('Rp 0');
                $('#total-saldo-display').text('Rp 0');
                $('#saldo-tabungan').val('');
                $('#saldo-jajan').val('');
                $('#total-saldo').val('');
                $('#saldo-info').hide();
            }
        });

        // Validasi minimal satu setoran
        $('form').submit(function(e) {
            var tabungan = RupiahFormatter.unformat($('#jumlah_tabungan').val());
            var jajan = RupiahFormatter.unformat($('#jumlah_jajan').val());

            if (tabungan === 0 && jajan === 0) {
                e.preventDefault();
                alert('Minimal harus ada setoran untuk tabungan atau jajan!');
                return false;
            }
        });

        // Handle template keterangan
        $('#keterangan_template').change(function() {
            var selectedValue = $(this).val();

            if (selectedValue === 'Isi sendiri') {
                $('#keterangan').val('').focus();
            } else if (selectedValue) {
                $('#keterangan').val(selectedValue);
            } else {
                $('#keterangan').val('');
            }
        });

        // Reset template saat form dibersihkan
        $('form').on('reset', function() {
            $('#keterangan_template').val('');
            $('#keterangan').val('');
        });
    });
</script>