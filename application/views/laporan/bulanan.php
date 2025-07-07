<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chart-bar mr-2"></i>Laporan Transaksi Bulanan
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Laporan Bulanan</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-2 bg-gradient-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-calendar-alt mr-1"></i> Filter Laporan
                </h6>
            </div>
            <div class="card-body py-3">
                <form method="GET" action="<?= base_url('laporan/bulanan') ?>" class="form-inline justify-content-center">
                    <div class="form-group mr-3">
                        <label for="bulan" class="mr-2 font-weight-bold text-gray-700">Pilih Bulan:</label>
                        <input type="month"
                            class="form-control form-control-sm"
                            id="bulan"
                            name="bulan"
                            value="<?= $bulan ?>"
                            required>
                    </div>
                    <?php if (in_array($this->session->userdata('role'), ['admin', 'keuangan'])): ?>
                        <div class="form-group mr-3">
                            <label for="kantin" class="mr-2 font-weight-bold text-gray-700">Kantin:</label>
                            <select class="form-control form-control-sm" id="kantin" name="kantin">
                                <option value="">Semua Kantin</option>
                                <option value="putra" <?= ($this->input->get('kantin') === 'putra') ? 'selected' : '' ?>>Kantin Putra</option>
                                <option value="putri" <?= ($this->input->get('kantin') === 'putri') ? 'selected' : '' ?>>Kantin Putri</option>
                            </select>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search mr-1"></i> Tampilkan Laporan
                    </button>
                </form>
                <div class="text-center mt-2">
                    <small class="text-muted">
                        Periode: <?= date('d/m/Y', strtotime($tanggal_awal)) ?> - <?= date('d/m/Y', strtotime($tanggal_akhir)) ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($transaksi) && !empty($transaksi)): ?>
    <div class="row mt-3">
        <!-- Ringkasan -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Transaksi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($transaksi) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Item Terjual</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_item ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Rata-rata per Transaksi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($rata_rata, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Saldo Jajan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($total_saldo_jajan, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tunai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($total_tunai, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-2 bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-chart-area mr-1"></i> Grafik Transaksi per Hari
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-2 bg-gradient-success">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-chart-pie mr-1"></i> Top Menu Terjual
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <?php if (isset($top_menu) && !empty($top_menu)): ?>
                            <?php
                            $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#fd7e14', '#20c9a6', '#ffc107', '#dc3545'];
                            $i = 0;
                            foreach ($top_menu as $menu):
                            ?>
                                <span class="mr-2">
                                    <i class="fas fa-circle" style="color: <?= $colors[$i] ?>"></i> <?= $menu->nama_menu ?>
                                </span>
                            <?php
                                $i++;
                            endforeach;
                            ?>
                        <?php else: ?>
                            <span class="text-muted">Tidak ada data menu terjual</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Detail -->
    <div class="card shadow mb-4">
        <div class="card-header py-2 bg-gradient-info d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-table mr-1"></i> Detail Transaksi
            </h6>
            <div>
            </div>
        </div>
        <div class="card-body">
            <?php
            // Grouping transaksi per nota (per waktu, nama_pelanggan, nama_kantin)
            $grouped = [];
            foreach ($transaksi as $t) {
                $key = $t->created_at . '|' . $t->nama_pelanggan . '|' . $t->nama_kantin;
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'created_at' => $t->created_at,
                        'nama_pelanggan' => $t->nama_pelanggan,
                        'nama_kantin' => $t->nama_kantin,
                        'menu_list' => [],
                        'qty_total' => 0,
                        'total' => 0
                    ];
                }
                $grouped[$key]['menu_list'][] = $t->nama_menu . ' x' . $t->jumlah;
                $grouped[$key]['qty_total'] += $t->jumlah;
                $grouped[$key]['total'] += $t->total_harga;
            }
            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Santri</th>
                            <th>Menu</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Kantin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($grouped as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                <td><?= date('H:i', strtotime($row['created_at'])) ?></td>
                                <td class="font-weight-bold"> <?= $row['nama_pelanggan'] ?> </td>
                                <td><?= implode(', ', $row['menu_list']) ?></td>
                                <td><?= $row['qty_total'] ?></td>
                                <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                                <td><?= $row['nama_kantin'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif (isset($transaksi) && empty($transaksi)): ?>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Tidak ada data transaksi</h5>
                    <p class="text-muted">Tidak ada transaksi untuk periode yang dipilih.</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    // Area Chart
    var ctx = document.getElementById("myAreaChart");
    if (ctx) {
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chart_labels) ?>,
                datasets: [{
                    label: "Pendapatan",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: <?= json_encode($chart_data) ?>
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 31
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value, index, values) {
                                return 'Rp ' + number_format(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }]
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': Rp ' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    }

    // Pie Chart
    var ctxPie = document.getElementById("myPieChart");
    if (ctxPie) {
        var myPieChart = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($pie_labels) ?>,
                datasets: [{
                    data: <?= json_encode($pie_data) ?>,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02424'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
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
            }
        });
    }

    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if ((sep.length > 0)) {
            var i = s[0].length;
            if (i % 3 !== 0) {
                i += 3 - i % 3;
            }
            s[0] = s[0].padStart(i, '0');
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((prec > 0) && (s[1].length < prec)) {
            s[1] += s[1].padEnd(prec, '0');
        }
        return s.join(dec);
    }
</script>