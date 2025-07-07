<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Histori Setoran ke Pemilik Jajanan - <?= html_escape($kantin_info->nama ?? 'N/A') ?></h1>
    <a href="<?= base_url('dashboard') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Dashboard
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

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-hand-holding-usd mr-2"></i>Histori Setoran Lengkap</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Nama Pemilik</th>
                        <th class="text-center">Total Item Terjual</th>
                        <th class="text-right">Jumlah Setoran (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($histori_setoran)): ?>
                        <?php
                        $no = 1;
                        $total_setoran = 0;
                        $total_item = 0;
                        foreach ($histori_setoran as $row):
                            $total_setoran += $row->total_setoran;
                            $total_item += $row->jumlah_transaksi;
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($row->tanggal)) ?></td>
                                <td><?= html_escape($row->pemilik) ?></td>
                                <td class="text-center"><?= $row->jumlah_transaksi ?></td>
                                <td class="text-right font-weight-bold">Rp <?= number_format($row->total_setoran, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada histori setoran.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background:#f8f9fc;font-weight:bold;">
                        <td colspan="3" class="text-center">Total</td>
                        <td class="text-center"><?= isset($total_item) ? $total_item : 0 ?></td>
                        <td class="text-right">Rp <?= isset($total_setoran) ? number_format($total_setoran, 0, ',', '.') : '0' ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Tabel Setoran Harian Per Tanggal -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-day mr-2"></i>Setoran Per Hari</h6>
        <form method="get" class="form-inline">
            <label for="tanggal_harian" class="mr-2 mb-0">Tanggal:</label>
            <input type="date" id="tanggal_harian" name="tanggal_harian" class="form-control form-control-sm mr-2" value="<?= html_escape($tanggal_harian) ?>">
            <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tabelSetoranHarian" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Pemilik</th>
                        <th class="text-center">Total Item Terjual</th>
                        <th class="text-right">Jumlah Setoran (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($setoran_harian)): ?>
                        <?php $no = 1;
                        $total_setoran = 0;
                        $total_item = 0;
                        foreach ($setoran_harian as $row):
                            $total_setoran += $row->total_setoran;
                            $total_item += $row->total_item_terjual;
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= html_escape($row->pemilik) ?></td>
                                <td class="text-center"><?= $row->total_item_terjual ?></td>
                                <td class="text-right font-weight-bold">Rp <?= number_format($row->total_setoran, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data setoran untuk tanggal ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background:#f8f9fc;font-weight:bold;">
                        <td colspan="2" class="text-center">Total</td>
                        <td class="text-center"><?= isset($total_item) ? $total_item : 0 ?></td>
                        <td class="text-right">Rp <?= isset($total_setoran) ? number_format($total_setoran, 0, ',', '.') : '0' ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [
                [1, "desc"]
            ],
            "language": {
                "url": "<?= base_url('assets/js/Indonesian.json') ?>"
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();

                // Helper konversi format Rp ke angka
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        parseInt(i.replace(/[^0-9]/g, ''), 10) :
                        typeof i === 'number' ?
                        i : 0;
                };

                // Total item terjual (kolom ke-3, index 3)
                var totalItem = api
                    .column(3, {
                        search: 'applied'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total setoran (kolom ke-4, index 4)
                var totalSetoran = api
                    .column(4, {
                        search: 'applied'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(3).footer()).html('<b>' + totalItem + '</b>');
                $(api.column(4).footer()).html('<b>Rp ' + totalSetoran.toLocaleString('id-ID') + '</b>');
            }
        });

        $('#tabelSetoranHarian').DataTable({
            "order": [
                [1, "asc"]
            ],
            "language": {
                "url": "<?= base_url('assets/js/Indonesian.json') ?>"
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        parseInt(i.replace(/[^0-9]/g, ''), 10) :
                        typeof i === 'number' ?
                        i : 0;
                };
                var totalItem = api
                    .column(2, {
                        search: 'applied'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                var totalSetoran = api
                    .column(3, {
                        search: 'applied'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                $(api.column(2).footer()).html('<b>' + totalItem + '</b>');
                $(api.column(3).footer()).html('<b>Rp ' + totalSetoran.toLocaleString('id-ID') + '</b>');
            }
        });
    });
</script>