<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard Operator - <?= html_escape($kantin_info->nama) ?></h1>
</div>

<!-- Info Cards -->
<div class="row">
    <!-- Transaksi Hari Ini Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Transaksi Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['transaksi_hari_ini'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendapatan Hari Ini Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pendapatan Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($stats['pendapatan_hari_ini'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keuntungan Hari Ini Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Keuntungan Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($stats['keuntungan_hari_ini'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stok Habis Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Menu Stok Habis</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['stok_habis'] ?? 0 ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box-open fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Tunai yang Dipegang Saat Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tunai yang Dipegang Saat Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($tunai_saat_ini ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tabel Setoran Harian -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-hand-holding-usd mr-2"></i>Setoran Harian ke Pemilik Jajanan</h6>
                <div>
                    <a href="<?= base_url('dashboard/histori_setoran') ?>" class="btn btn-sm btn-info">
                        <i class="fas fa-history"></i> Histori Setoran
                    </a>
                    <?php if (in_array($this->session->userdata('role'), ['admin'])): ?>
                        <a href="<?= base_url('dashboard/debug_setoran_harian') ?>" class="btn btn-sm btn-warning" target="_blank">
                            <i class="fas fa-bug"></i> Debug
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <!-- Debug Information -->
                <?php if (empty($setoran_harian)): ?>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Informasi Setoran Harian:</h6>
                        <p class="mb-2">Tabel setoran harian kosong karena:</p>
                        <ul class="mb-0">
                            <li><strong>Kantin ID:</strong> <?= $kantin_id ?? 'Tidak ada' ?></li>
                            <li><strong>Tanggal:</strong> <?= date('Y-m-d') ?></li>
                            <li><strong>Total Transaksi Hari Ini:</strong> <?= $stats['transaksi_hari_ini'] ?? 0 ?></li>
                            <li><strong>Pendapatan Hari Ini:</strong> Rp <?= number_format($stats['pendapatan_hari_ini'] ?? 0, 0, ',', '.') ?></li>
                        </ul>
                        <hr class="my-2">
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-lightbulb"></i>
                            <strong>Kemungkinan penyebab:</strong><br>
                            1. Belum ada transaksi hari ini<br>
                            2. Menu yang terjual tidak memiliki data pemilik<br>
                            3. Transaksi belum selesai (status bukan 'selesai')
                        </p>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Pemilik</th>
                                <th class="text-center">Total Item Terjual</th>
                                <th class="text-right">Jumlah Setoran (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($setoran_harian)): ?>
                                <?php
                                $no = 1;
                                $total_setoran = 0;
                                $total_item = 0;
                                foreach ($setoran_harian as $setoran):
                                    $total_setoran += $setoran->total_setoran;
                                    $total_item += $setoran->total_item_terjual;
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= html_escape($setoran->pemilik) ?></td>
                                        <td class="text-center"><?= $setoran->total_item_terjual ?></td>
                                        <td class="text-right font-weight-bold">Rp <?= number_format($setoran->total_setoran, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data setoran untuk hari ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($setoran_harian)): ?>
                            <tfoot>
                                <tr class="table-info font-weight-bold">
                                    <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                                    <td class="text-center"><strong><?= $total_item ?></strong></td>
                                    <td class="text-right"><strong>Rp <?= number_format($total_setoran, 0, ',', '.') ?></strong></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="mt-2 text-muted small">
                    * Tabel ini berisi rincian uang yang harus disetorkan ke setiap pemilik berdasarkan total item terjual dikalikan harga beli.
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart Stok -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Status Stok Menu</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="stokPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2"><i class="fas fa-circle text-success"></i> Aman</span>
                    <span class="mr-2"><i class="fas fa-circle text-warning"></i> Menipis</span>
                    <span class="mr-2"><i class="fas fa-circle text-danger"></i> Habis</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 5 Transaksi Terakhir -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">5 Transaksi Terakhir di Kantin Anda</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Nama</th>
                                <th>Jumlah</th>
                                <th class="text-right">Total</th>
                                <th>Metode Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_transactions)): ?>
                                <?php foreach ($recent_transactions as $tx): ?>
                                    <tr>
                                        <td><?= date('H:i:s', strtotime($tx->created_at)) ?></td>
                                        <td class="font-weight-bold"><?= $tx->nama_pelanggan ?></td>
                                        <td><?= $tx->jumlah ?></td>
                                        <td class="text-right">Rp <?= number_format($tx->total_harga, 0, ',', '.') ?></td>
                                        <td>
                                            <?php if (($tx->metode_pembayaran ?? '') === 'tunai' || ($tx->jenis ?? '') === 'ustadz'): ?>
                                                <span class="badge badge-primary">Tunai</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Saldo Jajan</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada transaksi hari ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-right mt-2">
                    <a href="<?= base_url('pos/riwayat_hari_ini') ?>">Lihat Semua Transaksi Hari Ini &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Pie Chart -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Pie Chart
        var ctx = document.getElementById("stokPieChart");
        var stokPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Aman", "Menipis", "Habis"],
                datasets: [{
                    data: [<?= $stats['stok_aman'] ?? 0 ?>, <?= $stats['stok_menipis'] ?? 0 ?>, <?= $stats['stok_habis'] ?? 0 ?>],
                    backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#17a673', '#f4b619', '#e02d1b'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 80,
            },
        });
    });
</script>