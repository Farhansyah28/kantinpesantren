<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-wallet mr-2"></i>Overview Saldo Tabungan
    </h1>
    <a href="<?= base_url('tabungan') ?>" class="btn btn-sm btn-primary">
        <i class="fas fa-arrow-left mr-1"></i>Kembali ke Tabungan
    </a>
</div>

<!-- Statistik Utama -->
<div class="row mb-4">
    <!-- Total Saldo Keseluruhan -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Saldo Keseluruhan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp <?= number_format($total_saldo_keseluruhan, 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Saldo Jajan -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Saldo Jajan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp <?= number_format($total_saldo_jajan, 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-coins fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Saldo Wajib -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Saldo Tabungan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp <?= number_format($total_saldo_tabungan, 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Santri -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Santri Aktif
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $santri_aktif ?> / <?= $total_santri ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Detail -->
<div class="row mb-4">
    <!-- Rata-rata Saldo -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line mr-2"></i>Rata-rata Saldo per Santri
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 font-weight-bold text-success">Rp <?= number_format($rata_saldo_jajan, 0, ',', '.') ?></div>
                            <div class="text-xs text-muted">Rata-rata Saldo Jajan</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 font-weight-bold text-info">Rp <?= number_format($rata_saldo_tabungan, 0, ',', '.') ?></div>
                            <div class="text-xs text-muted">Rata-rata Saldo Tabungan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribusi Santri -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie mr-2"></i>Distribusi Santri
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 font-weight-bold text-primary"><?= $santri_putra ?></div>
                            <div class="text-xs text-muted">Santri Putra</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 font-weight-bold text-pink"><?= $santri_putri ?></div>
                            <div class="text-xs text-muted">Santri Putri</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Saldo per Kantin -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-store mr-2"></i>Saldo per Kantin
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Kantin</th>
                                <th>Jenis</th>
                                <th>Total Santri</th>
                                <th class="text-right">Saldo Jajan</th>
                                <th class="text-right">Saldo Tabungan</th>
                                <th class="text-right">Total Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($saldo_per_kantin as $kantin): ?>
                                <tr>
                                    <td><strong><?= $kantin['nama_kantin'] ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?= $kantin['jenis'] == 'putra' ? 'primary' : 'pink' ?>">
                                            <?= ucfirst($kantin['jenis']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center"><?= $kantin['total_santri'] ?></td>
                                    <td class="text-right text-success">Rp <?= number_format($kantin['total_saldo_jajan'], 0, ',', '.') ?></td>
                                    <td class="text-right text-info">Rp <?= number_format($kantin['total_saldo_tabungan'], 0, ',', '.') ?></td>
                                    <td class="text-right font-weight-bold">Rp <?= number_format($kantin['total_saldo'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Santri dengan Saldo (Compact) -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list mr-2"></i>Daftar Santri dengan Saldo (50 Teratas)
                </h6>
                <a href="<?= base_url('tabungan') ?>" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-sm table-hover table-bordered">
                        <thead class="thead-light sticky-top">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>JK</th>
                                <th class="text-right">Saldo Jajan</th>
                                <th class="text-right">Saldo Tabungan</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($santri_with_saldo as $santri): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <div class="font-weight-bold"><?= $santri['nama'] ?></div>
                                        <small class="text-muted"><?= $santri['nomor_induk'] ?></small>
                                    </td>
                                    <td class="text-center"><?= $santri['kelas'] ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $santri['jenis_kelamin'] == 'L' ? 'primary' : 'pink' ?>">
                                            <?= $santri['jenis_kelamin'] == 'L' ? 'L' : 'P' ?>
                                        </span>
                                    </td>
                                    <td class="text-right text-success">Rp <?= number_format($santri['saldo_jajan'], 0, ',', '.') ?></td>
                                    <td class="text-right text-info">Rp <?= number_format($santri['saldo_tabungan'], 0, ',', '.') ?></td>
                                    <td class="text-right font-weight-bold">Rp <?= number_format($santri['total_saldo'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 1020;
    background: white;
}

.table-sm td, .table-sm th {
    padding: 0.3rem;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}

.text-xs {
    font-size: 0.75rem;
}

.card-body {
    padding: 1rem;
}

.table-responsive {
    overflow-y: auto;
}
</style> 