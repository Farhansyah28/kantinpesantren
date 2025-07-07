<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Profil Pengguna</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profil</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center py-3">
                            <?php if($this->session->userdata('role') == 'santri' && isset($santri->foto) && $santri->foto): ?>
                                <img src="<?= base_url('uploads/santri/' . $santri->foto) ?>" class="rounded-circle mb-2" width="120" height="120">
                            <?php elseif($this->session->userdata('role') == 'santri' && isset($santri->jenis_kelamin) && $santri->jenis_kelamin == 'P'): ?>
                                <img src="<?= base_url('assets/img/profilwebperempuan.png') ?>" class="rounded-circle mb-2" width="120" height="120">
                            <?php else: ?>
                                <img src="<?= base_url('assets/img/profilweb.png') ?>" class="rounded-circle mb-2" width="120" height="120">
                            <?php endif; ?>
                            
                            <h5 class="mb-1"><?= isset($user) ? $user->username : 'N/A' ?></h5>
                            <span class="badge badge-primary mb-2"><?= isset($user) ? ucfirst($user->role) : 'N/A' ?></span>
                            
                            <?php if($this->session->userdata('role') == 'santri' && isset($santri)): ?>
                            <div class="mt-2">
                                <p class="mb-1 small"><strong><?= $santri->nama ?></strong></p>
                                <p class="mb-1 small">NIS: <?= $santri->nomor_induk ?></p>
                                <p class="mb-1 small">Kelas: <?= $santri->kelas ?></p>
                                <p class="mb-0 small">Saldo Jajan: Rp <?= number_format($santri->saldo_jajan, 0, ',', '.') ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header py-2">
                            <h3 class="card-title mb-0">Ganti Password</h3>
                        </div>
                        <div class="card-body py-3">
                            <?php if($this->session->flashdata('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    <?= $this->session->flashdata('success') ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <?= $this->session->flashdata('error') ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?= form_open('auth/change_password') ?>
                                <div class="form-group row mb-2 align-items-center">
                                    <label for="current_password" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                                        Password Saat Ini <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="password" 
                                               class="form-control form-control-sm" 
                                               id="current_password" 
                                               name="current_password" 
                                               required>
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-2 align-items-center">
                                    <label for="new_password" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                                        Password Baru <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="password" 
                                               class="form-control form-control-sm" 
                                               id="new_password" 
                                               name="new_password" 
                                               required 
                                               minlength="6">
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-2 align-items-center">
                                    <label for="confirm_password" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                                        Konfirmasi Password <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="password" 
                                               class="form-control form-control-sm" 
                                               id="confirm_password" 
                                               name="confirm_password" 
                                               required 
                                               minlength="6">
                                    </div>
                                </div>
                                
                                <div class="form-group row mt-2">
                                    <div class="col-sm-9 offset-sm-3">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-save mr-1"></i> Ganti Password
                                        </button>
                                    </div>
                                </div>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Validasi password baru dan konfirmasi password harus sama
    $('#confirm_password').on('input', function() {
        let newPassword = $('#new_password').val();
        let confirmPassword = $(this).val();
        
        if (newPassword && confirmPassword) {
            if (newPassword !== confirmPassword) {
                $(this).addClass('is-invalid');
                $(this).removeClass('is-valid');
            } else {
                $(this).removeClass('is-invalid');
                $(this).addClass('is-valid');
            }
        } else {
            $(this).removeClass('is-invalid is-valid');
        }
    });
    
    // Validasi form sebelum submit
    $('form').on('submit', function(e) {
        var newPassword = $('#new_password').val();
        var confirmPassword = $('#confirm_password').val();
        
        if (newPassword && confirmPassword && newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Password baru dan konfirmasi password tidak sama!');
            return false;
        }
    });
});
</script> 