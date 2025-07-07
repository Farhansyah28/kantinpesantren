<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    <a href="<?= base_url('menu') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Menu
    </a>
</div>

<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Informasi Menu -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-utensils mr-1"></i> Informasi Menu
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nama Menu:</strong></td>
                        <td><?= $menu->nama_menu ?></td>
                    </tr>
                    <tr>
                        <td><strong>Pemilik:</strong></td>
                        <td><?= $menu->pemilik ?? '-' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Harga Beli:</strong></td>
                        <td>Rp <?= number_format($menu->harga_beli, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Harga Jual:</strong></td>
                        <td>Rp <?= number_format($menu->harga_jual, 0, ',', '.') ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Stok Sekarang:</strong></td>
                        <td>
                            <span class="badge badge-<?= $menu->stok > 10 ? 'success' : ($menu->stok > 0 ? 'warning' : 'danger') ?>">
                                <?= $menu->stok ?> pcs
                            </span>
                        </td>
                    </tr>
                    <?php if(isset($summary[0])): ?>
                    <tr>
                        <td><strong>Total Masuk:</strong></td>
                        <td><?= $summary[0]->total_masuk ?? 0 ?> pcs</td>
                    </tr>
                    <tr>
                        <td><strong>Total Keluar:</strong></td>
                        <td><?= $summary[0]->total_keluar ?? 0 ?> pcs</td>
                    </tr>
                    <tr>
                        <td><strong>Total Pembelian:</strong></td>
                        <td>Rp <?= number_format($summary[0]->total_pembelian ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Stok -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-history mr-1"></i> Riwayat Perubahan Stok
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Stok Sebelum</th>
                        <th class="text-center">Stok Sesudah</th>
                        <th class="text-right">Harga Beli</th>
                        <th class="text-right">Total Harga</th>
                        <th>Keterangan</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($riwayat): ?>
                        <?php $no = 1; foreach($riwayat as $r): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($r->created_at)) ?></td>
                            <td>
                                <?php if($r->jenis == 'masuk'): ?>
                                    <span class="badge badge-success">Masuk</span>
                                <?php elseif($r->jenis == 'keluar'): ?>
                                    <span class="badge badge-danger">Keluar</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Adjustment</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="font-weight-bold"><?= $r->jumlah ?> pcs</span>
                            </td>
                            <td class="text-center"><?= $r->stok_sebelum ?> pcs</td>
                            <td class="text-center"><?= $r->stok_sesudah ?> pcs</td>
                            <td class="text-right">
                                <?= $r->harga_beli > 0 ? 'Rp ' . number_format($r->harga_beli, 0, ',', '.') : '-' ?>
                            </td>
                            <td class="text-right">
                                <?= $r->total_harga > 0 ? 'Rp ' . number_format($r->total_harga, 0, ',', '.') : '-' ?>
                            </td>
                            <td><?= $r->keterangan ?: '-' ?></td>
                            <td><?= $r->admin_nama ?: '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada riwayat stok</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[1, "desc"]],
        "language": {
            "url": "<?= base_url('assets/js/Indonesian.json') ?>"
        }
    });
});
</script> 