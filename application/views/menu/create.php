<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-plus-circle mr-2"></i>Tambah Menu Baru
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('menu') ?>">Manajemen Menu</a></li>
        <li class="breadcrumb-item active">Tambah Menu</li>
    </ol>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow">
            <div class="card-header py-2 bg-gradient-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-plus-circle mr-1"></i> Form Tambah Menu
                </h6>
            </div>
            <div class="card-body py-3">
                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= $this->session->flashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('menu/store') ?>" method="POST">
                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="nama_menu" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Nama Menu <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   name="nama_menu" 
                                   value="<?= set_value('nama_menu', '', TRUE) ?>" 
                                   required 
                                   placeholder="Contoh: Nasi Goreng">
                            <?= form_error('nama_menu', '<small class="text-danger">', '</small>') ?>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="pemilik" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Pemilik <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <select id="pemilik" name="pemilik" class="form-control form-control-sm select2" required>
                                <option></option>
                                <?php if(isset($pemilik_list) && is_array($pemilik_list)): foreach($pemilik_list as $p): ?>
                                    <option value="<?= htmlspecialchars($p->pemilik) ?>" <?= set_value('pemilik') == $p->pemilik ? 'selected' : '' ?>><?= htmlspecialchars($p->pemilik) ?></option>
                                <?php endforeach; endif; ?>
                                <option value="__new__" <?= set_value('pemilik') == '__new__' ? 'selected' : '' ?>>Tambah Pemilik Baru</option>
                            </select>
                            <input type="text" name="pemilik_baru" id="input-pemilik-baru" class="form-control form-control-sm mt-2" placeholder="Masukkan nama pemilik baru" style="display:none;" value="<?= set_value('pemilik_baru', '', TRUE) ?>">
                            <?= form_error('pemilik', '<small class="text-danger">', '</small>') ?>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="harga_beli" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Harga Beli <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text"
                                   class="form-control form-control-sm rupiah-input <?= form_error('harga_beli') ? 'is-invalid' : '' ?>"
                                   name="harga_beli"
                                   value="<?= set_value('harga_beli', '', TRUE) ?>"
                                   required
                                   placeholder="Contoh: Rp 5.000">
                            <small class="form-text text-muted">Harga beli dari supplier</small>
                            <?= form_error('harga_beli', '<small class="text-danger">', '</small>') ?>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="harga_jual" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Harga Jual <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text"
                                   class="form-control form-control-sm rupiah-input <?= form_error('harga_jual') ? 'is-invalid' : '' ?>"
                                   name="harga_jual"
                                   value="<?= set_value('harga_jual', '', TRUE) ?>"
                                   required
                                   placeholder="Contoh: Rp 8.000">
                            <small class="form-text text-muted">Harga jual ke santri</small>
                            <?= form_error('harga_jual', '<small class="text-danger">', '</small>') ?>
                        </div>
                    </div>
                    <div class="form-group row mt-2">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary btn-sm mr-2">
                                <i class="fas fa-save mr-1"></i> Simpan Menu
                            </button>
                            <a href="<?= base_url('menu') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/rupiah-formatter.js') ?>"></script>
<script src="<?= base_url('assets/js/select2.min.js') ?>"></script>
<link href="<?= base_url('assets/css/select2.min.css') ?>" rel="stylesheet" />
<script>
$(document).ready(function() {
    $('#pemilik').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih atau ketik pemilik',
        allowClear: true
    });
    // Toggle input pemilik baru
    function toggleInputPemilik() {
        if ($('#pemilik').val() === '__new__') {
            $('#input-pemilik-baru').show().prop('required', true);
        } else {
            $('#input-pemilik-baru').hide().prop('required', false).val('');
        }
    }
    $('#pemilik').on('change', toggleInputPemilik);
    toggleInputPemilik(); // initial
});
</script> 