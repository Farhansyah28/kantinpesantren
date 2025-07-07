<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard Admin E-Kantin
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard Admin</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- OVERVIEW STATISTICS -->
            <div class="row">
                <div class="col-12">
                    <h4 class="text-primary mb-3">
                        <i class="fas fa-chart-pie mr-1"></i>Overview Statistik
                    </h4>
                </div>
            </div>

            <!-- Info Boxes -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Santri
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= isset($total_santri) ? $total_santri : 0 ?> orang
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-male mr-1"></i><?= isset($santri_putra) ? $santri_putra : 0 ?> Putra
                                        <i class="fas fa-female ml-2 mr-1"></i><?= isset($santri_putri) ? $santri_putri : 0 ?> Putri
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Menu
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= isset($total_menu) ? $total_menu : 0 ?> menu
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-store mr-1"></i><?= isset($menu_aktif) ? $menu_aktif : 0 ?> Aktif
                                        <i class="fas fa-exclamation-triangle ml-2 mr-1"></i><?= isset($menu_stok_habis) ? $menu_stok_habis : 0 ?> Stok Habis
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-utensils fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Total Kantin
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= isset($total_kantin) ? $total_kantin : 0 ?> kantin
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-male mr-1"></i><?= isset($kantin_putra) ? $kantin_putra : 0 ?> Putra
                                        <i class="fas fa-female ml-2 mr-1"></i><?= isset($kantin_putri) ? $kantin_putri : 0 ?> Putri
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-store fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Saldo Jajan
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp <?= number_format($total_saldo_jajan ?? 0, 0, ',', '.') ?>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-chart-line mr-1"></i>Rata-rata Rp <?= number_format($rata_saldo_jajan ?? 0, 0, ',', '.') ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wallet fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FINANCIAL STATISTICS -->
            <div class="row">
                <div class="col-12">
                    <h4 class="text-success mb-3">
                        <i class="fas fa-dollar-sign mr-1"></i>Statistik Keuangan
                    </h4>
                </div>
            </div>

            <!-- Statistik Hari Ini -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger elevation-1">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Transaksi Hari Ini</span>
                            <span class="info-box-number">
                                <?= isset($transaksi_hari_ini) ? $transaksi_hari_ini : 0 ?> transaksi
                            </span>
                            <div class="progress">
                                <div class="progress-bar bg-danger" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <i class="fas fa-clock mr-1"></i>Rata-rata <?= isset($rata_transaksi_harian) ? $rata_transaksi_harian : 0 ?>/hari
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pendapatan Hari Ini</span>
                            <span class="info-box-number">
                                Rp <?= number_format($pendapatan_hari_ini ?? 0, 0, ',', '.') ?>
                            </span>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <i class="fas fa-chart-line mr-1"></i>Rata-rata Rp <?= number_format($rata_pendapatan_harian ?? 0, 0, ',', '.') ?>/hari
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1">
                            <i class="fas fa-chart-line"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Keuntungan Hari Ini</span>
                            <span class="info-box-number">
                                Rp <?= number_format($keuntungan_hari_ini ?? 0, 0, ',', '.') ?>
                            </span>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <i class="fas fa-percentage mr-1"></i>Margin <?= isset($margin_keuntungan) ? $margin_keuntungan : 0 ?>%
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary elevation-1">
                            <i class="fas fa-user-shield"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Admin/Operator</span>
                            <span class="info-box-number">
                                <?= isset($total_users) ? $total_users : 0 ?> user
                            </span>
                            <div class="progress">
                                <div class="progress-bar bg-secondary" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <i class="fas fa-user-tie mr-1"></i><?= isset($admin_count) ? $admin_count : 0 ?> Admin
                                <i class="fas fa-user-cog ml-2 mr-1"></i><?= isset($operator_count) ? $operator_count : 0 ?> Operator
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KANTIN PERFORMANCE -->
            <div class="row">
                <div class="col-12">
                    <h4 class="text-warning mb-3">
                        <i class="fas fa-trophy mr-1"></i>Performa Kantin
                    </h4>
                </div>
            </div>

            <!-- Info Boxes Row 3 -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-purple elevation-1">
                            <i class="fas fa-fire"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Menu Terlaris</span>
                            <span class="info-box-number">
                                <?= isset($menu_terlaris) ? $menu_terlaris : '-' ?>
                            </span>
                            <div class="progress">
                                <div class="progress-bar bg-purple" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <i class="fas fa-shopping-cart mr-1"></i><?= isset($penjualan_terlaris) ? $penjualan_terlaris : 0 ?> terjual
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-teal elevation-1">
                            <i class="fas fa-star"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Kantin Terbaik</span>
                            <span class="info-box-number">
                                <?= isset($kantin_terbaik) ? $kantin_terbaik : '-' ?>
                            </span>
                            <div class="progress">
                                <div class="progress-bar bg-teal" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <i class="fas fa-dollar-sign mr-1"></i>Rp <?= number_format($pendapatan_kantin_terbaik ?? 0, 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-indigo elevation-1">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Jam Puncak</span>
                            <span class="info-box-number">
                                <?= isset($jam_puncak) ? $jam_puncak : '-' ?>
                            </span>
                            <div class="progress">
                                <div class="progress-bar bg-indigo" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <i class="fas fa-chart-bar mr-1"></i><?= isset($transaksi_jam_puncak) ? $transaksi_jam_puncak : 0 ?> transaksi
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-pink elevation-1">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Stok Menipis</span>
                            <span class="info-box-number">
                                <?= isset($stok_menipis) ? $stok_menipis : 0 ?> menu
                            </span>
                            <div class="progress">
                                <div class="progress-bar bg-pink" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                <i class="fas fa-boxes mr-1"></i><?= isset($stok_habis) ? $stok_habis : 0 ?> habis
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Per Kantin -->
            <?php if (isset($kantin_stats) && $kantin_stats): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-header bg-white border-left-info shadow-sm">
                                <h3 class="card-title text-info mb-0">
                                    <i class="fas fa-chart-bar mr-1"></i> Statistik Per Kantin
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Nama Kantin</th>
                                                <th class="text-center">Total Menu</th>
                                                <th class="text-center">Total Transaksi</th>
                                                <th class="text-right">Total Pendapatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($kantin_stats as $kantin): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= $kantin->nama ?></strong>
                                                        <br><small class="text-muted"><?= ucfirst($kantin->jenis) ?></small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-info"><?= $kantin->total_menu ?? 0 ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-secondary"><?= $kantin->total_transaksi ?? 0 ?></span>
                                                    </td>
                                                    <td class="text-right">
                                                        <strong class="text-success">Rp <?= number_format($kantin->total_pendapatan ?? 0, 0, ',', '.') ?></strong>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Grafik Transaksi 7 Hari Terakhir -->
            <?php if (isset($transaksi_harian) && $transaksi_harian): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-gradient-success">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-area mr-1"></i> Grafik Pendapatan 7 Hari Terakhir
                                </h3>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Transaksi Terakhir -->
            <div class="card">
                <div class="card-header border-left-primary bg-white shadow-sm">
                    <h3 class="card-title text-primary mb-0">
                        <i class="fas fa-history mr-1"></i> Transaksi Terakhir (Semua Kantin)
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th width="12%">Tanggal</th>
                                    <th width="20%">Pelanggan</th>
                                    <th width="15%">Kantin</th>
                                    <th width="15%">Menu</th>
                                    <th width="8%">Jumlah</th>
                                    <th width="12%" class="text-right">Total Harga</th>
                                    <th width="8%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($transaksi_terakhir) && $transaksi_terakhir): ?>
                                    <?php foreach ($transaksi_terakhir as $t): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <div class="font-weight-bold">
                                                    <?= date('d/m/Y', strtotime($t->created_at)) ?>
                                                </div>
                                                <div class="text-muted">
                                                    <small><?= date('H:i', strtotime($t->created_at)) ?></small>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="font-weight-bold"><?= $t->nama_pelanggan ?? '-' ?></div>
                                                <div class="text-muted">
                                                    <small>
                                                        <span class="badge badge-<?= ($t->jenis == 'ustadz') ? 'warning' : 'primary' ?>">
                                                            <?= ucfirst($t->jenis) ?>
                                                        </span>
                                                        <?php if ($t->jenis == 'santri' && isset($t->nomor_induk) && $t->nomor_induk): ?>
                                                            <span class="ml-2"><i class="fas fa-id-card mr-1"></i><?= $t->nomor_induk ?></span>
                                                            <?php if (isset($t->kelas) && $t->kelas): ?>
                                                                <span class="badge badge-info ml-1"><?= $t->kelas ?></span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="font-weight-bold"><?= $t->nama_kantin ?? '-' ?></div>
                                                <div class="text-muted">
                                                    <small><?= ucfirst($t->jenis_kantin ?? '-') ?></small>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="font-weight-bold"><?= $t->nama_menu ?? '-' ?></div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-secondary">
                                                    <?= $t->jumlah ?? 0 ?> pcs
                                                </span>
                                            </td>
                                            <td class="text-right align-middle">
                                                <div class="font-weight-bold text-success">
                                                    Rp <?= number_format($t->total_harga ?? 0, 0, ',', '.') ?>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php $status = $t->status ?? 'selesai'; ?>
                                                <?php if (strtolower($status) == 'selesai'): ?>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check mr-1"></i> Selesai
                                                    </span>
                                                <?php elseif (strtolower($status) == 'pending'): ?>
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-clock mr-1"></i> Pending
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times mr-1"></i> Batal
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada transaksi terakhir</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-gradient-light">
                    <div class="row">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info" role="status" aria-live="polite">
                                Menampilkan <?= isset($transaksi_terakhir) ? count($transaksi_terakhir) : 0 ?> transaksi terakhir
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="float-sm-right">
                                <a href="<?= base_url('pos') ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-cash-register mr-1"></i> POS Kantin
                                </a>
                                <a href="<?= base_url('pos/riwayat_hari_ini') ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-history mr-1"></i> Riwayat Hari Ini
                                </a>
                                <a href="<?= base_url('laporan') ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-chart-bar mr-1"></i> Laporan Transaksi
                                </a>
                                <a href="<?= base_url('menu') ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-utensils mr-1"></i> Kelola Menu
                                </a>
                                <a href="<?= base_url('santri') ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-users mr-1"></i> Kelola Santri
                                </a>
                                <a href="<?= base_url('users') ?>" class="btn btn-dark btn-sm">
                                    <i class="fas fa-user-shield mr-1"></i> Kelola User
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .info-box {
        min-height: 90px;
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        border-radius: 0.25rem;
        background-color: #fff;
        display: flex;
        margin-bottom: 1rem;
        position: relative;
        width: 100%;
    }

    .info-box .info-box-icon {
        border-radius: 0.25rem 0 0 0.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70px;
        font-size: 1.875rem;
    }

    .info-box .info-box-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.8;
        flex: 1;
        padding: 0 10px;
    }

    .info-box .info-box-text {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .info-box .info-box-number {
        display: block;
        font-weight: 700;
    }

    .info-box .progress {
        height: 3px;
        margin: 5px 0;
    }

    .info-box .progress-description {
        display: block;
        font-size: 0.875rem;
        color: #6c757d;
    }

    .table th {
        background-color: #f8f9fa;
        border-top: none;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.85em;
        padding: 0.4em 0.6em;
    }

    .bg-purple {
        background-color: #6f42c1 !important;
    }

    .bg-teal {
        background-color: #20c997 !important;
    }

    .bg-indigo {
        background-color: #6610f2 !important;
    }

    .bg-pink {
        background-color: #e83e8c !important;
    }
</style>

<script>
    $(document).ready(function() {
        // Inisialisasi tooltip
        $('[data-toggle="tooltip"]').tooltip();

        // Grafik Pendapatan 7 Hari Terakhir
        <?php if (isset($transaksi_harian) && $transaksi_harian): ?>
            var canvas = document.getElementById('revenueChart');
            if (canvas) {
                var ctx = canvas.getContext('2d');
                var revenueChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [<?= implode(',', array_map(function ($item) {
                                        return '"' . $item['tanggal'] . '"';
                                    }, $transaksi_harian)) ?>],
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: [<?= implode(',', array_map(function ($item) {
                                        return $item['pendapatan'];
                                    }, $transaksi_harian)) ?>],
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            }
                        }
                    }
                });
            }
        <?php endif; ?>
    });
</script>