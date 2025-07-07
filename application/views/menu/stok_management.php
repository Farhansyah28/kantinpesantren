<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen Stok Menu</h1>
    
    <!-- Statistik Stok -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Menu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_menu ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                Stok Aman (>10)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stok_aman ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Stok Menipis (1-10)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stok_menipis ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Stok Habis (0)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stok_habis ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Menu -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Menu dan Stok</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Menu</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Kantin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($menu as $m): ?>
                        <tr <?= (isset($m->jenis_kantin) && $m->jenis_kantin === 'putri') ? 'style="background-color: #ffe6f2;"' : '' ?>>
                            <td><?= $no++ ?></td>
                            <td><?= $m->nama_menu ?></td>
                            <td>
                                <span class="badge badge-<?= $m->stok > 10 ? 'success' : ($m->stok > 0 ? 'warning' : 'danger') ?>">
                                    <?= $m->stok ?>
                                </span>
                            </td>
                            <td>
                                <?php if($m->stok > 10): ?>
                                    <span class="badge badge-success">Aman</span>
                                <?php elseif($m->stok > 0): ?>
                                    <span class="badge badge-warning">Menipis</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Habis</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= (isset($m->jenis_kantin) && $m->jenis_kantin === 'putri') ? 'pink' : 'primary' ?>">
                                    <?= isset($m->nama_kantin) ? $m->nama_kantin : '-' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('menu/tambah_stok_form/'.$m->id) ?>" 
                                       class="btn btn-success btn-sm" title="Tambah Stok">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    <a href="<?= base_url('menu/riwayat_stok/'.$m->id) ?>" 
                                       class="btn btn-info btn-sm" title="Riwayat Stok">
                                        <i class="fas fa-history"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kurangi Stok -->
<div class="modal fade" id="modalKurangiStok" tabindex="-1" role="dialog" aria-labelledby="modalKurangiStokLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKurangiStokLabel">Kurangi Stok Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('menu/kurangi_stok/') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" id="menu_id_kurangi" name="menu_id">
                    <div class="form-group">
                        <label for="nama_menu_kurangi">Nama Menu</label>
                        <input type="text" class="form-control" id="nama_menu_kurangi" readonly>
                    </div>
                    <div class="form-group">
                        <label for="jumlah_kurangi">Jumlah yang Dikurangi</label>
                        <input type="number" class="form-control" id="jumlah_kurangi" name="jumlah_kurangi" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Alasan pengurangan stok..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Kurangi Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showKurangiStok(menuId, namaMenu) {
    $('#menu_id_kurangi').val(menuId);
    $('#nama_menu_kurangi').val(namaMenu);
    $('#modalKurangiStok').modal('show');
}

$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script> 