<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url() ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Tabungan & Kantin</div>
    </a>
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('dashboard') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <!-- Master Data Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#masterDataSubmenu" aria-expanded="true" aria-controls="masterDataSubmenu">
            <i class="fas fa-database"></i>
            <span>Master Data</span>
        </a>
        <div id="masterDataSubmenu" class="collapse" aria-labelledby="headingMasterData" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <?php if ($this->session->userdata('role') === 'admin'): ?>
                    <a class="collapse-item" href="<?= base_url('users') ?>">Manajemen User</a>
                <?php endif; ?>
                <a class="collapse-item" href="<?= base_url('santri') ?>">Data Santri</a>
                <a class="collapse-item" href="<?= base_url('ustadz') ?>">Data Ustadz/Ustadzah</a>
            </div>
        </div>
    </li>
    <hr class="sidebar-divider">

    <!-- Tabungan Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#tabunganSubmenu" aria-expanded="true" aria-controls="tabunganSubmenu">
            <i class="fas fa-wallet"></i>
            <span>Tabungan</span>
        </a>
        <div id="tabunganSubmenu" class="collapse" aria-labelledby="headingTabungan" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('santri') ?>">Data Santri</a>
                <a class="collapse-item" href="<?= base_url('tabungan') ?>">Data Tabungan</a>
                <a class="collapse-item" href="<?= base_url('tabungan/riwayat') ?>">Riwayat Transaksi</a>
            </div>
        </div>
    </li>

    <!-- Kantin Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#kantinSubmenu" aria-expanded="true" aria-controls="kantinSubmenu">
            <i class="fas fa-utensils"></i>
            <span>Kantin</span>
        </a>
        <div id="kantinSubmenu" class="collapse" aria-labelledby="headingKantin" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('pos/modern') ?>">Point Of Sales (POS)</a>
                <a class="collapse-item" href="<?= base_url('menu') ?>">Menu Kantin</a>
                <a class="collapse-item" href="<?= base_url('menu/stok_management') ?>">Manajemen Stok</a>
                <a class="collapse-item" href="<?= base_url('menu/riwayat_stok') ?>">Riwayat Stok</a>
            </div>
        </div>
    </li>

    <!-- Laporan Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#laporanSubmenu" aria-expanded="true" aria-controls="laporanSubmenu">
            <i class="fas fa-chart-bar"></i>
            <span>Laporan</span>
        </a>
        <div id="laporanSubmenu" class="collapse" aria-labelledby="headingLaporan" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= base_url('laporan') ?>">Transaksi Harian</a>
                <a class="collapse-item" href="<?= base_url('laporan/mingguan') ?>">Transaksi Mingguan</a>
                <a class="collapse-item" href="<?= base_url('laporan/bulanan') ?>">Transaksi Bulanan</a>
            </div>
        </div>
    </li>

    <!-- Activity Log Menu -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('activity-logs') ?>">
            <i class="fas fa-clipboard-list"></i>
            <span>Activity Log</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->