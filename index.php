<?php

session_start();

// =====================================================
// CEK LOGIN
// =====================================================

if (!isset($_SESSION["id_user"])) {
    header("Location: auth/login.php");
    exit;
}


// =====================================================
// KONEKSI DATABASE
// =====================================================

require_once "config/database.php";


// =====================================================
// STATISTIK DASHBOARD
// =====================================================

// -----------------------------------------------------
// TOTAL KEGIATAN
// -----------------------------------------------------

$query_kegiatan = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM tbl_kegiatan"
);

$data_kegiatan = mysqli_fetch_assoc($query_kegiatan);

$total_kegiatan = $data_kegiatan["total"];


// -----------------------------------------------------
// TOTAL PEGAWAI
// -----------------------------------------------------

$query_pegawai = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM tbl_pegawai"
);

$data_pegawai = mysqli_fetch_assoc($query_pegawai);

$total_pegawai = $data_pegawai["total"];


// -----------------------------------------------------
// TOTAL PAGU
// -----------------------------------------------------

$query_pagu = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(anggaran_pagu), 0) AS total
     FROM tbl_pagu"
);

$data_pagu = mysqli_fetch_assoc($query_pagu);

$total_pagu = $data_pagu["total"];


// =====================================================
// DATA KEGIATAN TERBARU
// =====================================================

$query_data_kegiatan = mysqli_query(
    $conn,
    "SELECT
        id_kegiatan,
        nama_kegiatan,
        tgl_mulai,
        tgl_target,
        target_kegiatan,
        realisasi_persen,
        risiko,
        status_kesiapan
     FROM tbl_kegiatan
     ORDER BY tgl_mulai DESC
     LIMIT 10"
);


// =====================================================
// JUDUL HALAMAN
// =====================================================

$title = "Dashboard";


// =====================================================
// TEMPLATE
// =====================================================

include "includes/header.php";
include "includes/sidebar.php";
include "includes/navbar.php";

?>


<!-- =====================================================
     CONTENT
===================================================== -->

<main class="main-content">


    <!-- =================================================
         JUDUL DASHBOARD
    ================================================== -->

    <div class="mb-4">

        <h2 class="page-title">
            Dashboard
        </h2>

        <p class="text-muted">
            Selamat datang di Sistem Monitoring PUSKIN.
        </p>

    </div>


    <!-- =================================================
         STATISTIK
    ================================================== -->

    <div class="row g-4">


        <!-- =============================================
             TOTAL KEGIATAN
        ============================================== -->

        <div class="col-md-6 col-xl-4">

            <div class="stat-card">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">
                            Total Kegiatan
                        </small>

                        <div class="stat-number">

                            <?= $total_kegiatan; ?>

                        </div>

                    </div>


                    <div class="stat-icon bg-primary-subtle text-primary">

                        <i class="bi bi-clipboard-data"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- =============================================
             TOTAL PEGAWAI
        ============================================== -->

        <div class="col-md-6 col-xl-4">

            <div class="stat-card">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">
                            Total Pegawai
                        </small>

                        <div class="stat-number">

                            <?= $total_pegawai; ?>

                        </div>

                    </div>


                    <div class="stat-icon bg-success-subtle text-success">

                        <i class="bi bi-people"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- =============================================
             TOTAL PAGU
        ============================================== -->

        <div class="col-md-6 col-xl-4">

            <div class="stat-card">

                <div class="d-flex justify-content-between">

                    <div>

                        <small class="text-muted">
                            Total Pagu
                        </small>

                        <div class="stat-number">

                            Rp <?= number_format(
                                $total_pagu,
                                0,
                                ',',
                                '.'
                            ); ?>

                        </div>

                    </div>


                    <div class="stat-icon bg-warning-subtle text-warning">

                        <i class="bi bi-wallet2"></i>

                    </div>

                </div>

            </div>

        </div>


    </div>


    <!-- =================================================
         MONITORING KEGIATAN
    ================================================== -->

    <div class="content-card mt-4">


        <!-- HEADER CARD -->

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h5 class="mb-1">
                    Monitoring Kegiatan
                </h5>

                <small class="text-muted">
                    Data kegiatan terbaru
                </small>

            </div>

        </div>


        <!-- =================================================
             TABLE
        ================================================== -->

        <div class="table-responsive">

            <table class="table table-hover align-middle">


                <!-- HEADER TABLE -->

                <thead>

                    <tr>

                        <th width="5%">
                            No
                        </th>

                        <th width="25%">
                            Nama Kegiatan
                        </th>

                        <th width="15%">
                            Target
                        </th>

                        <th width="12%">
                            Realisasi
                        </th>

                        <th width="12%">
                            Risiko
                        </th>

                        <th width="15%">
                            Status
                        </th>

                    </tr>

                </thead>


                <!-- BODY TABLE -->

                <tbody>


                <?php if (
                    $query_data_kegiatan &&
                    mysqli_num_rows($query_data_kegiatan) > 0
                ): ?>


                    <?php $no = 1; ?>


                    <?php while (
                        $row = mysqli_fetch_assoc(
                            $query_data_kegiatan
                        )
                    ): ?>


                        <tr>


                            <!-- NO -->

                            <td>

                                <?= $no++; ?>

                            </td>


                            <!-- NAMA KEGIATAN -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $row["nama_kegiatan"]
                                    ); ?>

                                </strong>

                            </td>


                            <!-- TARGET -->

                            <td>

                                <?php

                                if (
                                    isset($row["target_kegiatan"]) &&
                                    $row["target_kegiatan"] !== ""
                                ) {

                                    echo htmlspecialchars(
                                        $row["target_kegiatan"]
                                    );

                                } else {

                                    echo "-";

                                }

                                ?>

                            </td>


                            <!-- REALISASI -->

                            <td>

                                <strong>

                                    <?= (int) $row[
                                        "realisasi_persen"
                                    ]; ?>%

                                </strong>

                            </td>


                            <!-- RISIKO -->

                            <td>


                                <?php

                                $risiko = strtolower(
                                    trim(
                                        $row["risiko"] ?? ""
                                    )
                                );

                                ?>


                                <?php if (
                                    $risiko === "tinggi"
                                ): ?>

                                    <span class="badge bg-danger">

                                        Tinggi

                                    </span>


                                <?php elseif (
                                    $risiko === "sedang"
                                ): ?>

                                    <span class="badge bg-warning text-dark">

                                        Sedang

                                    </span>


                                <?php elseif (
                                    $risiko === "rendah"
                                ): ?>

                                    <span class="badge bg-success">

                                        Rendah

                                    </span>


                                <?php else: ?>

                                    <span class="badge bg-secondary">

                                        <?= $risiko !== ""
                                            ? htmlspecialchars(
                                                $row["risiko"]
                                            )
                                            : "-"
                                        ?>

                                    </span>

                                <?php endif; ?>


                            </td>


                            <!-- STATUS -->

                            <td>


                                <?php

                                $status = trim(
                                    $row["status_kesiapan"] ?? ""
                                );

                                $status_lower = strtolower(
                                    $status
                                );

                                ?>


                                <?php if (
                                    $status_lower === "siap"
                                ): ?>

                                    <span class="badge bg-success">

                                        Siap

                                    </span>


                                <?php elseif (
                                    $status_lower === "belum siap"
                                ): ?>

                                    <span class="badge bg-danger">

                                        Belum Siap

                                    </span>


                                <?php elseif (
                                    $status_lower === "proses"
                                ): ?>

                                    <span class="badge bg-warning text-dark">

                                        Proses

                                    </span>


                                <?php else: ?>

                                    <span class="badge bg-secondary">

                                        <?= $status !== ""
                                            ? htmlspecialchars(
                                                $status
                                            )
                                            : "-"
                                        ?>

                                    </span>

                                <?php endif; ?>


                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <!-- TIDAK ADA DATA -->

                    <tr>

                        <td
                            colspan="6"
                            class="text-center text-muted py-5"
                        >

                            <div class="mb-2">

                                <i
                                    class="bi bi-inbox"
                                    style="font-size: 35px;"
                                ></i>

                            </div>

                            <div>

                                Belum ada data kegiatan

                            </div>

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>


            </table>

        </div>


    </div>


</main>


<?php

// =====================================================
// FOOTER
// =====================================================

include "includes/footer.php";

?>