<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Import Data Santri CSV</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">Import Data Santri (CSV)</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if (!empty($result)): ?>
            <div class="alert alert-<?= $result['success'] ? 'success' : 'danger' ?>">
                <?= $result['message'] ?>
            </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="card p-4">
            <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
            <div class="form-group">
                <label for="csv_file">Pilih File CSV</label>
                <input type="file" name="csv_file" id="csv_file" class="form-control-file" accept=".csv" required>
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
            <a href="<?= site_url('importsantri/template') ?>" class="btn btn-success ml-2">Download Template CSV</a>
        </form>
        <div class="mt-4">
            <b>Format CSV:</b><br>
            Nomor Induk, Nama, Angkatan, Jenis Kelamin (L/P)
        </div>
    </div>
</body>

</html>