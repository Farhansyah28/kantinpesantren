<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard E-Kantin DEM</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
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

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-tachometer-alt mr-2 text-primary"></i>Dashboard Admin
                </h1>
                <div class="d-flex">
                    <span class="badge badge-primary badge-pill mr-2">
                        <i class="fas fa-calendar-day mr-1"></i><?= date('d F Y') ?>
                    </span>
                    <span class="badge badge-success badge-pill" id="realtime-clock">
                        <i class="fas fa-clock mr-1"></i><?= date('H:i:s') ?>
                    </span>
                </div>
            </div>

            <!-- Statistik Ringkas -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4 d-flex align-items-stretch">
                    <div class="card border-left-primary shadow h-100 py-2 w-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Santri
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= isset($total_santri) ? $total_santri : 0 ?> orang
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4 d-flex align-items-stretch">
                    <div class="card border-left-success shadow h-100 py-2 w-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Ustadz/Ustadzah
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= isset($total_ustadz) ? $total_ustadz : 0 ?> orang
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4 d-flex align-items-stretch">
                    <div class="card border-left-info shadow h-100 py-2 w-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Transaksi Keseluruhan
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= isset($total_transaksi_keseluruhan) ? $total_transaksi_keseluruhan : 0 ?> transaksi
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4 d-flex align-items-stretch">
                    <div class="card border-left-warning shadow h-100 py-2 w-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Total Saldo Jajan Keseluruhan
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Rp <?= isset($total_saldo_keseluruhan) ? number_format($total_saldo_keseluruhan, 0, ',', '.') : '0' ?>
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

            <!-- Card Transaksi Kantin Putra & Putri Hari Ini -->
            <div class="row mb-4">
                <div class="col-xl-6 col-md-12 mb-4 d-flex align-items-stretch">
                    <div class="card border-left-primary shadow h-100 py-2 w-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Transaksi Kantin Putra Hari Ini
                                    </div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800">
                                        <?= isset($transaksi_hari_ini_putra) ? $transaksi_hari_ini_putra : 0 ?> transaksi
                                    </div>
                                    <div class="mt-2">
                                        <span class="d-block text-xs text-gray-600">Total: <strong>Rp <?= number_format($pendapatan_hari_ini_putra ?? 0, 0, ',', '.') ?></strong></span>
                                        <span class="d-block text-xs text-success">Tunai: <strong>Rp <?= number_format($pendapatan_tunai_putra ?? 0, 0, ',', '.') ?></strong></span>
                                        <span class="d-block text-xs text-info">Saldo Jajan: <strong>Rp <?= number_format($pendapatan_saldo_putra ?? 0, 0, ',', '.') ?></strong></span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-male fa-3x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-md-12 mb-4 d-flex align-items-stretch">
                    <div class="card border-left-pink shadow h-100 py-2 w-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-pink text-uppercase mb-1">
                                        Transaksi Kantin Putri Hari Ini
                                    </div>
                                    <div class="h3 mb-0 font-weight-bold text-gray-800">
                                        <?= isset($transaksi_hari_ini_putri) ? $transaksi_hari_ini_putri : 0 ?> transaksi
                                    </div>
                                    <div class="mt-2">
                                        <span class="d-block text-xs text-gray-600">Total: <strong>Rp <?= number_format($pendapatan_hari_ini_putri ?? 0, 0, ',', '.') ?></strong></span>
                                        <span class="d-block text-xs text-success">Tunai: <strong>Rp <?= number_format($pendapatan_tunai_putri ?? 0, 0, ',', '.') ?></strong></span>
                                        <span class="d-block text-xs text-info">Saldo Jajan: <strong>Rp <?= number_format($pendapatan_saldo_putri ?? 0, 0, ',', '.') ?></strong></span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-female fa-3x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Utama -->
            <!-- CARD KECIL DIHAPUS -->

            <!-- Statistik Hari Ini -->
            <!-- CARD KECIL DIHAPUS -->

            <!-- Statistik Per Kantin -->
            <?php if (isset($kantin_stats) && $kantin_stats): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-bar mr-1"></i> Statistik Per Kantin
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
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
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-line mr-2"></i>Transaksi 7 Hari Terakhir
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-area">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Transaksi Terakhir (Semua Kantin) -->
            <?php
            // Grouping transaksi per nota (per waktu, nama_pelanggan, nama_kantin)
            $grouped = [];
            if (isset($transaksi_terakhir) && $transaksi_terakhir) {
                foreach ($transaksi_terakhir as $t) {
                    $key = $t->created_at . '|' . $t->nama_pelanggan . '|' . $t->nama_kantin;
                    if (!isset($grouped[$key])) {
                        $grouped[$key] = [
                            'created_at' => $t->created_at,
                            'nama_pelanggan' => $t->nama_pelanggan,
                            'nomor_induk' => $t->nomor_induk ?? '',
                            'kelas' => $t->kelas ?? '',
                            'nama_kantin' => $t->nama_kantin,
                            'jenis_kantin' => $t->jenis_kantin ?? '',
                            'menu_list' => [],
                            'qty_total' => 0,
                            'total' => 0,
                            'status' => $t->status
                        ];
                    }
                    $grouped[$key]['menu_list'][] = $t->nama_menu . ' x' . $t->jumlah;
                    $grouped[$key]['qty_total'] += $t->jumlah;
                    $grouped[$key]['total'] += $t->total_harga;
                }
            }
            ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-1"></i> Transaksi Terakhir (Semua Kantin)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="12%">Tanggal</th>
                                    <th width="20%">Santri</th>
                                    <th width="15%">Kantin</th>
                                    <th width="20%">Menu</th>
                                    <th width="8%">Qty</th>
                                    <th width="12%" class="text-right">Total Harga</th>
                                    <th width="8%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($grouped)): ?>
                                    <?php foreach ($grouped as $g): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <div class="font-weight-bold">
                                                    <?= date('d/m/Y', strtotime($g['created_at'])) ?>
                                                </div>
                                                <div class="text-muted">
                                                    <small><?= date('H:i', strtotime($g['created_at'])) ?></small>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="font-weight-bold"><?= $g['nama_pelanggan'] ?? '-' ?></div>
                                                <?php if (!empty($g['nomor_induk'])): ?>
                                                    <div class="text-muted">
                                                        <small>
                                                            <i class="fas fa-id-card mr-1"></i>
                                                            <?= $g['nomor_induk'] ?>
                                                            <?php if (!empty($g['kelas'])): ?>
                                                                <span class="badge badge-info ml-1"><?= $g['kelas'] ?></span>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <div class="font-weight-bold"><?= $g['nama_kantin'] ?? '-' ?></div>
                                                <div class="text-muted">
                                                    <small><?= ucfirst($g['jenis_kantin'] ?? '-') ?></small>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div><?= implode(', ', $g['menu_list']) ?></div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?= $g['qty_total'] ?> pcs
                                            </td>
                                            <td class="text-right align-middle">
                                                <div class="font-weight-bold text-success">
                                                    Rp <?= number_format($g['total'], 0, ',', '.') ?>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php if ($g['status'] == 'selesai'): ?>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check mr-1"></i> Selesai
                                                    </span>
                                                <?php elseif ($g['status'] == 'pending'): ?>
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
                                Menampilkan <?= isset($grouped) ? count($grouped) : 0 ?> transaksi terakhir
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    /* Hapus custom card-table dan card-chart agar style SB Admin 2 */
    .card-table,
    .card-chart,
    .card-table .card-header,
    .card-chart .card-header,
    .card-table .card-title,
    .card-chart .card-title {
        all: unset;
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
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        <?php endif; ?>
    });
</script>

<!-- Script untuk Jam Real-time -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = hours + ':' + minutes + ':' + seconds;
            const clockElement = document.getElementById('realtime-clock');
            if (clockElement) {
                clockElement.innerHTML = '<i class="fas fa-clock mr-1"></i>' + timeString;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    });
</script>