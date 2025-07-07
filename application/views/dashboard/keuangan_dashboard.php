<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h2 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chart-line mr-2 text-primary"></i>Dashboard Keuangan<?= isset($kantin_info->nama) ? html_escape($kantin_info->nama) : '' ?>
    </h2>
    <div class="d-flex">
        <span class="badge badge-primary badge-pill mr-2">
            <i class="fas fa-calendar-day mr-1"></i><?= date('d F Y') ?>
        </span>
        <span class="badge badge-success badge-pill" id="realtime-clock">
            <i class="fas fa-clock mr-1"></i><?= date('H:i:s') ?>
        </span>
    </div>
</div>

<!-- Card Total Pendapatan Kantin Putra & Putri -->
<div class="row mb-4">
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Pendapatan Kantin Putra</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($pendapatan_kantin_putra, 0, ',', '.') ?></div>
                        <div class="mt-2">
                            <a href="<?= base_url('dashboard/detail_kantin/putra') ?>" class="btn btn-sm btn-outline-primary" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-male fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-pink shadow h-100 py-2" style="border-left-color:#e83e8c !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-pink text-uppercase mb-1" style="color:#e83e8c !important;">
                            Total Pendapatan Kantin Putri</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($pendapatan_kantin_putri, 0, ',', '.') ?></div>
                        <div class="mt-2">
                            <a href="<?= base_url('dashboard/detail_kantin/putri') ?>" class="btn btn-sm btn-outline-pink" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; border-color: #e83e8c; color: #e83e8c;">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-female fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Utama -->
<div class="row mb-4">
    <!-- Total Transaksi Hari Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Transaksi Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $transaksi_hari_ini ?></div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-arrow-up text-success mr-1"></i>Semua jenis pembayaran
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendapatan Hari Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Pendapatan Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($pendapatan_hari_ini, 0, ',', '.') ?></div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-coins text-warning mr-1"></i>Tunai + Saldo Jajan
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keuntungan Hari Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Keuntungan Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($keuntungan_hari_ini, 0, ',', '.') ?></div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-chart-line text-info mr-1"></i>Selisih harga jual - beli
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo Terpotong -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Saldo Terpotong Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($total_saldo_terpotong, 0, ',', '.') ?></div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-wallet text-warning mr-1"></i>Dari saldo jajan santri
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

<!-- Statistik Pembayaran -->
<div class="row mb-4">
    <!-- Transaksi Tunai -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 bg-gradient-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-money-bill-wave mr-2"></i>Transaksi Tunai Hari Ini
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 font-weight-bold text-primary"><?= $total_transaksi_tunai ?></div>
                            <div class="text-xs text-muted">Total Transaksi</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 font-weight-bold text-success">Rp <?= number_format($total_pendapatan_tunai, 0, ',', '.') ?></div>
                            <div class="text-xs text-muted">Total Pendapatan</div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($transaksi_tunai)): ?>
                    <div class="table-responsive" style="max-height: 300px;">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Nama</th>
                                    <th>Menu</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($transaksi_tunai, 0, 5) as $tunai): ?>
                                    <tr>
                                        <td><small><?= date('H:i', strtotime($tunai['created_at'])) ?></small></td>
                                        <td>
                                            <div class="font-weight-bold"><?= $tunai['nama_pelanggan'] ?></div>
                                        </td>
                                        <td><small><?= $tunai['nama_menu'] ?></small></td>
                                        <td class="text-right font-weight-bold">Rp <?= number_format($tunai['total_harga'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($transaksi_tunai) > 5): ?>
                        <div class="text-center mt-2">
                            <small class="text-muted">Dan <?= count($transaksi_tunai) - 5 ?> transaksi lainnya</small>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada transaksi tunai hari ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Transaksi Saldo Jajan -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 bg-gradient-success">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-credit-card mr-2"></i>Transaksi Saldo Jajan Hari Ini
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 font-weight-bold text-success"><?= $total_transaksi_saldo ?></div>
                            <div class="text-xs text-muted">Total Transaksi</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 font-weight-bold text-info">Rp <?= number_format($total_pendapatan_saldo, 0, ',', '.') ?></div>
                            <div class="text-xs text-muted">Total Pendapatan</div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($transaksi_saldo)): ?>
                    <div class="table-responsive" style="max-height: 300px;">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Santri</th>
                                    <th>Menu</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($transaksi_saldo, 0, 5) as $saldo): ?>
                                    <tr>
                                        <td><small><?= date('H:i', strtotime($saldo['created_at'])) ?></small></td>
                                        <td>
                                            <div class="font-weight-bold"><?= $saldo['nama_santri'] ?></div>
                                        </td>
                                        <td><small><?= $saldo['nama_menu'] ?></small></td>
                                        <td class="text-right font-weight-bold">Rp <?= number_format($saldo['total_harga'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($transaksi_saldo) > 5): ?>
                        <div class="text-center mt-2">
                            <small class="text-muted">Dan <?= count($transaksi_saldo) - 5 ?> transaksi lainnya</small>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada transaksi saldo jajan hari ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Saldo Terpotong Detail -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 bg-gradient-warning">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-wallet mr-2"></i>Detail Saldo Terpotong Hari Ini
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($saldo_terpotong)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Santri</th>
                                    <th>Jumlah Terpotong</th>
                                    <th>Keterangan</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($saldo_terpotong as $saldo): ?>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold"><?= date('H:i', strtotime($saldo['created_at'])) ?></div>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($saldo['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold"><?= $saldo['nama_santri'] ?></div>
                                        </td>
                                        <td>
                                            <span class="badge badge-danger badge-pill">
                                                Rp <?= number_format($saldo['jumlah'], 0, ',', '.') ?>
                                            </span>
                                        </td>
                                        <td><small><?= $saldo['keterangan'] ?></small></td>
                                        <td><small><?= $saldo['operator_nama'] ?? 'System' ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-wallet fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada saldo yang terpotong hari ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Setoran Harian dan Grafik -->
<div class="row">
    <!-- Setoran Harian -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-info">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-hand-holding-usd mr-2"></i>Setoran Harian ke Pemilik Jajanan
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($setoran_harian)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pemilik</th>
                                    <th class="text-center">Total Item Terjual</th>
                                    <th class="text-right">Jumlah Setoran (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($setoran_harian as $setoran): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td>
                                            <div class="font-weight-bold"><?= html_escape($setoran->pemilik) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info badge-pill"><?= $setoran->total_item_terjual ?> pcs</span>
                                        </td>
                                        <td class="text-right">
                                            <div class="font-weight-bold text-success">Rp <?= number_format($setoran->total_setoran, 0, ',', '.') ?></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-hand-holding-usd fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data setoran untuk hari ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Grafik Transaksi -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-secondary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-chart-area mr-2"></i>Pendapatan 7 Hari Terakhir
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-area pt-4 pb-2">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Grouping transaksi per nota (per waktu, nama_pelanggan, nama_kantin)
$grouped = [];
if (!empty($recent_transactions)) {
    foreach ($recent_transactions as $t) {
        $key = $t->created_at . '|' . ($t->nama_pelanggan ?? $t->nama_santri ?? '-') . '|' . ($t->nama_kantin ?? '-');
        if (!isset($grouped[$key])) {
            $grouped[$key] = [
                'created_at' => $t->created_at,
                'nama_pelanggan' => $t->nama_pelanggan ?? $t->nama_santri ?? '-',
                'nomor_induk' => $t->nomor_induk ?? '',
                'kelas' => $t->kelas ?? '',
                'nama_kantin' => $t->nama_kantin ?? '-',
                'jenis_kantin' => $t->jenis_kantin ?? '',
                'menu_list' => [],
                'qty_total' => 0,
                'total' => 0,
                'status' => $t->status ?? 'selesai',
                'metode_pembayaran' => $t->metode_pembayaran ?? '',
                'jenis' => $t->jenis ?? ''
            ];
        }
        $grouped[$key]['menu_list'][] = ($t->nama_menu ?? '-') . ' x' . ($t->jumlah ?? 0);
        $grouped[$key]['qty_total'] += $t->jumlah ?? 0;
        $grouped[$key]['total'] += $t->total_harga ?? 0;
    }
}
?>
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-dark">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-history mr-2"></i>10 Transaksi Terakhir
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($grouped)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Nama</th>
                                    <th>Kantin</th>
                                    <th>Menu</th>
                                    <th>Qty</th>
                                    <th class="text-right">Total</th>
                                    <th>Metode</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grouped as $g): ?>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold"><?= date('H:i', strtotime($g['created_at'])) ?></div>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($g['created_at'])) ?></small>
                                        </td>
                                        <td class="font-weight-bold">
                                            <?= $g['nama_pelanggan'] ?>
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
                                        <td>
                                            <div class="font-weight-bold"><?= $g['nama_kantin'] ?></div>
                                            <div class="text-muted"><small><?= ucfirst($g['jenis_kantin']) ?></small></div>
                                        </td>
                                        <td>
                                            <div><?= implode(', ', $g['menu_list']) ?></div>
                                        </td>
                                        <td class="text-center"><?= $g['qty_total'] ?> pcs</td>
                                        <td class="text-right">
                                            <div class="font-weight-bold text-success">Rp <?= number_format($g['total'], 0, ',', '.') ?></div>
                                        </td>
                                        <td>
                                            <?php if (($g['metode_pembayaran'] ?? '') === 'tunai' || ($g['jenis'] ?? '') === 'ustadz'): ?>
                                                <span class="badge badge-primary">Tunai</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Saldo Jajan</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada transaksi hari ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Grafik -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Grafik Pendapatan 7 Hari Terakhir
        var ctx = document.getElementById('revenueChart').getContext('2d');
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
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(75, 192, 192)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgb(75, 192, 192)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return 'Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }

    .card-header {
        border-bottom: none;
    }

    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df 10%, #224abe 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(45deg, #1cc88a 10%, #13855c 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(45deg, #36b9cc 10%, #258391 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(45deg, #f6c23e 10%, #dda20a 100%);
    }

    .bg-gradient-secondary {
        background: linear-gradient(45deg, #858796 10%, #6e707e 100%);
    }

    .bg-gradient-dark {
        background: linear-gradient(45deg, #5a5c69 10%, #373840 100%);
    }

    .table th {
        border-top: none;
        font-weight: 600;
    }

    .badge-pill {
        padding-left: 1em;
        padding-right: 1em;
    }

    .chart-area {
        position: relative;
        height: 20rem;
        width: 100%;
    }

    /* Custom button styles untuk card */
    .btn-outline-pink {
        border-color: #e83e8c;
        color: #e83e8c;
    }

    .btn-outline-pink:hover {
        background-color: #e83e8c;
        border-color: #e83e8c;
        color: white;
    }

    .btn-outline-pink:focus {
        box-shadow: 0 0 0 0.2rem rgba(232, 62, 140, 0.25);
    }

    /* Button kecil untuk card */
    .card .btn-sm {
        transition: all 0.2s ease-in-out;
    }

    .card .btn-sm:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Link button styling */
    .card .btn-sm.btn-outline-primary,
    .card .btn-sm.btn-outline-pink {
        text-decoration: none;
        display: inline-block;
    }

    .card .btn-sm.btn-outline-primary:hover,
    .card .btn-sm.btn-outline-pink:hover {
        text-decoration: none;
    }
</style>

<!-- Script untuk Jam Real-time -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Fungsi untuk update jam real-time
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = hours + ':' + minutes + ':' + seconds;

            // Update elemen jam
            const clockElement = document.getElementById('realtime-clock');
            if (clockElement) {
                // Pertahankan ikon dan update hanya waktu
                clockElement.innerHTML = '<i class="fas fa-clock mr-1"></i>' + timeString;
            }
        }

        // Update jam setiap detik
        setInterval(updateClock, 1000);

        // Update jam pertama kali
        updateClock();
    });
</script>