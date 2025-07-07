<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen User</h1>
    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success"> <?= $this->session->flashdata('success') ?> </div>
    <?php endif; ?>
    
    <a href="<?= base_url('users/create') ?>" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Tambah User</a>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Gender</th>
                            <th>Terakhir Login</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($users) && !empty($users)): ?>
                            <?php $no=1; foreach($users as $user): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= html_escape($user->username) ?></td>
                                <td><span class="badge badge-info"><?= $user->role ?></span></td>
                                <td><?= $user->gender ? ($user->gender == 'L' ? 'Laki-laki' : 'Perempuan') : '-' ?></td>
                                <td><?= $user->terakhir_login ? date('d/m/Y H:i', strtotime($user->terakhir_login)) : '-' ?></td>
                                <td>
                                    <a href="<?= base_url('users/edit/'.$user->id) ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="<?= base_url('users/delete/'.$user->id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data users</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 