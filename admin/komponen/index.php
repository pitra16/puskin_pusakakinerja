<?php

session_start();

require_once "../../config/database.php";

/*
|--------------------------------------------------------------------------
| Cek Login
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION["id_user"])) {
    header("Location: ../../auth/login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Cek Level Admin
|--------------------------------------------------------------------------
*/
if (
    !isset($_SESSION["level"]) ||
    $_SESSION["level"] !== "admin"
) {
    header("Location: ../../index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data Komponen
|--------------------------------------------------------------------------
*/
$query = "
    SELECT
        id_komponen,
        kesiapan_persen
    FROM tbl_komponen
    ORDER BY id_komponen ASC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

$title = "Komponen";

?>

<?php include "../../includes/header.php"; ?>

<?php include "../../includes/sidebar.php"; ?>

<?php include "../../includes/navbar.php"; ?>


<!-- =====================================================
     CONTENT
===================================================== -->

<main class="main-content">

    <!-- HEADER HALAMAN -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="page-title mb-1">
                Komponen
            </h1>

            <p class="text-muted mb-0">
                Kelola master komponen kegiatan
            </p>
        </div>

        <a
            href="/puskin/admin/komponen/tambah.php"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Komponen
        </a>

    </div>


    <!-- =================================================
         TABEL KOMPONEN
    ================================================== -->

    <div class="content-card">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th width="60">No</th>
                        <th>ID Komponen</th>
                        <th width="350">Kesiapan</th>
                        <th width="120">Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($result) > 0): ?>

                        <?php
                        $no = 1;
                        ?>

                        <?php while ($row = mysqli_fetch_assoc($result)): ?>

                            <?php
                            $persen = (int) $row["kesiapan_persen"];

                            if ($persen < 0) {
                                $persen = 0;
                            }

                            if ($persen > 100) {
                                $persen = 100;
                            }
                            ?>

                            <tr>

                                <!-- NO -->
                                <td>
                                    <?= $no++; ?>
                                </td>


                                <!-- ID KOMPONEN -->
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row["id_komponen"]); ?>
                                    </strong>
                                </td>


                                <!-- KESIAPAN -->
                                <td>

                                    <div class="d-flex align-items-center gap-2">

                                        <div
                                            class="progress flex-grow-1"
                                            style="height: 8px;"
                                        >

                                            <div
                                                class="progress-bar"
                                                role="progressbar"
                                                style="width: <?= $persen; ?>%;"
                                                aria-valuenow="<?= $persen; ?>"
                                                aria-valuemin="0"
                                                aria-valuemax="100"
                                            ></div>

                                        </div>

                                        <strong>
                                            <?= $persen; ?>%
                                        </strong>

                                    </div>

                                </td>


                                <!-- AKSI -->
                                <td>

                                    <a
                                        href="/puskin/admin/komponen/hapus.php?id=<?= urlencode($row["id_komponen"]); ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus komponen ini?');"
                                        title="Hapus"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="4"
                                class="text-center text-muted py-4"
                            >
                                Belum ada data komponen.
                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>


<?php include "../../includes/footer.php"; ?>