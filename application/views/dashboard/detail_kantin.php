<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-store mr-2 text-primary"></i>Detail Kantin <?= ucfirst($jenis_kantin) ?>
        <?php if(isset($kantin_info->nama)): ?>
            <span class="text-muted">- <?= html_escape($kantin_info->nama) ?></span>
        <?php endif; ?>
    </h1>
    <div class="d-flex">
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </a>
        <span class="badge badge-primary badge-pill mr-2">
            <i class="fas fa-calendar-day mr-1"></i><?= date('d F Y') ?>
        </span>
        <span class="badge badge-success badge-pill" id="realtime-clock">
            <i class="fas fa-clock mr-1"></i><?= date('H:i:s') ?>
        </span>
    </div>
</div>

<?php if($kantin_info): ?>
<!-- Statistik Utama -->
<div class="row mb-4">
    <!-- Total Pendapatan -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Pendapatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-chart-line text-primary mr-1"></i>Semua waktu
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Hari Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Transaksi Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $transaksi_hari_ini ?></div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-receipt text-success mr-1"></i>Total transaksi
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendapatan Hari Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Pendapatan Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($pendapatan_hari_ini, 0, ',', '.') ?></div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-coins text-info mr-1"></i>Hari ini
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Menu -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Menu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_menu ?></div>
                        <div class="text-xs text-muted mt-1">
                            <i class="fas fa-utensils text-warning mr-1"></i>Menu tersedia
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-utensils fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Tunai yang Dipegang Hari Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tunai yang Dipegang Hari Ini</div>
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

<!-- Grafik dan Menu Terlaris -->
<div class="row mb-4">
    <!-- Grafik Transaksi 7 Hari Terakhir -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-primary">
                <h6 class="m-0 font-weight-bold text-white">
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

    <!-- Menu Terlaris -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-success">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-star mr-2"></i>Menu Terlaris
                </h6>
            </div>
            <div class="card-body">
                <?php if(!empty($menu_terlaris)): ?>
                    <div class="table-responsive" style="max-height: 300px;">
                        <table class="table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Menu</th>
                                    <th class="text-center">Terjual</th>
                                    <th class="text-right">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($menu_terlaris as $menu): ?>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold"><?= $menu->nama_menu ?></div>
                                            <small class="text-muted"><?= $menu->pemilik ?></small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary"><?= $menu->total_terjual ?></span>
                                        </td>
                                        <td class="text-right">
                                            <small class="font-weight-bold text-success">
                                                Rp <?= number_format($menu->total_pendapatan, 0, ',', '.') ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data menu terlaris</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Transaksi Terakhir -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-dark">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-history mr-2"></i>Transaksi Terakhir
                </h6>
            </div>
            <div class="card-body">
                <?php if(!empty($recent_transactions)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Nama</th>
                                    <th>Menu</th>
                                    <th>Jumlah</th>
                                    <th class="text-right">Total</th>
                                    <th>Metode</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_transactions as $tx): ?>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold"><?= date('H:i', strtotime($tx->created_at)) ?></div>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($tx->created_at)) ?></small>
                                        </td>
                                        <td class="font-weight-bold"><?= $tx->nama_pelanggan ?></td>
                                        <td><?= $tx->nama_menu ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary"><?= $tx->jumlah ?> pcs</span>
                                        </td>
                                        <td class="text-right">
                                            <div class="font-weight-bold text-success">Rp <?= number_format($tx->total_harga, 0, ',', '.') ?></div>
                                        </td>
                                        <td>
                                            <?php if (($tx->metode_pembayaran ?? '') === 'tunai' || ($tx->jenis ?? '') === 'ustadz'): ?>
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
                        <p class="text-muted">Belum ada transaksi di kantin ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Kantin tidak ditemukan -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-store fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Kantin <?= ucfirst($jenis_kantin) ?> Tidak Ditemukan</h4>
                <p class="text-muted">Data kantin untuk jenis <?= ucfirst($jenis_kantin) ?> belum tersedia.</p>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Script untuk Grafik -->
<?php if(isset($transaksi_harian) && !empty($transaksi_harian)): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Grafik Pendapatan 7 Hari Terakhir
    var ctx = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?= implode(',', array_map(function($item) { return '"' . $item['tanggal'] . '"'; }, $transaksi_harian)) ?>],
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: [<?= implode(',', array_map(function($item) { return $item['pendapatan']; }, $transaksi_harian)) ?>],
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
<?php endif; ?>

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
</style> 