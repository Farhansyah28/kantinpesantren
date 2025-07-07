<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Selamat Datang - Sistem Kantin DEM</title>

	<!-- Custom fonts for this template-->
	<link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
	<link href="<?= base_url('assets/css/nunito-fonts.css') ?>" rel="stylesheet">
	<!-- Custom styles for this template-->
	<link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">

	<style>
		.bg-landing-image {
			background: url('<?= base_url('assets/img/pesantren-bg.jpg') ?>');
			background-position: center;
			background-size: cover;
			background-attachment: fixed;
		}

		.overlay {
			background: rgba(0, 0, 0, 0.6);
			height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.landing-content {
			text-align: center;
			color: white;
			max-width: 800px;
			padding: 2rem;
		}

		.btn-landing {
			font-size: 1.1rem;
			padding: 0.75rem 2rem;
			border-radius: 50px;
			margin: 0.5rem;
			transition: all 0.3s ease;
		}

		.btn-landing:hover {
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
		}

		.feature-icon {
			font-size: 3rem;
			margin-bottom: 1rem;
			color: #4e73df;
		}

		.features-section {
			background: white;
			padding: 4rem 0;
		}

		.feature-card {
			text-align: center;
			padding: 2rem;
			border-radius: 10px;
			box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
			margin-bottom: 2rem;
			transition: transform 0.3s ease;
		}

		.feature-card:hover {
			transform: translateY(-5px);
		}
	</style>
</head>

<body>
	<!-- Hero Section -->
	<div class="bg-landing-image">
		<div class="overlay">
			<div class="landing-content">
				<div class="mb-4">
					<img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo DEM" style="max-width: 150px; margin-bottom: 2rem;">
				</div>

				<h1 class="display-4 font-weight-bold mb-4">
					<i class="fas fa-store mr-3"></i>
					Sistem Kantin DEM
				</h1>

				<p class="lead mb-5">
					Platform manajemen kantin pesantren yang terintegrasi untuk memudahkan
					pengelolaan transaksi, tabungan santri, dan administrasi kantin.
				</p>

				<div class="mb-5">
					<a href="<?= base_url('auth/login') ?>" class="btn btn-primary btn-landing">
						<i class="fas fa-sign-in-alt mr-2"></i>
						Login ke Sistem
					</a>

					<a href="<?= base_url('auth/register') ?>" class="btn btn-outline-light btn-landing">
						<i class="fas fa-user-plus mr-2"></i>
						Daftar Akun
					</a>
				</div>

				<div class="row text-center">
					<div class="col-md-4">
						<div class="mb-3">
							<i class="fas fa-users feature-icon"></i>
						</div>
						<h5>Manajemen Santri</h5>
						<p class="small">Kelola data santri dengan mudah</p>
					</div>
					<div class="col-md-4">
						<div class="mb-3">
							<i class="fas fa-wallet feature-icon"></i>
						</div>
						<h5>Sistem Tabungan</h5>
						<p class="small">Kelola tabungan santri secara digital</p>
					</div>
					<div class="col-md-4">
						<div class="mb-3">
							<i class="fas fa-cash-register feature-icon"></i>
						</div>
						<h5>Point of Sale</h5>
						<p class="small">Transaksi cepat dan akurat</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Features Section -->
	<section class="features-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 text-center mb-5">
					<h2 class="font-weight-bold text-gray-800">Fitur Unggulan</h2>
					<p class="text-muted">Sistem yang dirancang khusus untuk kebutuhan pesantren</p>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-4 col-md-6">
					<div class="feature-card">
						<i class="fas fa-user-graduate feature-icon"></i>
						<h4>Manajemen Santri</h4>
						<p class="text-muted">
							Kelola data santri, import data massal, dan monitoring kehadiran
							dengan sistem yang terintegrasi.
						</p>
					</div>
				</div>

				<div class="col-lg-4 col-md-6">
					<div class="feature-card">
						<i class="fas fa-piggy-bank feature-icon"></i>
						<h4>Sistem Tabungan</h4>
						<p class="text-muted">
							Kelola tabungan santri dengan fitur setoran, penarikan,
							dan transfer antar santri.
						</p>
					</div>
				</div>

				<div class="col-lg-4 col-md-6">
					<div class="feature-card">
						<i class="fas fa-shopping-cart feature-icon"></i>
						<h4>Point of Sale</h4>
						<p class="text-muted">
							Transaksi cepat dengan sistem POS yang user-friendly
							dan laporan real-time.
						</p>
					</div>
				</div>

				<div class="col-lg-4 col-md-6">
					<div class="feature-card">
						<i class="fas fa-boxes feature-icon"></i>
						<h4>Manajemen Stok</h4>
						<p class="text-muted">
							Monitor stok menu, tambah/kurangi stok, dan riwayat
							pergerakan stok.
						</p>
					</div>
				</div>

				<div class="col-lg-4 col-md-6">
					<div class="feature-card">
						<i class="fas fa-chart-line feature-icon"></i>
						<h4>Laporan & Analisis</h4>
						<p class="text-muted">
							Laporan transaksi, analisis penjualan, dan dashboard
							untuk monitoring kantin.
						</p>
					</div>
				</div>

				<div class="col-lg-4 col-md-6">
					<div class="feature-card">
						<i class="fas fa-shield-alt feature-icon"></i>
						<h4>Keamanan Data</h4>
						<p class="text-muted">
							Sistem keamanan yang kuat dengan role-based access
							dan activity logging.
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="bg-dark text-white text-center py-4">
		<div class="container">
			<p class="mb-0">
				&copy; <?= date('Y') ?> Sistem Kantin DEM.
				Dibuat dengan <i class="fas fa-heart text-danger"></i> untuk Pesantren DEM.
			</p>
		</div>
	</footer>

	<!-- Bootstrap core JavaScript-->
	<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
	<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
	<!-- Core plugin JavaScript-->
	<script src="<?= base_url('assets/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
	<!-- Custom scripts for all pages-->
	<script src="<?= base_url('assets/js/sb-admin-2.min.js') ?>"></script>
</body>

</html>