<aside class="sidebar">

    <!-- BRAND -->

    <div class="sidebar-brand">

        <div class="brand-icon">

            <i class="bi bi-building"></i>

        </div>


        <div>

            <h5>PUSKIN</h5>

            <small>
                Sistem Monitoring
            </small>

        </div>

    </div>


    <!-- MENU -->

    <div class="sidebar-menu">


        <p class="menu-title">
            MENU UTAMA
        </p>


        <!-- DASHBOARD -->

        <a
            href="<?= BASE_URL; ?>/index.php"
            class="menu-item"
        >

            <i class="bi bi-grid-1x2-fill"></i>

            <span>
                Dashboard
            </span>

        </a>


        <!-- KEGIATAN -->

        <a
            href="<?= BASE_URL; ?>/admin/kegiatan/index.php"
            class="menu-item"
        >

            <i class="bi bi-clipboard-data"></i>

            <span>
                Kegiatan
            </span>

        </a>


        <!-- PEGAWAI -->

        <a
            href="<?= BASE_URL; ?>/admin/pegawai/index.php"
            class="menu-item active"
        >

            <i class="bi bi-people"></i>

            <span>
                Pegawai
            </span>

        </a>


        <!-- PAGU -->

        <a
            href="<?= BASE_URL; ?>/admin/pagu/index.php"
            class="menu-item"
        >

            <i class="bi bi-wallet2"></i>

            <span>
                Pagu Anggaran
            </span>

        </a>


        <!-- KOMPONEN -->

        <a
            href="<?= BASE_URL; ?>/admin/komponen/index.php"
            class="menu-item"
        >

            <i class="bi bi-puzzle"></i>

            <span>
                Komponen
            </span>

        </a>


        <!-- LAINNYA -->

        <p class="menu-title mt-4">
            LAINNYA
        </p>


        <!-- LOG -->

        <a
            href="<?= BASE_URL; ?>/admin/log/index.php"
            class="menu-item"
        >

            <i class="bi bi-clock-history"></i>

            <span>
                Log Kegiatan
            </span>

        </a>


        <!-- PENGATURAN -->

        <a
            href="<?= BASE_URL; ?>/admin/pengaturan/index.php"
            class="menu-item"
        >

            <i class="bi bi-gear"></i>

            <span>
                Pengaturan
            </span>

        </a>


        <!-- LOGOUT -->

        <a
            href="<?= BASE_URL; ?>/auth/logout.php"
            class="menu-item logout"
        >

            <i class="bi bi-box-arrow-right"></i>

            <span>
                Logout
            </span>

        </a>


    </div>

</aside>