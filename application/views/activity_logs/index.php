<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Activity Logs</h1>
    <div>
        <a href="<?= base_url('activity-logs/export') ?>" class="btn btn-success btn-sm">
            <i class="fas fa-download fa-sm"></i> Export CSV
        </a>
        <a href="<?= base_url('activity-logs/dashboard') ?>" class="btn btn-info btn-sm">
            <i class="fas fa-chart-bar fa-sm"></i> Dashboard
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= base_url('activity-logs') ?>">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" class="form-control form-control-sm">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category ?>" <?= ($filters['category'] == $category) ? 'selected' : '' ?>>
                                    <?= $category ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Status</option>
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= $status ?>" <?= ($filters['status'] == $status) ? 'selected' : '' ?>>
                                    <?= ucfirst($status) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm"
                            value="<?= $filters['date_from'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                            value="<?= $filters['date_to'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Action</label>
                        <select name="action" class="form-control form-control-sm">
                            <option value="">All Actions</option>
                            <option value="LOGIN_SUCCESS" <?= ($filters['action'] == 'LOGIN_SUCCESS') ? 'selected' : '' ?>>Login Success</option>
                            <option value="LOGIN_FAILED" <?= ($filters['action'] == 'LOGIN_FAILED') ? 'selected' : '' ?>>Login Failed</option>
                            <option value="LOGOUT" <?= ($filters['action'] == 'LOGOUT') ? 'selected' : '' ?>>Logout</option>
                            <option value="SETORAN_TABUNGAN" <?= ($filters['action'] == 'SETORAN_TABUNGAN') ? 'selected' : '' ?>>Setoran Tabungan</option>
                            <option value="PENARIKAN_TABUNGAN" <?= ($filters['action'] == 'PENARIKAN_TABUNGAN') ? 'selected' : '' ?>>Penarikan Tabungan</option>
                            <option value="TRANSFER_KATEGORI" <?= ($filters['action'] == 'TRANSFER_KATEGORI') ? 'selected' : '' ?>>Transfer Kategori</option>
                            <option value="TRANSFER_ANTAR_SANTRI" <?= ($filters['action'] == 'TRANSFER_ANTAR_SANTRI') ? 'selected' : '' ?>>Transfer Antar Santri</option>
                            <option value="DASHBOARD_ADMIN_VIEW" <?= ($filters['action'] == 'DASHBOARD_ADMIN_VIEW') ? 'selected' : '' ?>>Dashboard Admin View</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search fa-sm"></i> Filter
                            </button>
                            <a href="<?= base_url('activity-logs') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times fa-sm"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Card -->
<?php if (isset($stats) && !empty($stats)): ?>
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="<?= base_url('activity-logs') ?>" style="text-decoration:none">
                <div class="card border-left-primary shadow h-100 py-2 card-filter" style="cursor:pointer;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Logs
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_logs ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="<?= base_url('activity-logs?status=success') ?>" style="text-decoration:none">
                <div class="card border-left-success shadow h-100 py-2 card-filter" style="cursor:pointer;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Success
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= isset($stats['success']) ? $stats['success'] : 0 ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="<?= base_url('activity-logs?status=warning') ?>" style="text-decoration:none">
                <div class="card border-left-warning shadow h-100 py-2 card-filter" style="cursor:pointer;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Warnings
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= isset($stats['warning']) ? $stats['warning'] : 0 ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="<?= base_url('activity-logs?status=error') ?>" style="text-decoration:none">
                <div class="card border-left-danger shadow h-100 py-2 card-filter" style="cursor:pointer;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Errors
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= isset($stats['error']) ? $stats['error'] : 0 ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Activity Logs Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Activity Logs</h6>
    </div>
    <div class="card-body">
        <?php if (empty($logs)): ?>
            <div class="text-center py-4">
                <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                <p class="text-gray-500">No activity logs found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Category</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Device</th>
                            <th>IP Address</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= $log->id ?></td>
                                <td><?= $log->user_username ?? $log->username ?? 'System' ?></td>
                                <td>
                                    <?php
                                    $user_display = $log->user_username ?? $log->username ?? 'System';
                                    $role_display = '';
                                    if (strtolower($user_display) === 'system' && !empty($log->username)) {
                                        $role_display = ucfirst($log->username);
                                    } else {
                                        $role_display = ucfirst($log->role ?? '');
                                    }
                                    ?>
                                    <span class="badge badge-<?= $log->role == 'admin' ? 'danger' : ($log->role == 'keuangan' ? 'warning' : 'info') ?>">
                                        <?= $role_display ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary"><?= $log->category ?></span>
                                </td>
                                <td><?= $log->action ?></td>
                                <td>
                                    <?php
                                    $status_class = 'secondary';
                                    if ($log->status == 'success') $status_class = 'success';
                                    elseif ($log->status == 'warning') $status_class = 'warning';
                                    elseif ($log->status == 'error') $status_class = 'danger';
                                    elseif ($log->status == 'critical') $status_class = 'dark';
                                    ?>
                                    <span class="badge badge-<?= $status_class ?>">
                                        <?= ucfirst($log->status ?? '') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $device_info = null;
                                    if ($log->details) {
                                        $details = json_decode($log->details, true);
                                        $device_info = isset($details['device_info']) ? $details['device_info'] : null;
                                    }
                                    ?>
                                    <?php if ($device_info): ?>
                                        <div class="small">
                                            <div class="font-weight-bold"><?= $device_info['browser'] ?> <?= $device_info['browser_version'] ?></div>
                                            <div class="text-muted"><?= $device_info['os'] ?></div>
                                            <div class="text-muted">
                                                <i class="fas fa-<?= $device_info['device_type'] == 'Mobile' ? 'mobile-alt' : ($device_info['device_type'] == 'Tablet' ? 'tablet-alt' : 'desktop') ?>"></i>
                                                <?= $device_info['device_type'] ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <small class="text-muted">Unknown</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?= $log->ip_address ?></small>
                                </td>
                                <td>
                                    <small><?= date('d/m/Y H:i', strtotime($log->created_at)) ?></small>
                                </td>
                                <td>
                                    <a href="<?= base_url('activity-logs/view/' . $log->id) ?>"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('activity-logs?page=' . ($current_page - 1)) ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('activity-logs?page=' . $i) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= base_url('activity-logs?page=' . ($current_page + 1)) ?>">
                                    Next
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Clean Old Logs Modal -->
<div class="modal fade" id="cleanLogsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clean Old Logs</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= base_url('activity-logs/clean') ?>" method="POST">
                <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <div class="modal-body">
                    <p>This will permanently delete logs older than the specified number of days.</p>
                    <div class="form-group">
                        <label>Delete logs older than (days):</label>
                        <input type="number" name="days" class="form-control" value="90" min="1" max="365">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Clean Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#dataTable').DataTable({
            "pageLength": 25,
            "order": [
                [0, "desc"]
            ]
        });

        // Card filter click: set status filter and submit form
        $('.card-filter').parent('a').on('click', function(e) {
            var href = $(this).attr('href');
            var url = new URL(href, window.location.origin);
            var status = url.searchParams.get('status');
            if (status) {
                e.preventDefault();
                $("select[name='status']").val(status);
                $(this).closest('.row').prev('form').submit();
            }
            // jika tidak ada status, biarkan default (reset filter)
        });
    });
</script>