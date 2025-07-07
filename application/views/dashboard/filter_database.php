<?php /* @var $kelas_list, $filter, $santri */ ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Filter Database Santri</h1>
    <form method="get" class="mb-4 card card-body shadow-sm">
        <div class="row">
            <div class="col-md-3">
                <label for="kelas">Kelas/Angkatan</label>
                <select name="kelas" id="kelas" class="form-control">
                    <option value="">Semua Kelas</option>
                    <?php foreach($kelas_list as $k): ?>
                        <option value="<?= htmlspecialchars($k) ?>" <?= $filter['kelas'] == $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                    <option value="">Semua</option>
                    <option value="L" <?= $filter['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= $filter['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Filter</button>
            </div>
        </div>
    </form>
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-primary text-white font-weight-bold">Hasil Filter</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nomor Induk</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Jenis Kelamin</th>
                            <th>Saldo Tabungan</th>
                            <th>Saldo Jajan</th>
                            <th>Total Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($santri): foreach($santri as $s): ?>
                        <tr>
                            <td><?= $s->nomor_induk ?></td>
                            <td><?= $s->nama ?></td>
                            <td><?= $s->kelas ?></td>
                            <td><?= $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                            <td class="text-right">Rp <?= number_format($s->saldo_tabungan ?? 0, 0, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format($s->saldo_jajan ?? 0, 0, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format(($s->saldo_tabungan ?? 0) + ($s->saldo_jajan ?? 0), 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 