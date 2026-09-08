<?php
$current_page = $_SERVER["PHP_SELF"];
?>

<aside class="sidebar">

    <!-- BRAND -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-building"></i>
        </div>

        <div>
            <h5>PUSKIN</h5>
            <small>Sistem Monitoring</small>
        </div>
    </div>

    <!-- MENU -->
    <div class="sidebar-menu">

        <p class="menu-title">MENU UTAMA</p>

        <!-- DASHBOARD -->
        <a href="/puskin/index.php"
           class="menu-item <?= (
               basename($current_page) === "index.php"
               && strpos($current_page, "/admin/") === false
           ) ? "active" : ""; ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <!-- KEGIATAN -->
        <a href="/puskin/admin/kegiatan/index.php"
           class="menu-item <?= (
               strpos($current_page, "/admin/kegiatan/") !== false
           ) ? "active" : ""; ?>">
            <i class="bi bi-clipboard-data"></i>
            <span>Kegiatan</span>
        </a>

        <!-- PEGAWAI -->
        <a href="/puskin/admin/pegawai/index.php"
           class="menu-item <?= (
               strpos($current_page, "/admin/pegawai/") !== false
           ) ? "active" : ""; ?>">
            <i class="bi bi-people"></i>
            <span>Pegawai</span>
        </a>

        <!-- PAGU -->
        <a href="/puskin/admin/pagu/index.php"
           class="menu-item <?= (
               strpos($current_page, "/admin/pagu/") !== false
           ) ? "active" : ""; ?>">
            <i class="bi bi-wallet2"></i>
            <span>Pagu Anggaran</span>
        </a>

        <!-- KOMPONEN -->
        <a href="/puskin/admin/komponen/index.php"
           class="menu-item <?= (
               strpos($current_page, "/admin/komponen/") !== false
           ) ? "active" : ""; ?>">
            <i class="bi bi-puzzle"></i>
            <span>Komponen</span>
        </a>


        <!-- LAINNYA -->
        <p class="menu-title mt-4">LAINNYA</p>

        <!-- LOG KEGIATAN -->
        <a href="/puskin/admin/log/index.php"
           class="menu-item <?= (
               strpos($current_page, "/admin/log/") !== false
           ) ? "active" : ""; ?>">
            <i class="bi bi-clock-history"></i>
            <span>Log Kegiatan</span>
        </a>

        <!-- PENGATURAN -->
        <a href="/puskin/admin/pengaturan/index.php"
           class="menu-item <?= (
               strpos($current_page, "/admin/pengaturan/") !== false
           ) ? "active" : ""; ?>">
            <i class="bi bi-gear"></i>
            <span>Pengaturan</span>
        </a>


        <!-- LOGOUT -->
        <div class="logout-divider"></div>

        <a href="/puskin/auth/logout.php"
           class="menu-item logout"
           onclick="return confirm('Yakin ingin logout dari sistem?');">

            <i class="bi bi-box-arrow-right"></i>

            <span>Logout</span>

        </a>

    </div>

</aside>