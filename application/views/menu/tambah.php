<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?= form_open('menu/tambah') ?>
                <div class="form-group">
                    <label for="nama_menu">Nama Menu</label>
                    <input type="text" class="form-control <?= form_error('nama_menu') ? 'is-invalid' : '' ?>" 
                           id="nama_menu" name="nama_menu" value="<?= set_value('nama_menu') ?>">
                    <div class="invalid-feedback">
                        <?= form_error('nama_menu') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pemilik">Pemilik / Penitip</label>
                    <select name="pemilik" id="select-pemilik" class="form-control <?= form_error('pemilik') ? 'is-invalid' : '' ?>" required>
                        <option value="">Pilih pemilik</option>
                        <?php if(isset(
                            $pemilik_list) && is_array($pemilik_list)): foreach($pemilik_list as $p): ?>
                            <option value="<?= htmlspecialchars($p->pemilik) ?>" <?= set_value('pemilik') == $p->pemilik ? 'selected' : '' ?>><?= htmlspecialchars($p->pemilik) ?></option>
                        <?php endforeach; endif; ?>
                        <option value="__new__" <?= set_value('pemilik') == '__new__' ? 'selected' : '' ?>>Tambah Pemilik Baru</option>
                    </select>
                    <input type="text" name="pemilik_baru" id="input-pemilik-baru" class="form-control mt-2" placeholder="Masukkan nama pemilik baru" style="display:none;" value="<?= set_value('pemilik_baru') ?>">
                    <div class="invalid-feedback">
                        <?= form_error('pemilik') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="harga_beli">Harga Beli</label>
                    <input type="text" class="form-control rupiah-input <?= form_error('harga_beli') ? 'is-invalid' : '' ?>" 
                           id="harga_beli" name="harga_beli" value="<?= set_value('harga_beli') ?>" placeholder="Rp 0">
                    <div class="invalid-feedback">
                        <?= form_error('harga_beli') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="harga_jual">Harga Jual</label>
                    <input type="text" class="form-control rupiah-input <?= form_error('harga_jual') ? 'is-invalid' : '' ?>" 
                           id="harga_jual" name="harga_jual" value="<?= set_value('harga_jual') ?>" placeholder="Rp 0">
                    <div class="invalid-feedback">
                        <?= form_error('harga_jual') ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('menu') ?>" class="btn btn-secondary">Kembali</a>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var hargaBeliInput = document.getElementById('harga_beli');
    var hargaJualInput = document.getElementById('harga_jual');
    if(hargaBeliInput && hargaJualInput) {
        hargaBeliInput.addEventListener('input', function() {
            // Set harga jual default 20% lebih tinggi dari harga beli
            var hargaBeli = unformatRupiah(this.value) || 0;
            var hargaJual = Math.round(hargaBeli * 1.2);
            setRupiahValue('#harga_jual', hargaJual);
        });
    }
    // Toggle input pemilik baru
    var selectPemilik = document.getElementById('select-pemilik');
    var inputBaru = document.getElementById('input-pemilik-baru');
    function toggleInputPemilik() {
        if (selectPemilik.value === '__new__') {
            inputBaru.style.display = 'block';
            inputBaru.required = true;
        } else {
            inputBaru.style.display = 'none';
            inputBaru.required = false;
            inputBaru.value = '';
        }
    }
    selectPemilik.addEventListener('change', toggleInputPemilik);
    toggleInputPemilik(); // initial
});
</script> 