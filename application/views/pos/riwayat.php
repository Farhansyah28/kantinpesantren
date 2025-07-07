<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-history mr-2"></i>Riwayat Transaksi Hari Ini
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('pos') ?>">POS Kantin</a></li>
                        <li class="breadcrumb-item active">Riwayat</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-gradient-info">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-day mr-1"></i> Transaksi <?= date('d/m/Y') ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('pos') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-cash-register mr-1"></i> Kembali ke POS
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($transaksi): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="15%">Waktu</th>
                                        <th>Santri</th>
                                        <th>Menu</th>
                                        <th width="10%" class="text-center">Jumlah</th>
                                        <th width="15%" class="text-right">Harga Satuan</th>
                                        <th width="15%" class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_hari_ini = 0;
                                    foreach($transaksi as $t): 
                                        $total_hari_ini += $t->total_harga;
                                    ?>
                                    <tr>
                                        <td class="align-middle">
                                            <div class="font-weight-bold">
                                                <?= date('H:i', strtotime($t->created_at)) ?>
                                            </div>
                                            <div class="text-muted">
                                                <small><?= date('d/m/Y', strtotime($t->created_at)) ?></small>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold"><?= $t->nama_santri ?></div>
                                            <div class="text-muted">
                                                <small>
                                                    <i class="fas fa-id-card mr-1"></i>
                                                    <?= $t->nomor_induk ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold"><?= $t->nama_menu ?></div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-info"><?= $t->jumlah ?></span>
                                        </td>
                                        <td class="text-right align-middle">
                                            Rp <?= number_format($t->harga_jual, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-right align-middle">
                                            <div class="font-weight-bold text-success">
                                                Rp <?= number_format($t->total_harga, 0, ',', '.') ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="thead-light">
                                    <tr>
                                        <th colspan="5" class="text-right">Total Hari Ini:</th>
                                        <th class="text-right">
                                            <span class="font-weight-bold text-primary">
                                                Rp <?= number_format($total_hari_ini, 0, ',', '.') ?>
                                            </span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada transaksi hari ini</h5>
                            <p class="text-muted">Transaksi akan muncul di sini setelah ada pembelian di POS</p>
                            <a href="<?= base_url('pos') ?>" class="btn btn-primary">
                                <i class="fas fa-cash-register mr-1"></i> Mulai Transaksi
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-gradient-light">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_info" role="status" aria-live="polite">
                                Menampilkan <?= count($transaksi) ?> transaksi hari ini
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="float-sm-right">
                                <a href="<?= base_url('pos') ?>" class="btn btn-primary">
                                    <i class="fas fa-cash-register mr-1"></i> POS Kantin
                                </a>
                                <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                                    <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
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
</style> 