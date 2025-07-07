<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Santri</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('santri') ?>">Data Santri</a></li>
                        <li class="breadcrumb-item active">Tambah Santri</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-plus mr-1"></i> Form Tambah Santri
                    </h6>
                </div>
                <div class="card-body py-3">
                    <?= form_open('santri/store', ['id' => 'form-santri']) ?>
                        <div class="row">
                            <!-- Data Santri -->
                            <div class="col-md-6">
                                <h6 class="text-primary font-weight-bold mb-2">
                                    <i class="fas fa-user mr-1"></i> Data Santri
                                </h6>
                                
                                <div class="form-group row mb-2 align-items-center">
                                    <label for="nomor_induk" class="col-sm-4 col-form-label font-weight-bold text-gray-700 text-right">
                                        Nomor Induk <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                               class="form-control form-control-sm <?= form_error('nomor_induk') ? 'is-invalid' : '' ?>" 
                                               id="nomor_induk" 
                                               name="nomor_induk" 
                                               value="<?= set_value('nomor_induk', '', TRUE) ?>" 
                                               placeholder="Contoh: 2023001" 
                                               required>
                                        <?= form_error('nomor_induk', '<div class="invalid-feedback">', '</div>') ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-2 align-items-center">
                                    <label for="nama" class="col-sm-4 col-form-label font-weight-bold text-gray-700 text-right">
                                        Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                               class="form-control form-control-sm <?= form_error('nama') ? 'is-invalid' : '' ?>" 
                                               id="nama" 
                                               name="nama" 
                                               value="<?= set_value('nama', '', TRUE) ?>" 
                                               placeholder="Masukkan nama lengkap santri" 
                                               required>
                                        <?= form_error('nama', '<div class="invalid-feedback">', '</div>') ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-2 align-items-center">
                                    <label for="kelas" class="col-sm-4 col-form-label font-weight-bold text-gray-700 text-right">
                                        Angkatan <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                               class="form-control form-control-sm <?= form_error('kelas') ? 'is-invalid' : '' ?>" 
                                               id="kelas" 
                                               name="kelas" 
                                               value="<?= set_value('kelas', '', TRUE) ?>" 
                                               placeholder="Contoh: 2025" 
                                               required>
                                        <?= form_error('kelas', '<div class="invalid-feedback">', '</div>') ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-2 align-items-center">
                                    <label for="jenis_kelamin" class="col-sm-4 col-form-label font-weight-bold text-gray-700 text-right">
                                        Jenis Kelamin <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm <?= form_error('jenis_kelamin') ? 'is-invalid' : '' ?>" 
                                                id="jenis_kelamin" 
                                                name="jenis_kelamin" 
                                                required>
                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                            <?php if ($this->session->userdata('kantin_id') == 1): ?>
                                                <option value="L" <?= set_select('jenis_kelamin', 'L') ?>>Laki-laki</option>
                                            <?php elseif ($this->session->userdata('kantin_id') == 2): ?>
                                                <option value="P" <?= set_select('jenis_kelamin', 'P') ?>>Perempuan</option>
                                            <?php else: ?>
                                                <option value="L" <?= set_select('jenis_kelamin', 'L') ?>>Laki-laki</option>
                                                <option value="P" <?= set_select('jenis_kelamin', 'P') ?>>Perempuan</option>
                                            <?php endif; ?>
                                        </select>
                                        <?= form_error('jenis_kelamin', '<div class="invalid-feedback">', '</div>') ?>
                                        <small class="form-text text-muted">
                                            <?php if ($this->session->userdata('kantin_id') == 1): ?>
                                                <i class="fas fa-info-circle text-primary"></i> Kantin Putra - Hanya untuk santri laki-laki
                                            <?php elseif ($this->session->userdata('kantin_id') == 2): ?>
                                                <i class="fas fa-info-circle text-primary"></i> Kantin Putri - Hanya untuk santri perempuan
                                            <?php else: ?>
                                                <i class="fas fa-info-circle text-primary"></i> Pilih jenis kelamin sesuai kantin yang akan diakses
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Wali Santri -->
                            <div class="col-md-6">
                                <h6 class="text-primary font-weight-bold mb-2">
                                    <i class="fas fa-users mr-1"></i> Data Wali Santri
                                </h6>
                                
                                <div class="form-group row mb-2 align-items-center">
                                    <label for="nama_wali" class="col-sm-4 col-form-label font-weight-bold text-gray-700 text-right">
                                        Nama Wali
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                               class="form-control form-control-sm <?= form_error('nama_wali') ? 'is-invalid' : '' ?>" 
                                               id="nama_wali" 
                                               name="nama_wali" 
                                               value="<?= set_value('nama_wali', '', TRUE) ?>" 
                                               placeholder="Masukkan nama wali santri">
                                        <?= form_error('nama_wali', '<div class="invalid-feedback">', '</div>') ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-2 align-items-center">
                                    <label for="kontak_wali" class="col-sm-4 col-form-label font-weight-bold text-gray-700 text-right">
                                        Kontak Wali
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" 
                                               class="form-control form-control-sm <?= form_error('kontak_wali') ? 'is-invalid' : '' ?>" 
                                               id="kontak_wali" 
                                               name="kontak_wali" 
                                               value="<?= set_value('kontak_wali', '', TRUE) ?>" 
                                               placeholder="Contoh: 081234567890">
                                        <?= form_error('kontak_wali', '<div class="invalid-feedback">', '</div>') ?>
                                    </div>
                                </div>

                                <div class="form-group row mb-2 align-items-center">
                                    <label for="hubungan_wali" class="col-sm-4 col-form-label font-weight-bold text-gray-700 text-right">
                                        Hubungan Wali
                                    </label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm <?= form_error('hubungan_wali') ? 'is-invalid' : '' ?>" 
                                                id="hubungan_wali" 
                                                name="hubungan_wali">
                                            <option value="">Pilih Hubungan</option>
                                            <option value="Ayah" <?= set_select('hubungan_wali', 'Ayah') ?>>Ayah</option>
                                            <option value="Ibu" <?= set_select('hubungan_wali', 'Ibu') ?>>Ibu</option>
                                            <option value="Wali" <?= set_select('hubungan_wali', 'Wali') ?>>Wali</option>
                                            <option value="Kakak" <?= set_select('hubungan_wali', 'Kakak') ?>>Kakak</option>
                                            <option value="Lainnya" <?= set_select('hubungan_wali', 'Lainnya') ?>>Lainnya</option>
                                        </select>
                                        <?= form_error('hubungan_wali', '<div class="invalid-feedback">', '</div>') ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mt-2">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-sm mr-2">
                                    <i class="fas fa-save mr-1"></i> Simpan
                                </button>
                                <button type="reset" class="btn btn-secondary btn-sm mr-2">
                                    <i class="fas fa-undo mr-1"></i> Reset
                                </button>
                                <a href="<?= base_url('santri') ?>" class="btn btn-default btn-sm">
                                    <i class="fas fa-times mr-1"></i> Batal
                                </a>
                            </div>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Validasi form sebelum submit
    $('#form-santri').submit(function(e) {
        // Validasi nomor induk harus unik (bisa ditambahkan AJAX check)
        let nomorInduk = $('#nomor_induk').val();
        if (nomorInduk.length < 3) {
            e.preventDefault();
            alert('Nomor induk minimal 3 karakter!');
            return false;
        }
        
        // Validasi jenis kelamin sesuai kantin
        let jenisKelamin = $('#jenis_kelamin').val();
        let kantinId = <?= $this->session->userdata('kantin_id') ?? 'null' ?>;
        
        if (kantinId == 1 && jenisKelamin != 'L') {
            e.preventDefault();
            alert('Kantin Putra hanya untuk santri laki-laki!');
            return false;
        } else if (kantinId == 2 && jenisKelamin != 'P') {
            e.preventDefault();
            alert('Kantin Putri hanya untuk santri perempuan!');
            return false;
        }
        
        // Validasi jenis kelamin harus dipilih
        if (!jenisKelamin) {
            e.preventDefault();
            alert('Pilih jenis kelamin!');
            return false;
        }
    });
    
    // Real-time validation untuk jenis kelamin
    $('#jenis_kelamin').change(function() {
        let jenisKelamin = $(this).val();
        let kantinId = <?= $this->session->userdata('kantin_id') ?? 'null' ?>;
        
        if (kantinId == 1 && jenisKelamin == 'P') {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
            $(this).after('<div class="invalid-feedback">Kantin Putra hanya untuk santri laki-laki</div>');
        } else if (kantinId == 2 && jenisKelamin == 'L') {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
            $(this).after('<div class="invalid-feedback">Kantin Putri hanya untuk santri perempuan</div>');
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
});
</script>
