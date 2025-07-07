<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-hand-holding-usd mr-2"></i>Penarikan Tabungan
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('tabungan') ?>">Tabungan</a></li>
        <li class="breadcrumb-item active">Penarikan</li>
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
            <div class="card-header py-2 bg-gradient-warning">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-minus-circle mr-1"></i> Form Penarikan Tabungan
                </h6>
            </div>
            <div class="card-body py-3">
                <?= form_open('tabungan/penarikan') ?>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="santri_id" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Pilih Santri <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control form-control-sm select2" id="santri_id" name="santri_id" style="width: 100%;" required>
                                <option value="">-- Pilih Santri --</option>
                                <?php foreach($santri as $s): ?>
                                    <option value="<?= $s->id ?>"<?= (isset($_GET['santri_id']) && $_GET['santri_id'] == $s->id) ? ' selected' : '' ?>><?= $s->nomor_induk ?> - <?= $s->nama ?> (<?= $s->kelas ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error('santri_id', '<small class="text-danger">', '</small>') ?>
                        </div>
                    </div>

                    <div class="form-group row mb-2 align-items-center">
                        <label for="kategori" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Kategori <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select name="kategori" id="kategori" class="form-control form-control-sm" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="tabungan">Tabungan</option>
                                <option value="jajan">Jajan</option>
                            </select>
                            <?= form_error('kategori', '<small class="text-danger">', '</small>') ?>
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
                        <label for="jumlah" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Jumlah Penarikan <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" 
                                   name="jumlah" 
                                   id="jumlah" 
                                   class="form-control form-control-sm rupiah-input" 
                                   required 
                                   placeholder="Rp 0">
                            <?= form_error('jumlah', '<small class="text-danger">', '</small>') ?>
                        </div>
                    </div>

                    <div class="form-group row mb-2 align-items-center">
                        <label for="keterangan_template" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Template Keterangan
                        </label>
                        <div class="col-sm-9">
                            <select class="form-control form-control-sm" id="keterangan_template">
                                <option value="">-- Pilih Template --</option>
                                <option value="Potong Rambut">Potong Rambut</option>
                                <option value="Kas Kamar">Kas Kamar</option>
                                <option value="Kas Kelas">Kas Kelas</option>
                                <option value="Surat Jalan">Surat Jalan</option>
                                <option value="Lainnya">Ketik Sendiri...</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-2 align-items-center">
                        <label for="keterangan" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Keterangan <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <textarea class="form-control form-control-sm" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="2" 
                                      required 
                                      placeholder="Masukkan keterangan penarikan..."></textarea>
                            <?= form_error('keterangan', '<small class="text-danger">', '</small>') ?>
                        </div>
                    </div>

                    <div class="form-group row mt-2">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-warning btn-sm mr-2">
                                <i class="fas fa-save mr-1"></i> Proses Penarikan
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
                        <li>Pilih santri yang akan melakukan penarikan</li>
                        <li>Pilih kategori penarikan (tabungan/jajan)</li>
                        <li>Masukkan jumlah yang akan ditarik</li>
                        <li>Pastikan saldo mencukupi untuk penarikan</li>
                        <li>Keterangan wajib diisi</li>
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

    // Jika sudah ada santri_id terpilih (dari URL), trigger change setelah Select2 siap dan buat readonly
    setTimeout(function() {
        if ($('#santri_id').val()) {
            $('#santri_id').trigger('change');
            $('#santri_id').prop('disabled', true);
        }
    }, 300);

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

    // Validasi jumlah penarikan tidak melebihi saldo
    $('form').submit(function(e) {
        var kategori = $('#kategori').val();
        var jumlah = parseInt($('#jumlah').val().replace(/[^\d]/g, '')) || 0;
        var saldoTabungan = parseInt($('#saldo-tabungan').val().replace(/[^\d]/g, '')) || 0;
        var saldoJajan = parseInt($('#saldo-jajan').val().replace(/[^\d]/g, '')) || 0;
        
        if (kategori === 'tabungan' && jumlah > saldoTabungan) {
            e.preventDefault();
            alert('Jumlah penarikan melebihi saldo tabungan!');
            return false;
        }
        
        if (kategori === 'jajan' && jumlah > saldoJajan) {
            e.preventDefault();
            alert('Jumlah penarikan melebihi saldo jajan!');
            return false;
        }
    });

    // Handle template keterangan
    $('#keterangan_template').change(function() {
        var selectedValue = $(this).val();
        
        if (selectedValue === 'Lainnya') {
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