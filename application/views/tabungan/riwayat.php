<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
    @media print {

        .btn,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: none !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-header {
            background: none !important;
            border-bottom: 2px solid #000 !important;
        }

        .table {
            font-size: 12px !important;
        }

        .badge {
            border: 1px solid #000 !important;
            color: #000 !important;
        }
    }
</style>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history mr-2 text-primary"></i>Riwayat Transaksi Tabungan
        </h1>
        <p class="text-muted">Daftar semua transaksi tabungan dan jajan santri</p>
    </div>
    <div>
        <a href="<?= site_url('tabungan') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
        <button class="btn btn-info btn-sm ml-2" onclick="window.print()">
            <i class="fas fa-print fa-sm"></i> Cetak
        </button>
        <button class="btn btn-danger btn-sm ml-2" data-toggle="modal" data-target="#modalExportPDF">
            <i class="fas fa-file-pdf fa-sm"></i> Export PDF
        </button>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-riwayat" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="15%">Tanggal & Waktu</th>
                                <th width="20%">Santri</th>
                                <th width="10%">Jenis</th>
                                <th width="15%">Jumlah</th>
                                <th width="15%">Admin</th>
                                <th width="25%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transaksi)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-history fa-3x mb-3"></i>
                                        <p>Tidak ada data transaksi</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transaksi as $t): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($t->created_at)) ?></td>
                                        <td>
                                            <strong><?= $t->nama_santri ?></strong>
                                            <br>
                                            <small class="text-muted"><?= $t->nomor_induk ?> - <?= $t->kelas ?></small>
                                        </td>
                                        <td>
                                            <?php if ($t->jenis == 'setoran'): ?>
                                                <span class="badge badge-success">Setoran</span>
                                            <?php elseif ($t->jenis == 'penarikan'): ?>
                                                <span class="badge badge-danger">Penarikan</span>
                                            <?php elseif ($t->jenis == 'transfer'): ?>
                                                <span class="badge badge-info">Transfer</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary"><?= ucfirst($t->jenis) ?></span>
                                            <?php endif; ?>
                                            <br>
                                            <small class="text-muted"><?= ucfirst($t->kategori) ?></small>
                                        </td>
                                        <td class="text-right">Rp <?= number_format($t->jumlah, 0, ',', '.') ?></td>
                                        <td><?= $t->admin_username ?></td>
                                        <td><?= $t->keterangan ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Export PDF -->
<div class="modal fade" id="modalExportPDF" tabindex="-1" role="dialog" aria-labelledby="modalExportPDFLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= site_url('tabungan/export_pdf_riwayat') ?>" method="get" target="_blank">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExportPDFLabel">Export PDF Riwayat Tabungan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" class="form-control" name="tanggal_awal" id="tanggal_awal" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" class="form-control" name="tanggal_akhir" id="tanggal_akhir" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-file-pdf mr-1"></i> Export PDF</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Custom sorting untuk format tanggal Indonesia
        $.fn.dataTable.ext.type.order['date-euro-pre'] = function(data) {
            if (data == '') return 0;

            var parts = data.split(' ');
            var date = parts[0];
            var time = parts[1] || '00:00';

            var dateParts = date.split('/');
            var timeParts = time.split(':');

            var day = parseInt(dateParts[0], 10);
            var month = parseInt(dateParts[1], 10);
            var year = parseInt(dateParts[2], 10);
            var hour = parseInt(timeParts[0], 10);
            var minute = parseInt(timeParts[1], 10);

            return new Date(year, month - 1, day, hour, minute, 0).getTime();
        };

        $('#table-riwayat').DataTable({
            "order": [
                [0, 'desc']
            ], // Urutkan berdasarkan tanggal (kolom 0) secara descending
            "pageLength": 25, // Tampilkan 25 data per halaman
            "language": {
                "url": "<?= base_url('assets/js/Indonesian.json') ?>"
            },
            "columnDefs": [{
                    "targets": [0], // Kolom tanggal
                    "type": "date-euro"
                },
                {
                    "targets": [3], // Kolom jumlah
                    "className": "text-right"
                }
            ],
            "responsive": true,
            "autoWidth": false,
            "dom": '<"top"lf>rt<"bottom"ip><"clear">',
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ]
        });
    });
</script>