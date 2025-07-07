<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Ekantin DEM
    </title>
    <link rel="icon" href="<?= base_url('assets/img/logo.png') ?>" type="image/x-icon">
    <!-- Custom fonts for this template-->
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="<?= base_url('assets/css/nunito-fonts.css') ?>" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
    <style>
        .bg-login-image {
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
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                <!-- 
                                GANTI GAMBAR DI ATAS DENGAN GAMBAR LOKAL:
                                1. Simpan gambar pesantren di: assets/img/pesantren-bg.jpg
                                2. Ganti URL di CSS dengan: <?= base_url('assets/img/pesantren-bg.jpg') ?>
                                3. Atau gunakan gambar lain yang sesuai dengan tema pesantren
                                -->
                                <div class="overlay-text">
                                    
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">
                                            <i class="fas fa-sign-in-alt mr-2"></i>Selamat Datang Aplikasi Kantin DEM
                                        </h1>
                                        <p class="text-gray-600 mb-4">Silakan login untuk melanjutkan</p>
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

                                    <?php if($this->session->flashdata('success')): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <?= $this->session->flashdata('success') ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <form class="user" method="POST" action="<?= base_url('auth/login') ?>">
                                        <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                        <div class="form-group">
                                            <input type="text" 
                                                   class="form-control form-control-user" 
                                                   name="username" 
                                                   placeholder="Masukkan Username..." 
                                                   required 
                                                   autofocus>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" 
                                                   class="form-control form-control-user" 
                                                   name="password" 
                                                   placeholder="Password" 
                                                   required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                                        </button>
                                    </form>
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
</body>
</html> 