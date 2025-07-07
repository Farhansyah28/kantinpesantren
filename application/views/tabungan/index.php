<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">Data Tabungan Santri</h1>
  <?php if (in_array($this->session->userdata('role'), ['admin', 'keuangan'])): ?>
    <div>
      <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modal-import-tabungan">
        <i class="fas fa-upload fa-sm"></i> Import CSV
      </button>
      <a href="<?= site_url('tabungan/export_csv') ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-download fa-sm"></i> Export CSV
      </a>
      <a href="<?= site_url('tabungan/template_csv') ?>" class="btn btn-outline-info btn-sm">
        <i class="fas fa-file-csv fa-sm"></i> Download Template
      </a>
      <a href="<?= site_url('tabungan/transfer_kategori') ?>" class="btn btn-success btn-sm">
        <i class="fas fa-exchange-alt fa-sm"></i> Transfer Kategori
      </a>
      <a href="<?= site_url('tabungan/transfer_antar_santri') ?>" class="btn btn-info btn-sm">
        <i class="fas fa-users fa-sm"></i> Transfer Antar Santri
      </a>
    </div>
  <?php endif; ?>
</div>

<?php if ($this->session->flashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $this->session->flashdata('success'); ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $this->session->flashdata('error'); ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php endif; ?>

<!-- Modal Import CSV Tabungan -->
<div class="modal fade" id="modal-import-tabungan" tabindex="-1" role="dialog" aria-labelledby="modalImportTabunganLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalImportTabunganLabel">Import CSV Tabungan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= site_url('tabungan/import_csv') ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <div class="modal-body">
          <div class="alert alert-info">
            <b>Format CSV (delimiter ;):</b><br>
            <code>nomor_induk;nama_santri;saldo_tabungan;saldo_jajan</code><br>
            Contoh:<br>
            <code>2023001;Ahmad;50000;20000</code><br>
            <code>2023002;Budi;75000;15000</code>
          </div>
          <div class="form-group">
            <label for="csv_file">Pilih file CSV</label>
            <input type="file" class="form-control" name="csv_file" id="csv_file" accept=".csv" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Import</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Drop Saldo -->
<div class="modal fade" id="modal-drop-saldo" tabindex="-1" role="dialog" aria-labelledby="modalDropSaldoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalDropSaldoLabel">Konfirmasi Hapus Semua Saldo</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= site_url('tabungan/drop_saldo') ?>" method="post">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <div class="modal-body">
          <div class="alert alert-danger">
            <b>PERINGATAN!</b> Tindakan ini akan menghapus seluruh saldo tabungan dan saldo jajan semua santri.<br>
            Data yang dihapus <b>tidak dapat dikembalikan</b>.<br>
            Lanjutkan?
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus Semua Saldo</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Kurangi Saldo Jajan Massal -->
<div class="modal fade" id="modal-mass-deduct" tabindex="-1" role="dialog" aria-labelledby="modalMassDeductLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalMassDeductLabel">Kurangi Saldo Jajan Massal</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= site_url('tabungan/kurangi_saldo_jajan_massal') ?>" method="post">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <div class="modal-body">
          <div class="form-group">
            <label for="mass_nominal">Nominal Pengurangan <span class="text-danger">*</span></label>
            <input type="text" class="form-control rupiah-input" name="nominal" id="mass_nominal" required placeholder="Masukkan nominal (Rp)">
          </div>
          <div class="form-group">
            <label for="mass_keterangan_template">Template Keterangan</label>
            <select class="form-control" id="mass_keterangan_template">
              <option value="">-- Pilih Template --</option>
              <option value="Surat Jalan">Surat Jalan</option>
              <option value="Kas Kelas">Kas Kelas</option>
              <option value="Kas Kamar">Kas Kamar</option>
              <option value="Potong Rambut">Potong Rambut</option>
              <option value="Lainnya">Ketik Sendiri...</option>
            </select>
          </div>
          <div class="form-group">
            <label for="mass_keterangan">Keterangan <span class="text-danger">*</span></label>
            <textarea class="form-control" name="keterangan" id="mass_keterangan" rows="2" required placeholder="Masukkan keterangan"></textarea>
          </div>
          <div class="alert alert-warning small mb-0">
            Semua saldo jajan santri akan dikurangi nominal ini. Jika saldo tidak cukup, akan menjadi minus (hutang).
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Kurangi Saldo Jajan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Content Row -->
<div class="row">
  <div class="col-xl-12 col-lg-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
          <i class="fas fa-wallet mr-1"></i> Daftar Tabungan Santri
        </h6>
        <div>
          <a href="<?= site_url('tabungan/setoran') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Setoran
          </a>
          <a href="<?= site_url('tabungan/penarikan') ?>" class="btn btn-warning btn-sm">
            <i class="fas fa-minus fa-sm"></i> Penarikan
          </a>
          <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-mass-deduct">
            <i class="fas fa-minus-circle fa-sm"></i> Kurangi Saldo Jajan Massal
          </button>
          <!-- <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modal-import-tabungan">
                        <i class="fas fa-upload fa-sm"></i> Import CSV
                    </button>
                    <a href="<?= site_url('tabungan/export_csv') ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-download fa-sm"></i> Export CSV
                    </a>
                    <a href="<?= site_url('tabungan/template_csv') ?>" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-file-csv fa-sm"></i> Download Template
                    </a>
                    <a href="<?= site_url('tabungan/transfer_kategori') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-exchange-alt fa-sm"></i> Transfer Kategori
                    </a>
                    <a href="<?= site_url('tabungan/transfer_antar_santri') ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-users fa-sm"></i> Transfer Antar Santri
                    </a> -->
        </div>
      </div>
      <div class="card-body">
        <?php if (empty($santri)): ?>
          <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Belum ada data santri dengan tabungan.
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" id="dataTable" width="100%" cellspacing="0">
              <thead class="thead-light text-center align-middle">
                <tr>
                  <th style="width:40px;">No</th>
                  <th>Nama</th>
                  <th style="width:80px;">Angkatan</th>
                  <th style="width:110px;">Jenis Kelamin</th>
                  <th class="text-right" style="width:120px;">Saldo Tabungan</th>
                  <th class="text-right" style="width:120px;">Saldo Jajan</th>
                  <th class="text-right" style="width:130px;">Total Saldo</th>
                  <th class="text-center" style="width:90px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1;
                foreach ($santri as $s): ?>
                  <tr>
                    <td class="text-center align-middle"><?= $no++ ?></td>
                    <td class="align-middle font-weight-bold"><?= htmlspecialchars($s->nama) ?></td>
                    <td class="text-center align-middle"><?= htmlspecialchars($s->kelas) ?></td>
                    <td class="text-center align-middle">
                      <span class="badge badge-<?= $s->jenis_kelamin == 'L' ? 'primary' : 'pink' ?>">
                        <?= $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                      </span>
                    </td>
                    <td class="text-right align-middle">
                      Rp <?= number_format($s->saldo_tabungan ?? 0, 0, ',', '.') ?>
                    </td>
                    <td class="text-right align-middle">
                      Rp <?= number_format($s->saldo_jajan ?? 0, 0, ',', '.') ?>
                    </td>
                    <td class="text-right align-middle font-weight-bold">
                      Rp <?= number_format(($s->saldo_tabungan ?? 0) + ($s->saldo_jajan ?? 0), 0, ',', '.') ?>
                    </td>
                    <td class="text-center align-middle">
                      <a href="<?= site_url('tabungan/setoran?santri_id=' . $s->id) ?>" class="btn btn-primary btn-sm mx-1" data-toggle="tooltip" title="Setoran">
                        <i class="fas fa-plus"></i>
                      </a>
                      <a href="<?= site_url('tabungan/penarikan?santri_id=' . $s->id) ?>" class="btn btn-warning btn-sm mx-1" data-toggle="tooltip" title="Penarikan">
                        <i class="fas fa-minus"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <style>
            .table thead th {
              vertical-align: middle;
            }

            .table-hover tbody tr:hover {
              background-color: #f1f3f6;
            }

            .badge-pink {
              background: #e83e8c;
              color: #fff;
            }
          </style>
        <?php endif; ?>
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

    var btnDrop = document.getElementById('btn-drop-saldo');
    if (btnDrop) {
      btnDrop.onclick = function() {
        $('#modal-drop-saldo').modal('show');
      };
    }

    function updateKeteranganReadonly() {
      var val = $('#mass_keterangan_template').val();
      if (val === 'Lainnya') {
        $('#mass_keterangan').val('').prop('readonly', false).focus();
      } else if (val) {
        $('#mass_keterangan').val(val).prop('readonly', true);
      } else {
        $('#mass_keterangan').val('').prop('readonly', true);
      }
    }
    $('#mass_keterangan_template').change(updateKeteranganReadonly);
    // Set readonly default saat modal dibuka
    $('#modal-mass-deduct').on('show.bs.modal', function() {
      updateKeteranganReadonly();
    });
    // Reset textarea saat modal ditutup
    $('#modal-mass-deduct').on('hidden.bs.modal', function() {
      $('#mass_keterangan').val('').prop('readonly', true);
      $('#mass_keterangan_template').val('');
    });
  });
</script>