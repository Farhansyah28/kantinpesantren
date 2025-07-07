<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-edit mr-2"></i>Edit Menu
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('menu') ?>">Manajemen Menu</a></li>
        <li class="breadcrumb-item active">Edit Menu</li>
    </ol>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow">
            <div class="card-header py-2 bg-gradient-warning">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-edit mr-1"></i> Form Edit Menu
                </h6>
            </div>
            <div class="card-body py-3">
                <form action="<?= base_url('menu/update/'.$menu->id) ?>" method="POST">
                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                    <input type="hidden" name="kantin_id" value="<?= $this->session->userdata('kantin_id') ?>">
                    <div class="form-group row mb-2 align-items-center">
                        <label for="nama_menu" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Nama Menu <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" 
                                   class="form-control form-control-sm <?= form_error('nama_menu') ? 'is-invalid' : '' ?>" 
                                   name="nama_menu" 
                                   value="<?= set_value('nama_menu', $menu->nama_menu, TRUE) ?>" 
                                   required 
                                   placeholder="Contoh: Nasi Goreng">
                            <?= form_error('nama_menu', '<div class="invalid-feedback">', '</div>') ?>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="pemilik" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Pemilik <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input type="text" 
                                   class="form-control form-control-sm <?= form_error('pemilik') ? 'is-invalid' : '' ?>" 
                                   name="pemilik" 
                                   value="<?= set_value('pemilik', $menu->pemilik, TRUE) ?>" 
                                   required 
                                   placeholder="Contoh: Pak Ahmad">
                            <?= form_error('pemilik', '<div class="invalid-feedback">', '</div>') ?>
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
                                   value="<?= set_value('harga_beli', 'Rp ' . number_format($menu->harga_beli, 0, ',', '.'), TRUE) ?>" 
                                   required 
                                   placeholder="Contoh: Rp 5.000">
                            <?= form_error('harga_beli', '<div class="invalid-feedback">', '</div>') ?>
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
                                   value="<?= set_value('harga_jual', 'Rp ' . number_format($menu->harga_jual, 0, ',', '.'), TRUE) ?>" 
                                   required 
                                   placeholder="Contoh: Rp 8.000">
                            <?= form_error('harga_jual', '<div class="invalid-feedback">', '</div>') ?>
                        </div>
                    </div>
                    <div class="form-group row mt-2">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-warning btn-sm mr-2">
                                <i class="fas fa-save mr-1"></i> Update Menu
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