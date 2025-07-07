<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Register - E-Kantin</title>
    <!-- Custom fonts for this template-->
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="<?= base_url('assets/css/nunito-fonts.css') ?>" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
    <style>
        .bg-register-image {
            background: url('<?= base_url('assets/img/pesantren-bg.jpg') ?>');
            background-position: center;
            background-size: cover;
        }
        .form-control-user {
            font-size: 0.875rem;
            border-radius: 10rem;
            padding: 0.75rem 1rem;
        }
        .btn-user {
            font-size: 0.875rem;
            border-radius: 10rem;
            padding: 0.75rem 1rem;
        }
        .card-body {
            padding: 2rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                            <div class="col-lg-7">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">
                                            <i class="fas fa-user-plus mr-2"></i>Buat Akun Baru
                                        </h1>
                                        <p class="text-gray-600 mb-4">Daftar sebagai pengguna E-Kantin</p>
                                    </div>
                                    
                                    <?php if($this->session->flashdata('error')): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            <?= $this->session->flashdata('error') ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <form class="user" method="POST" action="<?= base_url('auth/register') ?>">
                                        <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                        <div class="form-group">
                                            <input type="text" 
                                                   class="form-control form-control-user" 
                                                   name="username" 
                                                   placeholder="Username" 
                                                   value="<?= set_value('username', '', TRUE) ?>" 
                                                   required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" 
                                                   class="form-control form-control-user" 
                                                   name="password" 
                                                   placeholder="Password" 
                                                   required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" 
                                                   class="form-control form-control-user" 
                                                   name="confirm_password" 
                                                   placeholder="Konfirmasi Password" 
                                                   required>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control form-control-user" name="role" id="role" required>
                                                <option value="">-- Pilih Role --</option>
                                                <option value="admin" <?= set_select('role','admin') ?>>Admin</option>
                                                <option value="operator" <?= set_select('role','operator') ?>>Operator</option>
                                                <option value="keuangan" <?= set_select('role','keuangan') ?>>Keuangan</option>
                                                <option value="santri" <?= set_select('role','santri') ?>>Santri</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="gender-group">
                                            <select class="form-control form-control-user" name="gender" id="gender">
                                                <option value="">-- Pilih Gender --</option>
                                                <option value="L" <?= set_select('gender','L') ?>>Laki-laki</option>
                                                <option value="P" <?= set_select('gender','P') ?>>Perempuan</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            <i class="fas fa-user-plus mr-1"></i> Register Akun
                                        </button>
                                    </form>
                                    
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="<?= base_url('auth/login') ?>">
                                            <i class="fas fa-sign-in-alt mr-1"></i>Sudah punya akun? Login
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- Core plugin JavaScript-->
    <script src="<?= base_url('assets/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
    <!-- Custom scripts for all pages-->
    <script src="<?= base_url('assets/js/sb-admin-2.min.js') ?>"></script>

    <script>
    $(document).ready(function() {
        // Fungsi untuk mengatur gender field berdasarkan role
        function toggleGenderField() {
            var role = $('#role').val();
            var genderGroup = $('#gender-group');
            var genderSelect = $('#gender');
            
            if (role === 'admin' || role === 'keuangan') {
                genderGroup.hide();
                genderSelect.removeAttr('required');
                genderSelect.val('');
            } else {
                genderGroup.show();
                genderSelect.attr('required', 'required');
            }
        }
        
        // Jalankan saat halaman dimuat
        toggleGenderField();
        
        // Jalankan saat role berubah
        $('#role').on('change', function() {
            toggleGenderField();
        });
        
        // Validasi konfirmasi password
        $('input[name="confirm_password"]').on('input', function() {
            var password = $('input[name="password"]').val();
            var confirmPassword = $(this).val();
            
            if (password !== confirmPassword) {
                $(this).addClass('is-invalid');
                $(this).removeClass('is-valid');
            } else {
                $(this).removeClass('is-invalid');
                $(this).addClass('is-valid');
            }
        });
        
        // Validasi form sebelum submit
        $('form').on('submit', function(e) {
            var password = $('input[name="password"]').val();
            var confirmPassword = $('input[name="confirm_password"]').val();
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak sama!');
                return false;
            }
            
            // Validasi gender untuk role selain admin dan keuangan
            var role = $('#role').val();
            var gender = $('#gender').val();
            
            if (role !== 'admin' && role !== 'keuangan' && !gender) {
                e.preventDefault();
                alert('Gender wajib dipilih untuk role operator dan santri!');
                return false;
            }
        });
    });
    </script>
</body>
</html> 