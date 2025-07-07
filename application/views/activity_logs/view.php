<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Activity Log Detail</h1>
    <a href="<?= base_url('activity-logs') ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Back to Logs
    </a>
</div>

<?php if (isset($log) && $log): ?>
    <div class="row">
        <div class="col-lg-8">
            <!-- Log Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Log Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td><?= $log->id ?></td>
                                </tr>
                                <tr>
                                    <td><strong>User:</strong></td>
                                    <td><?= $log->user_username ?? $log->username ?? 'System' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Role:</strong></td>
                                    <td>
                                        <span class="badge badge-<?= $log->role == 'admin' ? 'danger' : ($log->role == 'keuangan' ? 'warning' : 'info') ?>">
                                            <?= ucfirst($log->role ?? '') ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>
                                        <span class="badge badge-secondary"><?= $log->category ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Action:</strong></td>
                                    <td><?= $log->action ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
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
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>IP Address:</strong></td>
                                    <td><?= $log->ip_address ?></td>
                                </tr>
                                <tr>
                                    <td><strong>User Agent:</strong></td>
                                    <td>
                                        <small class="text-muted"><?= $log->user_agent ?></small>
                                    </td>
                                </tr>
                                <?php
                                $device_info = null;
                                if ($log->details && isset($log->details_array['device_info'])) {
                                    $device_info = $log->details_array['device_info'];
                                }
                                ?>
                                <?php if ($device_info): ?>
                                <tr>
                                    <td><strong>Device Info:</strong></td>
                                    <td>
                                        <div class="small">
                                            <div class="mb-1">
                                                <i class="fas fa-globe text-primary"></i>
                                                <strong>Browser:</strong> <?= $device_info['browser'] ?> <?= $device_info['browser_version'] ?>
                                            </div>
                                            <div class="mb-1">
                                                <i class="fas fa-desktop text-info"></i>
                                                <strong>OS:</strong> <?= $device_info['os'] ?>
                                            </div>
                                            <div class="mb-1">
                                                <i class="fas fa-<?= $device_info['device_type'] == 'Mobile' ? 'mobile-alt' : ($device_info['device_type'] == 'Tablet' ? 'tablet-alt' : 'desktop') ?> text-success"></i>
                                                <strong>Device:</strong> <?= $device_info['device_type'] ?>
                                                <?php if ($device_info['mobile']): ?>
                                                    <span class="badge badge-info badge-sm">Mobile</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td><strong>Created At:</strong></td>
                                    <td><?= date('d/m/Y H:i:s', strtotime($log->created_at)) ?></td>
                                </tr>
                                <?php if (isset($log->amount) && $log->amount): ?>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td>Rp <?= number_format($log->amount, 0, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (isset($log->item_id) && $log->item_id): ?>
                                <tr>
                                    <td><strong>Item ID:</strong></td>
                                    <td><?= $log->item_id ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (isset($log->quantity) && $log->quantity): ?>
                                <tr>
                                    <td><strong>Quantity:</strong></td>
                                    <td><?= $log->quantity ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Card -->
            <?php if (isset($log->details) && $log->details): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Details</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($log->details_array) && is_array($log->details_array)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Key</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($log->details_array as $key => $value): ?>
                                        <tr>
                                            <td><strong><?= ucfirst(str_replace('_', ' ', $key)) ?></strong></td>
                                            <td>
                                                <?php if (is_array($value)): ?>
                                                    <pre class="mb-0"><code><?= json_encode($value, JSON_PRETTY_PRINT) ?></code></pre>
                                                <?php else: ?>
                                                    <?= $value ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <pre><code><?= $log->details ?></code></pre>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Related Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Related Actions</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="<?= base_url('activity-logs') ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-list fa-fw"></i> All Logs
                        </a>
                        <a href="<?= base_url('activity-logs?category=' . $log->category) ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-filter fa-fw"></i> Same Category
                        </a>
                        <a href="<?= base_url('activity-logs?action=' . $log->action) ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-search fa-fw"></i> Same Action
                        </a>
                        <?php if ($log->user_id): ?>
                        <a href="<?= base_url('activity-logs?user_id=' . $log->user_id) ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-user fa-fw"></i> User Activities
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-2">
                            <strong>Category:</strong> <?= $log->category ?><br>
                            <small class="text-muted"><?= $log->category ?> activities</small>
                        </div>
                        <div class="mb-2">
                            <strong>Status:</strong> <?= ucfirst($log->status ?? '') ?><br>
                            <small class="text-muted"><?= $log->status ?? '' ?> events</small>
                        </div>
                        <div class="mb-2">
                            <strong>Time:</strong> <?= date('H:i', strtotime($log->created_at)) ?><br>
                            <small class="text-muted"><?= date('l, d F Y', strtotime($log->created_at)) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card shadow mb-4">
        <div class="card-body text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <h5>Log Not Found</h5>
            <p class="text-gray-500">The requested activity log could not be found.</p>
            <a href="<?= base_url('activity-logs') ?>" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Logs
            </a>
        </div>
    </div>
<?php endif; ?> 