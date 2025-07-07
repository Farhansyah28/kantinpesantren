<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Activity Logs Dashboard</h1>
    <a href="<?= base_url('activity-logs') ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-list fa-sm"></i> View All Logs
    </a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Activities (30 days)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= isset($stats_30_days['total']) ? $stats_30_days['total'] : 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Success Rate (7 days)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                            $success_rate = 0;
                            if (isset($stats_7_days['total']) && $stats_7_days['total'] > 0) {
                                $success_rate = round(($stats_7_days['success'] ?? 0) / $stats_7_days['total'] * 100);
                            }
                            echo $success_rate . '%';
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Active Users (7 days)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= isset($top_users) ? count($top_users) : 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Errors (7 days)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= isset($stats_7_days['error']) ? $stats_7_days['error'] : 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activities -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
            </div>
            <div class="card-body">
                <?php if (empty($recent_activities)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No recent activities found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_activities as $activity): ?>
                                    <tr>
                                        <td>
                                            <small><?= date('H:i', strtotime($activity->created_at)) ?></small>
                                        </td>
                                        <td>
                                            <small><?= $activity->user_username ?? $activity->username ?? 'System' ?></small>
                                        </td>
                                        <td>
                                            <small><?= $activity->action ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = 'secondary';
                                            if ($activity->status == 'success') $status_class = 'success';
                                            elseif ($activity->status == 'warning') $status_class = 'warning';
                                            elseif ($activity->status == 'error') $status_class = 'danger';
                                            ?>
                                            <span class="badge badge-<?= $status_class ?> badge-sm">
                                                <?= ucfirst($activity->status) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top Users -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Active Users (30 days)</h6>
            </div>
            <div class="card-body">
                <?php if (empty($top_users)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No user activity data.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($top_users as $user): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong><?= $user->username ?></strong>
                                <br>
                                <small class="text-muted"><?= $user->activity_count ?> activities</small>
                            </div>
                            <div class="text-right">
                                <div class="progress" style="width: 60px; height: 6px;">
                                    <?php 
                                    $max_activities = $top_users[0]->activity_count;
                                    $percentage = $max_activities > 0 ? ($user->activity_count / $max_activities) * 100 : 0;
                                    ?>
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Activity by Hour Chart -->
<?php if (isset($activity_by_hour) && !empty($activity_by_hour)): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Activity by Hour (Last 24 Hours)</h6>
            </div>
            <div class="card-body">
                <canvas id="activityByHourChart"></canvas>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
$(document).ready(function() {
    <?php if (isset($activity_by_hour) && !empty($activity_by_hour)): ?>
    // Activity by Hour Chart
    var ctx = document.getElementById('activityByHourChart').getContext('2d');
    var activityByHourChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                <?php 
                $labels = [];
                $data = [];
                foreach ($activity_by_hour as $hour) {
                    $labels[] = $hour->hour . ':00';
                    $data[] = $hour->count;
                }
                echo "'" . implode("', '", $labels) . "'";
                ?>
            ],
            datasets: [{
                label: 'Activities',
                data: [<?= implode(', ', $data) ?>],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    <?php endif; ?>
});
</script> 