<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 align-self-center">
                    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-graduate mr-2"></i> Detail Santri</h1>
                </div>
                <div class="col-sm-6 align-self-center">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('santri') ?>">Data Santri</a></li>
                        <li class="breadcrumb-item active">Detail Santri</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Profil Santri -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body pb-2">
                            <div class="text-center mb-3">
                                <?php if (isset($santri->foto) && $santri->foto): ?>
                                    <img src="<?= base_url('uploads/santri/' . $santri->foto) ?>" class="img-fluid rounded-circle border border-primary" style="width: 120px; height: 120px; object-fit: cover;">
                                <?php else: ?>
                                    <?php if (isset($santri->jenis_kelamin) && $santri->jenis_kelamin == 'P'): ?>
                                        <img src="<?= base_url('assets/img/profilwebperempuan.png') ?>" class="img-fluid rounded-circle border border-secondary" style="width: 120px; height: 120px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?= base_url('assets/img/profilweb.png') ?>" class="img-fluid rounded-circle border border-secondary" style="width: 120px; height: 120px; object-fit: cover;">
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <table class="table table-sm table-borderless mb-2">
                                <tr>
                                    <th class="text-gray-700 pr-2">Nomor Induk</th>
                                    <td><?= html_escape($santri->nomor_induk) ?></td>
                                </tr>
                                <tr>
                                    <th class="text-gray-700 pr-2">Nama</th>
                                    <td><?= html_escape($santri->nama) ?></td>
                                </tr>
                                <tr>
                                    <th class="text-gray-700 pr-2">Angkatan</th>
                                    <td><?= html_escape($santri->kelas) ?></td>
                                </tr>
                                <tr>
                                    <th class="text-gray-700 pr-2">Jenis Kelamin</th>
                                    <td>
                                        <?php if ($santri->jenis_kelamin == 'L'): ?>
                                            <span class="badge badge-primary"><i class="fas fa-mars mr-1"></i>Laki-laki</span>
                                        <?php else: ?>
                                            <span class="badge badge-pink"><i class="fas fa-venus mr-1"></i>Perempuan</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            <?php if (isset($santri->nama_wali) && $santri->nama_wali): ?>
                                <div class="border-top pt-2 mt-2">
                                    <div class="small text-gray-600 font-weight-bold mb-1">Wali Santri</div>
                                    <div class="mb-1"><i class="fas fa-user-tie mr-1"></i><?= html_escape($santri->nama_wali) ?></div>
                                    <div class="mb-1"><i class="fas fa-phone mr-1"></i><?= isset($santri->kontak_wali) ? html_escape($santri->kontak_wali) : '-' ?></div>
                                    <div><i class="fas fa-heart mr-1"></i><?= isset($santri->hubungan_wali) ? html_escape($santri->hubungan_wali) : '-' ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white text-center border-0 pt-0 pb-3">
                            <a href="<?= base_url('santri/edit/' . $santri->id) ?>" class="btn btn-warning btn-sm mr-2"><i class="fas fa-edit mr-1"></i> Edit</a>
                            <button id="btnHapusSantri" class="btn btn-danger btn-sm" data-id="<?= $santri->id ?>" title="Hapus"><i class="fas fa-trash-alt mr-1"></i> Hapus</button>
                            <a href="<?= base_url('santri') ?>" class="btn btn-secondary btn-sm ml-2"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tabungan & Riwayat -->
                <div class="col-lg-8 mb-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="mr-2"><i class="fas fa-coins fa-lg text-primary"></i></span>
                                        <span class="font-weight-bold text-gray-700">Saldo Tabungan</span>
                                    </div>
                                    <div class="h5 font-weight-bold text-primary mb-0">Rp <?= number_format($santri->saldo_tabungan ?? 0, 0, ',', '.') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="mr-2"><i class="fas fa-wallet fa-lg text-success"></i></span>
                                        <span class="font-weight-bold text-gray-700">Saldo Jajan</span>
                                    </div>
                                    <div class="h5 font-weight-bold text-success mb-0">Rp <?= number_format($santri->saldo_jajan ?? 0, 0, ',', '.') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow h-100">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="mr-2"><i class="fas fa-money-bill-wave fa-lg text-info"></i></span>
                                        <span class="font-weight-bold text-gray-700">Total Saldo</span>
                                    </div>
                                    <div class="h5 font-weight-bold text-info mb-0">Rp <?= number_format(($santri->saldo_tabungan ?? 0) + ($santri->saldo_jajan ?? 0), 0, ',', '.') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-history mr-1"></i> Riwayat Transaksi</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0" id="tabel-transaksi">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jenis</th>
                                            <th>Jumlah</th>
                                            <th>Keterangan</th>
                                            <th>Kategori</th>
                                            <th>Saldo Akhir</th>
                                            <th>Petugas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($transaksi) && $transaksi): ?>
                                            <?php
                                            // Ambil saldo saat ini
                                            $saldo_tabungan = $santri->saldo_tabungan ?? 0;
                                            $saldo_jajan = $santri->saldo_jajan ?? 0;
                                            // Urutkan transaksi dari terbaru ke lama
                                            $trans_sorted = $transaksi;
                                            usort($trans_sorted, function ($a, $b) {
                                                return strtotime($b->created_at) <=> strtotime($a->created_at);
                                            });
                                            // Hitung saldo akhir mundur
                                            foreach ($trans_sorted as $t) {
                                                if ($t->kategori == 'tabungan') {
                                                    $t->_saldo_akhir_tabungan = $saldo_tabungan;
                                                    if ($t->jenis == 'setoran') $saldo_tabungan -= $t->jumlah;
                                                    elseif ($t->jenis == 'penarikan') $saldo_tabungan += $t->jumlah;
                                                } else if ($t->kategori == 'jajan') {
                                                    $t->_saldo_akhir_jajan = $saldo_jajan;
                                                    if ($t->jenis == 'setoran') $saldo_jajan -= $t->jumlah;
                                                    elseif ($t->jenis == 'penarikan') $saldo_jajan += $t->jumlah;
                                                }
                                            }
                                            // Tampilkan transaksi (urut terbaru ke lama)
                                            foreach ($trans_sorted as $t): ?>
                                                <tr>
                                                    <td><?= date('d/m/Y H:i', strtotime($t->created_at)) ?></td>
                                                    <td>
                                                        <?php if ($t->jenis == 'setoran'): ?>
                                                            <span class="badge badge-success">Setoran</span>
                                                        <?php elseif ($t->jenis == 'penarikan'): ?>
                                                            <span class="badge badge-danger">Penarikan</span>
                                                        <?php elseif ($t->jenis == 'transfer'): ?>
                                                            <span class="badge badge-info">Transfer</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right">
                                                        Rp <?= number_format($t->jumlah, 0, ',', '.') ?>
                                                    </td>
                                                    <td><?= html_escape($t->keterangan) ?></td>
                                                    <td>
                                                        <?php if ($t->kategori == 'tabungan'): ?>
                                                            <span class="badge badge-primary">Tabungan</span>
                                                        <?php elseif ($t->kategori == 'jajan'): ?>
                                                            <span class="badge badge-warning">Jajan</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right font-weight-bold">
                                                        <?php if ($t->kategori == 'tabungan'): ?>
                                                            Rp <?= number_format($t->_saldo_akhir_tabungan, 0, ',', '.') ?>
                                                        <?php elseif ($t->kategori == 'jajan'): ?>
                                                            Rp <?= number_format($t->_saldo_akhir_jajan, 0, ',', '.') ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $t->admin_nama ?? '-' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada transaksi</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Tambahkan Riwayat Pembelian Kantin -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-gradient-success text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-store mr-1"></i> Riwayat Pembelian Kantin</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="15%">Tanggal</th>
                                            <th width="30%">Menu</th>
                                            <th width="10%">Qty</th>
                                            <th width="15%" class="text-right">Total Harga</th>
                                            <th width="10%">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($transaksi_kantin)): ?>
                                            <?php foreach ($transaksi_kantin as $group): ?>
                                                <tr>
                                                    <td class="align-middle">
                                                        <div class="font-weight-bold">
                                                            <?= date('d/m/Y', strtotime($group['created_at'])) ?>
                                                        </div>
                                                        <div class="text-muted">
                                                            <small><?= date('H:i', strtotime($group['created_at'])) ?></small>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php $menu_list = array_map(function ($item) {
                                                            return $item->nama_menu . ' x' . $item->jumlah;
                                                        }, $group['items']); ?>
                                                        <div><?= implode(', ', $menu_list) ?></div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <?php $qty_total = array_sum(array_map(function ($item) {
                                                            return $item->jumlah;
                                                        }, $group['items'])); ?>
                                                        <?= $qty_total ?> pcs
                                                    </td>
                                                    <td class="text-right align-middle">
                                                        <div class="font-weight-bold text-success">
                                                            Rp <?= number_format($group['total_transaksi'], 0, ',', '.') ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <?php if ($group['status'] == 'selesai'): ?>
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check mr-1"></i> Selesai
                                                            </span>
                                                        <?php elseif ($group['status'] == 'pending'): ?>
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-clock mr-1"></i> Pending
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">
                                                                <i class="fas fa-times mr-1"></i> Batal
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada riwayat pembelian kantin</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
                <a href="#" id="btn-delete" class="btn btn-danger">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tabel-transaksi').DataTable({
            "order": [
                [0, "desc"]
            ],
            "language": {
                "url": "<?= base_url('assets/js/Indonesian.json') ?>"
            }
        });

        // Konfirmasi hapus santri
        $('#btnHapusSantri').click(function() {
            var id = $(this).data('id');
            $('#btn-delete').attr('href', '<?= base_url('santri/delete/') ?>' + id);
            $('#modal-delete').modal('show');
        });
    });
</script>

<style>
    .badge-pink {
        background-color: #e83e8c;
        color: #fff;
    }
</style>