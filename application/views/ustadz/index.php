<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-tie mr-2 text-primary"></i>Data Ustadz/Ustadzah
    </h1>
    <div>
        <a href="<?= site_url('ustadz/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Ustadz/Ustadzah
        </a>
        <a href="<?= site_url('ustadz/import') ?>" class="btn btn-success btn-sm ml-2">
            <i class="fas fa-upload fa-sm"></i> Import CSV
        </a>
        <a href="<?= site_url('ustadz/export_csv') ?>" class="btn btn-info btn-sm ml-2">
            <i class="fas fa-download fa-sm"></i> Export CSV
        </a>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list mr-1"></i> Daftar Ustadz/Ustadzah
                </h6>
                <form class="form-inline" method="GET" action="<?= site_url('ustadz/search') ?>">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" name="keyword"
                            placeholder="Cari nama atau nomor telepon..."
                            value="<?= isset($keyword) ? htmlspecialchars($keyword) : '' ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary btn-sm" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <?php if (empty($ustadz)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-user-tie fa-3x mb-3"></i>
                        <p>Tidak ada data ustadz/ustadzah</p>
                        <?php if (isset($keyword)): ?>
                            <a href="<?= site_url('ustadz') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Semua Data
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="40%">Nama</th>
                                    <th width="25%">Nomor Telepon</th>
                                    <th width="15%">Tanggal Dibuat</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($ustadz as $u): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($u->nama) ?></td>
                                        <td><?= htmlspecialchars($u->nomor_telepon) ?></td>
                                        <td><?= date('d/m/Y', strtotime($u->created_at)) ?></td>
                                        <td>
                                            <a href="<?= site_url('ustadz/edit/' . $u->id) ?>"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmDelete(<?= $u->id ?>, '<?= htmlspecialchars($u->nama, ENT_QUOTES) ?>')"
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "<?= base_url('assets/js/Indonesian.json') ?>"
            }
        });
    });

    function confirmDelete(id, nama) {
        if (confirm('Yakin ingin menghapus ustadz/ustadzah "' + nama + '"?\n\nTindakan ini tidak dapat dibatalkan.')) {
            // Validasi ID
            if (!id || isNaN(id) || id <= 0) {
                alert('ID tidak valid!');
                return false;
            }

            // Redirect ke halaman delete
            window.location.href = '<?= site_url('ustadz/delete/') ?>' + id;
        }
        return false;
    }
</script>