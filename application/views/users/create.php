<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-plus mr-2"></i>Tambah User Baru
    </h1>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('users') ?>">Manajemen User</a></li>
        <li class="breadcrumb-item active">Tambah User</li>
    </ol>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow">
            <div class="card-header py-2 bg-gradient-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-user-plus mr-1"></i> Form Tambah User
                </h6>
            </div>
            <div class="card-body py-3">
                <form method="post" class="form-horizontal">
                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="username" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Username
                        </label>
                        <div class="col-sm-9">
                            <input type="text"
                                name="username"
                                id="username"
                                class="form-control form-control-sm <?= form_error('username') ? 'is-invalid' : '' ?>"
                                value="<?= set_value('username') ?>"
                                placeholder="Masukkan username"
                                required>
                            <?= form_error('username', '<div class="invalid-feedback">', '</div>') ?>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="password" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Password
                        </label>
                        <div class="col-sm-9">
                            <input type="password"
                                name="password"
                                id="password"
                                class="form-control form-control-sm <?= form_error('password') ? 'is-invalid' : '' ?>"
                                placeholder="Masukkan password"
                                required>
                            <?= form_error('password', '<div class="invalid-feedback">', '</div>') ?>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="confirm_password" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Konfirmasi Password
                        </label>
                        <div class="col-sm-9">
                            <input type="password"
                                name="confirm_password"
                                id="confirm_password"
                                class="form-control form-control-sm"
                                placeholder="Konfirmasi password"
                                required>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center">
                        <label for="role" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Role
                        </label>
                        <div class="col-sm-9">
                            <select name="role"
                                id="role"
                                class="form-control form-control-sm <?= form_error('role') ? 'is-invalid' : '' ?>"
                                required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin" <?= set_select('role', 'admin') ?>>Admin</option>
                                <option value="operator" <?= set_select('role', 'operator') ?>>Operator</option>
                                <option value="keuangan" <?= set_select('role', 'keuangan') ?>>Keuangan</option>
                                <option value="santri" <?= set_select('role', 'santri') ?>>Santri</option>
                            </select>
                            <?= form_error('role', '<div class="invalid-feedback">', '</div>') ?>
                        </div>
                    </div>
                    <div class="form-group row mb-2 align-items-center" id="gender-group">
                        <label for="gender" class="col-sm-3 col-form-label font-weight-bold text-gray-700 text-right">
                            Gender
                        </label>
                        <div class="col-sm-9">
                            <select name="gender"
                                id="gender"
                                class="form-control form-control-sm <?= form_error('gender') ? 'is-invalid' : '' ?>">
                                <option value="">-- Pilih Gender --</option>
                                <option value="L" <?= set_select('gender', 'L') ?>>Laki-laki</option>
                                <option value="P" <?= set_select('gender', 'P') ?>>Perempuan</option>
                            </select>
                            <?= form_error('gender', '<div class="invalid-feedback">', '</div>') ?>
                        </div>
                    </div>
                    <div class="form-group row mt-2">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary btn-sm mr-2">
                                <i class="fas fa-save mr-1"></i> Simpan User
                            </button>
                            <a href="<?= base_url('users') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Fungsi untuk mengatur gender field berdasarkan role
        function toggleGenderField() {
            var role = $('#role').val();
            var genderGroup = $('#gender-group');
            var genderSelect = $('#gender');

            if (role === 'admin' || role === 'keuangan') {
                genderGroup.hide();
                genderSelect.removeAttr('required');
                genderSelect.val('');
            } else {
                genderGroup.show();
                genderSelect.attr('required', 'required');
            }
        }

        // Jalankan saat halaman dimuat
        toggleGenderField();

        // Jalankan saat role berubah
        $('#role').on('change', function() {
            toggleGenderField();
        });

        // Validasi konfirmasi password
        $('#confirm_password').on('input', function() {
            var password = $('#password').val();
            var confirmPassword = $(this).val();

            if (password !== confirmPassword) {
                $(this).addClass('is-invalid');
                $(this).removeClass('is-valid');
            } else {
                $(this).removeClass('is-invalid');
                $(this).addClass('is-valid');
            }
        });

        // Validasi form sebelum submit
        $('form').on('submit', function(e) {
            var password = $('#password').val();
            var confirmPassword = $('#confirm_password').val();

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak sama!');
                return false;
            }

            // Validasi gender untuk role selain admin dan keuangan
            var role = $('#role').val();
            var gender = $('#gender').val();

            if (role !== 'admin' && role !== 'keuangan' && !gender) {
                e.preventDefault();
                alert('Gender wajib dipilih untuk role operator dan santri!');
                return false;
            }
        });
    });
</script>