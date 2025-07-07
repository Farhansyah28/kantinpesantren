<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-clock mr-2"></i>Riwayat Transaksi Hari Ini
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('pos') ?>">POS Kantin</a></li>
        <li class="breadcrumb-item active">Riwayat Hari Ini</li>
    </ol>
</div>

<!-- Flash Messages -->
<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Statistik Hari Ini -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Transaksi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_transaksi ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                            Total Pendapatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Total Keuntungan Hari Ini -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Total Keuntungan Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp <?= number_format($total_keuntungan, 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-coins fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Item Terjual</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_item ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
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
                            Tanggal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= date('d/m/Y', strtotime($tanggal)) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Transaksi -->
<div class="card shadow mb-4">
    <div class="card-header bg-gradient-primary">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-list mr-1"></i> Daftar Transaksi Hari Ini
        </h6>
    </div>
    <div class="card-body">
        <?php if ($grouped_transaksi): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Waktu</th>
                            <th>Pelanggan</th>
                            <th>Kantin</th>
                            <th>Menu</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Total</th>
                            <th>Metode</th>
                            <th>Petugas</th>
                            <th width="10%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($grouped_transaksi as $g): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td>
                                    <div class="font-weight-bold"><?= date('d/m/Y', strtotime($g['created_at'])) ?></div>
                                    <small><?= date('H:i', strtotime($g['created_at'])) ?></small>
                                </td>
                                <td class="font-weight-bold">
                                    <?= $g['nama_pelanggan'] ?>
                                    <?php if (!empty($g['jenis_pelanggan'])): ?>
                                        <span class="badge badge-secondary ml-1"><?= ucfirst($g['jenis_pelanggan']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $g['nama_kantin'] ?></td>
                                <td><?= implode(', ', $g['menu_list']) ?></td>
                                <td class="text-center">
                                    <span class="badge badge-info"><?= $g['qty_total'] ?> pcs</span>
                                </td>
                                <td class="text-right font-weight-bold">
                                    Rp <?= number_format($g['total'], 0, ',', '.') ?>
                                </td>
                                <td><?= ucfirst($g['metode_pembayaran']) ?></td>
                                <td><?= $g['operator_nama'] ?></td>
                                <td class="text-center">
                                    <?php if ($g['status'] == 'selesai'): ?>
                                        <span class="badge badge-success">Selesai</span>
                                    <?php elseif ($g['status'] == 'batal'): ?>
                                        <span class="badge badge-danger">Batal</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-gray-300 mb-3"></i>
                <h5 class="text-gray-500">Belum ada transaksi hari ini</h5>
                <p class="text-muted">Transaksi akan muncul di sini setelah ada pembelian di POS</p>
                <a href="<?= base_url('pos/modern') ?>" class="btn btn-primary">
                    <i class="fas fa-cash-register mr-1"></i> Mulai Transaksi
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tombol Aksi -->


<script>
    $(document).ready(function() {
        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Initialize DataTable
        $('#dataTable').DataTable({
            "order": [
                [1, "desc"]
            ],
            "language": {
                "url": "<?= base_url('assets/js/Indonesian.json') ?>"
            },
            "pageLength": 25
        });
    });
</script>

<style>
    @media print {

        .btn,
        .breadcrumb,
        .alert {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
        }
    }
</style>