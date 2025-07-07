<?php
$active_menu = in_array($this->uri->segment(1), ['pos', 'menu']) ? 'show' : '';
?>
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('dashboard') ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-store"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Operator Kantin</div>
    </a>
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('dashboard') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        Menu Utama
    </div>

    <!-- Kantin Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#kantinSubmenu" aria-expanded="true" aria-controls="kantinSubmenu">
            <i class="fas fa-utensils"></i>
            <span>Kantin</span>
        </a>
        <div id="kantinSubmenu" class="collapse" aria-labelledby="headingKantin" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Fitur Utama:</h6>
                <a class="collapse-item" href="<?= base_url('pos/modern') ?>">Point Of Sales (POS)</a>
                <a class="collapse-item" href="<?= base_url('menu') ?>">Daftar Menu</a>
                <!-- <a class="collapse-item" href="<?= base_url('menu/stok_management') ?>">Manajemen Stok</a> -->
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
                <h6 class="collapse-header">Jenis Laporan:</h6>
                <a class="collapse-item" href="<?= base_url('pos/riwayat_hari_ini') ?>">Transaksi Hari Ini</a>
                <a class="collapse-item" href="<?= base_url('laporan') ?>">Laporan Harian</a>
                <a class="collapse-item" href="<?= base_url('laporan/mingguan') ?>">Laporan Mingguan</a>
                <a class="collapse-item" href="<?= base_url('laporan/bulanan') ?>">Laporan Bulanan</a>
            </div>
        </div>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('dashboard/histori_setoran') ?>">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Histori Setoran Pemilik</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar --> 