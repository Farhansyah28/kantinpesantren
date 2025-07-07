<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    <a href="<?= base_url('menu') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Menu
    </a>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Ringkasan Stok -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-bar mr-1"></i> Ringkasan Stok Semua Menu
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="summaryTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Menu</th>
                        <th>Kantin</th>
                        <th class="text-center">Stok Sekarang</th>
                        <th class="text-center">Total Masuk</th>
                        <th class="text-center">Total Keluar</th>
                        <th class="text-right">Total Pembelian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($summary): ?>
                        <?php foreach ($summary as $s): ?>
                            <tr>
                                <td>
                                    <div class="font-weight-bold"><?= $s->nama_menu ?></div>
                                </td>
                                <td>
                                    <?php if (($s->jenis_kantin ?? '') === 'putra'): ?>
                                        <span class="badge badge-primary">Kantin Putra</span>
                                    <?php elseif (($s->jenis_kantin ?? '') === 'putri'): ?>
                                        <span class="badge badge-pink">Kantin Putri</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-<?= ($s->stok_sekarang ?? 0) > 10 ? 'success' : (($s->stok_sekarang ?? 0) > 0 ? 'warning' : 'danger') ?>">
                                        <?= $s->stok_sekarang ?? 0 ?> pcs
                                    </span>
                                </td>
                                <td class="text-center"><?= $s->total_masuk ?? 0 ?> pcs</td>
                                <td class="text-center"><?= $s->total_keluar ?? 0 ?> pcs</td>
                                <td class="text-right">Rp <?= number_format($s->total_pembelian ?? 0, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Riwayat Stok -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-history mr-1"></i> Riwayat Perubahan Stok Semua Menu
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Menu & Stok Saat Ini</th>
                        <th>Jenis</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Stok Awal</th>
                        <th class="text-center">Stok Akhir</th>
                        <th>Keterangan</th>
                        <th>Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($riwayat as $item): ?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= date('d M Y H:i', strtotime($item->created_at)); ?></td>
                            <td>
                                <?php if (isset($item->nama_menu)): ?>
                                    <strong class="d-block"><?= htmlspecialchars($item->nama_menu, ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small class="text-muted">Stok Saat Ini: <?= htmlspecialchars($item->stok ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></small>
                                <?php else: ?>
                                    <span class="text-danger font-italic">(Menu pada baris ini telah dihapus)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item->jumlah > 0): ?>
                                    <span class="badge badge-success">Masuk</span>
                                <?php elseif ($item->jumlah < 0): ?>
                                    <span class="badge badge-danger">Keluar</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Netral</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center font-weight-bold"><?= abs($item->jumlah); ?></td>
                            <td class="text-center"><?= $item->stok_sebelum; ?></td>
                            <td class="text-center"><?= $item->stok_sesudah; ?></td>
                            <td><?= htmlspecialchars($item->keterangan, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($item->admin_nama ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#summaryTable').DataTable({
            "order": [
                [1, "desc"]
            ],
            "language": {
                "url": "<?= base_url('assets/js/Indonesian.json') ?>"
            }
        });

        $('#dataTable').DataTable({
            "order": [
                [1, "desc"]
            ],
            "language": {
                "url": "<?= base_url('assets/js/Indonesian.json') ?>"
            }
        });
    });
</script>

<style>
    .badge-pink {
        background-color: #e83e8c;
        color: #fff;
    }
</style>