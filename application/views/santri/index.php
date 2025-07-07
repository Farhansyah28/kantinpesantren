<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Santri</h1>
    <?php if (in_array($this->session->userdata('role'), ['admin', 'keuangan'])): ?>
    <div>
        <a href="<?= base_url('santri/create') ?>" class="btn btn-success btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Santri
        </a>
        <a href="<?= base_url('santri/import') ?>" class="btn btn-info btn-sm">
            <i class="fas fa-upload fa-sm"></i> Import CSV
        </a>
        <a href="<?= base_url('santri/export_csv') ?>" class="btn btn-warning btn-sm">
            <i class="fas fa-download fa-sm"></i> Export CSV
        </a>
        <a href="<?= base_url('santri/download_template') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-file-download fa-sm"></i> Download Template
        </a>
    </div>
    <?php endif; ?>
</div>

<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-users mr-1"></i> Daftar Santri
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nomor Induk</th>
                        <th>Nama</th>
                        <th class="text-center">Angkatan</th>
                        <th class="text-center">Jenis Kelamin</th>
                        <th>Wali Santri</th>
                        <th class="text-right">Saldo Jajan</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($santri as $s): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td>
                            <div class="font-weight-bold"><?= html_escape($s->nomor_induk) ?></div>
                            <div class="text-muted">
                                <small>ID: <?= html_escape($s->id) ?></small>
                            </div>
                        </td>
                        <td>
                            <div class="font-weight-bold"><?= html_escape($s->nama) ?></div>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info"><?= html_escape($s->kelas) ?></span>
                        </td>
                        <td class="text-center">
                            <?php if($s->jenis_kelamin == 'L'): ?>
                                <span class="badge badge-primary">
                                    <i class="fas fa-mars mr-1"></i>Laki-laki
                                </span>
                            <?php else: ?>
                                <span class="badge badge-pink">
                                    <i class="fas fa-venus mr-1"></i>Perempuan
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(!empty($s->nama_wali)): ?>
                                <div class="font-weight-bold"><?= html_escape($s->nama_wali) ?></div>
                                <div class="text-muted">
                                    <small>
                                        <i class="fas fa-phone mr-1"></i><?= html_escape($s->kontak_wali) ?>
                                    </small>
                                </div>
                                <div class="text-muted">
                                    <small>
                                        <i class="fas fa-user-tie mr-1"></i><?= html_escape($s->hubungan_wali) ?>
                                    </small>
                                </div>
                            <?php else: ?>
                                <span class="badge badge-light">
                                    <i class="fas fa-user-slash mr-1"></i>
                                    Belum ada wali
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <div class="font-weight-bold <?= ($s->saldo_jajan ?? 0) > 0 ? 'text-success' : (($s->saldo_jajan ?? 0) < 0 ? 'text-danger' : 'text-muted') ?>">
                                Rp <?= number_format($s->saldo_jajan ?? 0, 0, ',', '.') ?>
                            </div>
                            <?php if(($s->saldo_jajan ?? 0) > 0): ?>
                                <div class="text-muted">
                                    <small>
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        Saldo positif
                                    </small>
                                </div>
                            <?php elseif(($s->saldo_jajan ?? 0) < 0): ?>
                                <div class="text-muted">
                                    <small>
                                        <i class="fas fa-arrow-down mr-1"></i>
                                        Saldo negatif
                                    </small>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="<?= base_url('santri/view/'.$s->id) ?>" 
                                    class="btn btn-info btn-sm" 
                                    data-toggle="tooltip" 
                                    title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (in_array($this->session->userdata('role'), ['admin', 'keuangan'])): ?>
                                <a href="<?= base_url('santri/edit/'.$s->id) ?>" 
                                    class="btn btn-warning btn-sm" 
                                    data-toggle="tooltip" 
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-sm btn-hapus-santri" 
                                        data-id="<?= $s->id ?>" 
                                        data-toggle="tooltip"
                                        title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus data santri ini? Data yang sudah dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
                <a href="#" id="btn-confirm-delete" class="btn btn-danger">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "language": {
            "url": "<?= base_url('assets/js/Indonesian.json') ?>"
        }
    });
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Handle delete button click
    $('.btn-hapus-santri').click(function() {
        var id = $(this).data('id');
        $('#btn-confirm-delete').attr('href', '<?= base_url('santri/delete/') ?>' + id);
        $('#modal-delete').modal('show');
    });
});
</script> 