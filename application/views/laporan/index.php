<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chart-bar mr-2"></i>Laporan Transaksi Harian
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Laporan</li>
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
                <form method="GET" action="<?= base_url('laporan') ?>" class="form-inline justify-content-center">
                    <div class="form-group mr-3">
                        <label for="tanggal" class="mr-2 font-weight-bold text-gray-700">Pilih Tanggal:</label>
                        <input type="date"
                            class="form-control form-control-sm"
                            id="tanggal"
                            name="tanggal"
                            value="<?= $tanggal ?>"
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
    </div>

    <!-- Tambahan: Total Saldo Jajan & Tunai -->
    <div class="row mb-3">
        <div class="col-xl-6 col-md-6 mb-3">
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
        <div class="col-xl-6 col-md-6 mb-3">
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
                        <i class="fas fa-chart-area mr-1"></i> Grafik Transaksi per Jam
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
                <a href="<?= base_url('laporan/export_pdf_harian') ?>?tanggal=<?= $tanggal ?><?= $this->input->get('kantin') ? '&kantin=' . $this->input->get('kantin') : '' ?>"
                    class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
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
                            <th>Waktu</th>
                            <th>Nama</th>
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
                                <td><?= date('H:i', strtotime($row['created_at'])) ?></td>
                                <td class="font-weight-bold"><?= $row['nama_pelanggan'] ?></td>
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
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Tidak ada transaksi pada tanggal <?= date('d/m/Y', strtotime($tanggal)) ?></h5>
                    <p class="text-gray-400">Silakan pilih tanggal lain untuk melihat laporan transaksi</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($transaksi) && !empty($transaksi)): ?>
    <script>
        // Debug data chart
        console.log('Chart Labels:', <?= json_encode($chart_labels ?? []) ?>);
        console.log('Chart Data:', <?= json_encode($chart_data ?? []) ?>);
        console.log('Pie Labels:', <?= json_encode($pie_labels ?? []) ?>);
        console.log('Pie Data:', <?= json_encode($pie_data ?? []) ?>);

        // Area Chart
        var ctx = document.getElementById("myAreaChart");
        if (ctx) {
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chart_labels ?? []) ?>,
                    datasets: [{
                        label: "Transaksi",
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
                        data: <?= json_encode($chart_data ?? []) ?>,
                    }],
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
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value, index, values) {
                                    return number_format(value);
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
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
                                return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
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
                    labels: <?= json_encode($pie_labels ?? []) ?>,
                    datasets: [{
                        data: <?= json_encode($pie_data ?? []) ?>,
                        backgroundColor: [
                            '#4e73df', // Biru
                            '#1cc88a', // Hijau
                            '#36b9cc', // Cyan
                            '#f6c23e', // Kuning
                            '#e74a3b', // Merah
                            '#6f42c1', // Ungu
                            '#fd7e14', // Orange
                            '#20c9a6', // Teal
                            '#ffc107', // Amber
                            '#dc3545' // Red
                        ],
                        hoverBackgroundColor: [
                            '#2e59d9', // Biru gelap
                            '#17a673', // Hijau gelap
                            '#2c9faf', // Cyan gelap
                            '#f4b619', // Kuning gelap
                            '#e02424', // Merah gelap
                            '#5a2d91', // Ungu gelap
                            '#e55a00', // Orange gelap
                            '#1a9f8a', // Teal gelap
                            '#e0a800', // Amber gelap
                            '#c82333' // Red gelap
                        ],
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
                        displayColors: true,
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var total = dataset.data.reduce(function(previousValue, currentValue) {
                                    return previousValue + currentValue;
                                });
                                var currentValue = dataset.data[tooltipItem.index];
                                var percentage = Math.round(((currentValue / total) * 100) + 0.5);
                                return data.labels[tooltipItem.index] + ': ' + currentValue + ' pcs (' + percentage + '%)';
                            }
                        }
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                },
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
                    s[0] = s[0].padStart(s[0].length + (3 - i % 3), ' ');
                }
                s[0] = s[0].replace(/\B(?=(\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }
    </script>
<?php endif; ?>