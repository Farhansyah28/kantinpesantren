<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    <?php if (in_array($this->session->userdata('role'), ['admin', 'keuangan', 'operator'])): ?>
    <a href="<?= base_url('menu/create') ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Menu
    </a>
    <?php endif; ?>
</div>

<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-utensils mr-1"></i> Daftar Menu Kantin
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <?php if (in_array($this->session->userdata('role'), ['admin', 'keuangan'])): ?>
                            <th>Kantin</th>
                        <?php endif; ?>
                        <th>Nama Menu</th>
                        <th class="text-center">Stok</th>
                        <th class="text-right">Harga Beli</th>
                        <th class="text-right">Harga Jual</th>
                        <th class="text-right"><strong>Keuntungan/Unit</strong></th>
                        <th class="text-right"><strong>Total Keuntungan</strong></th>
                        <th>Pemilik</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($menu as $m): ?>
                    <tr <?= (isset($m->jenis_kantin) && $m->jenis_kantin === 'putri') ? 'style="background-color: #ffe6f2;"' : '' ?>>
                        <td class="text-center"><?= $no++ ?></td>
                        <?php if (in_array($this->session->userdata('role'), ['admin', 'keuangan'])): ?>
                            <td>
                                <span class="badge badge-<?= (isset($m->jenis_kantin) && $m->jenis_kantin === 'putri') ? 'pink' : 'primary' ?>"><?= $m->nama_kantin ?? 'N/A' ?></span>
                            </td>
                        <?php endif; ?>
                        <td>
                            <div class="font-weight-bold"><?= $m->nama_menu ?></div>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-<?= $m->stok > 10 ? 'success' : ($m->stok > 0 ? 'warning' : 'danger') ?>">
                                <?= $m->stok ?> pcs
                            </span>
                        </td>
                        <td class="text-right">Rp <?= number_format($m->harga_beli, 0, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format($m->harga_jual, 0, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format($m->harga_jual - $m->harga_beli, 0, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format(($m->harga_jual - $m->harga_beli) * $m->stok, 0, ',', '.') ?></td>
                        <td><?= $m->pemilik ?? '-' ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="<?= base_url('menu/tambah_stok_form/'.$m->id) ?>" class="btn btn-success btn-sm" title="Tambah Stok">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <?php if ($this->session->userdata('role') === 'admin'): ?>
                                <a href="<?= base_url('menu/edit/'.$m->id) ?>" class="btn btn-warning btn-sm" title="Edit Menu">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <a href="<?= base_url('menu/riwayat_stok/'.$m->id) ?>" class="btn btn-info btn-sm" title="Riwayat Stok">
                                    <i class="fas fa-history"></i>
                                </a>
                                <?php if ($this->session->userdata('role') === 'admin'): ?>
                                <a href="<?= base_url('menu/delete/'.$m->id) ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Yakin ingin menghapus menu ini?')" title="Hapus Menu">
                                    <i class="fas fa-trash"></i>
                                </a>
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

<!-- Informasi Stok Management -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-boxes mr-1"></i> Manajemen Stok
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="font-weight-bold text-primary">Cara Mengelola Stok:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-plus text-success mr-2"></i>Klik tombol <span class="badge badge-success"><i class="fas fa-plus"></i></span> untuk menambah stok</li>
                    <li><i class="fas fa-history text-info mr-2"></i>Klik tombol <span class="badge badge-info"><i class="fas fa-history"></i></span> untuk melihat riwayat stok</li>
                    <li><i class="fas fa-chart-bar text-primary mr-2"></i>Lihat <a href="<?= base_url('menu/riwayat_stok') ?>">Riwayat Stok Semua Menu</a> untuk overview</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="font-weight-bold text-primary">Status Stok:</h6>
                <ul class="list-unstyled">
                    <li><span class="badge badge-success mr-2">Hijau</span> Stok > 10 pcs</li>
                    <li><span class="badge badge-warning mr-2">Kuning</span> Stok 1-10 pcs</li>
                    <li><span class="badge badge-danger mr-2">Merah</span> Stok = 0 pcs</li>
                </ul>
            </div>
        </div>
    </div>
</div> 