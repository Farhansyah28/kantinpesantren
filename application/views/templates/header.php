<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem Manajemen Kantin Digital">
    <meta name="author" content="Tim E-Kantin">
    <title><?= html_escape($title ?? 'Dashboard') ?> E Kantin</title>

    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('assets/img/logo.png') ?>" type="image/x-icon">

    <!-- Font Awesome -->
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">

    <!-- Google Fonts -->
    <link href="<?= base_url('assets/css/nunito-fonts.css') ?>" rel="stylesheet">

    <!-- SB Admin 2 CSS -->
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="<?= base_url('assets/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="<?= base_url('assets/css/select2.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/select2-bootstrap-5-theme.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/select2-bootstrap4-theme.min.css') ?>" rel="stylesheet">

    <!-- JQuery -->
    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>

    <!-- Chart.js -->
    <script src="<?= base_url('assets/vendor/chart.js/Chart.bundle.min.js') ?>"></script>

    <!-- Rupiah Formatter -->
    <script src="<?= base_url('assets/js/rupiah-formatter.js') ?>"></script>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php
        $role = $this->session->userdata('role');
        if ($role === 'operator') {
            $this->load->view('templates/operator_sidebar');
        } elseif ($role === 'keuangan') {
            $this->load->view('templates/keuangan_sidebar');
        } else {
            // Default sidebar untuk admin dan role lainnya
            $this->load->view('templates/admin_sidebar');
        }
        ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Kantin Info dan Ganti Kantin -->
                        <?php if ($this->session->userdata('role') != 'admin' && $this->session->userdata('kantin_nama')): ?>
                            <li class="nav-item mr-3">
                                <span class="navbar-text text-gray-600">
                                    <i class="fas fa-store mr-1"></i>
                                    <?= html_escape($this->session->userdata('kantin_nama')) ?>
                                </span>
                            </li>
                        <?php endif; ?>

                        <!-- Nav Item - Ganti Kantin (Admin only) -->

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?= html_escape($this->session->userdata('username') ?? 'User') ?>
                                </span>
                                <img class="img-profile rounded-circle"
                                    src="<?= base_url('assets/img/profil.png') ?>" alt="Profile Picture">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= base_url('dashboard/profile') ?>">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="<?= base_url('auth/change_password') ?>">
                                    <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Ganti Password
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= base_url('auth/logout') ?>" data-method="post">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                        <?php if (in_array($this->session->userdata('role'), ['admin', 'keuangan'])): ?>
                            <!-- Nav Item - Notifications -->
                            <li class="nav-item dropdown no-arrow mx-1">
                                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-bell fa-fw"></i>
                                    <!-- Counter - Alerts -->
                                    <span class="badge badge-danger badge-counter" id="notification-counter" style="display: none;">0</span>
                                </a>
                                <!-- Dropdown - Alerts -->
                                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="alertsDropdown">
                                    <h6 class="dropdown-header">
                                        Notifikasi Transaksi
                                    </h6>
                                    <div id="notification-list">
                                        <div class="text-center py-3">
                                            <i class="fas fa-bell fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">Tidak ada notifikasi baru</p>
                                        </div>
                                    </div>
                                    <a class="dropdown-item text-center small text-gray-500" href="#" id="mark-all-read">
                                        Tandai semua sudah dibaca
                                    </a>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Modal Session Timeout Warning -->
                    <div class="modal fade" id="modal-session-timeout" tabindex="-1" role="dialog" aria-labelledby="modalSessionTimeoutLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title" id="modalSessionTimeoutLabel"><i class="fas fa-exclamation-triangle mr-2"></i> Sesi Akan Habis</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Sesi login Anda akan habis dalam <span id="timeout-countdown">120</span> detik.<br>Silakan klik di mana saja untuk memperpanjang sesi.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-dismiss="modal">Perpanjang Sesi</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        // Konfigurasi waktu (detik)
                        const SESSION_TIMEOUT = 1200; // 20 menit
                        const WARNING_BEFORE = 120; // 2 menit sebelum habis
                        let warningShown = false;
                        let timeoutInterval, countdownInterval;

                        function resetSessionWarning() {
                            clearTimeout(timeoutInterval);
                            clearInterval(countdownInterval);
                            warningShown = false;
                            $("#modal-session-timeout").modal('hide');
                            startSessionWarning();
                        }

                        function startSessionWarning() {
                            timeoutInterval = setTimeout(function() {
                                warningShown = true;
                                let countdown = WARNING_BEFORE;
                                $("#timeout-countdown").text(countdown);
                                $("#modal-session-timeout").modal({
                                    backdrop: 'static',
                                    keyboard: false
                                });
                                countdownInterval = setInterval(function() {
                                    countdown--;
                                    $("#timeout-countdown").text(countdown);
                                    if (countdown <= 0) {
                                        clearInterval(countdownInterval);
                                        window.location.href = "<?= base_url('auth/logout') ?>";
                                    }
                                }, 1000);
                            }, (SESSION_TIMEOUT - WARNING_BEFORE) * 1000);
                        }

                        $(document).ready(function() {
                            startSessionWarning();
                            $(document).on('click keydown mousemove', function() {
                                if (warningShown) {
                                    // Perpanjang sesi dengan reload halaman (atau bisa AJAX ping ke server)
                                    window.location.reload();
                                } else {
                                    resetSessionWarning();
                                }
                            });
                            $('#modal-session-timeout').on('hide.bs.modal', function() {
                                resetSessionWarning();
                            });
                        });
                    </script>