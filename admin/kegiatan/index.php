<?php

session_start();


// =====================================================
// CEK LOGIN
// =====================================================

if (!isset($_SESSION["id_user"])) {

    header("Location: ../../auth/login.php");

    exit;
}


// =====================================================
// CEK LEVEL
// =====================================================

if (
    !isset($_SESSION["level"]) ||
    strtolower($_SESSION["level"]) !== "admin"
) {

    header("Location: ../../index.php");

    exit;
}


// =====================================================
// DATABASE
// =====================================================

require_once "../../config/database.php";


// =====================================================
// DATA KEGIATAN
// =====================================================

$query = mysqli_query(
    $conn,
    "SELECT
        k.id_kegiatan,
        k.nama_kegiatan,
        k.tgl_mulai,
        k.tgl_target,
        k.target_kegiatan,
        k.realisasi_pagu,
        k.realisasi_persen,
        k.risiko,
        k.level_risiko,
        k.mitigasi,
        k.status_kesiapan,
        k.catatan,

        p.nama_pegawai,

        a.nama_pagu,
        a.anggaran_pagu

     FROM tbl_kegiatan k

     LEFT JOIN tbl_pegawai p
        ON k.id_pegawai = p.id_pegawai

     LEFT JOIN tbl_pagu a
        ON k.id_pagu = a.id_pagu

     ORDER BY k.tgl_target ASC, k.nama_kegiatan ASC"
);


// =====================================================
// JUDUL
// =====================================================

$title = "Data Kegiatan";


// =====================================================
// TEMPLATE
// =====================================================

include "../../includes/header.php";

include "../../includes/sidebar.php";

include "../../includes/navbar.php";

?>


<main class="main-content">


    <!-- =================================================
         HEADER
    ================================================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="page-title mb-1">
                Data Kegiatan
            </h2>

            <p class="text-muted mb-0">
                Kelola kegiatan dan monitoring pelaksanaannya.
            </p>

        </div>


        <a
            href="tambah.php"
            class="btn btn-primary"
        >

            <i class="bi bi-plus-lg me-2"></i>

            Tambah Kegiatan

        </a>

    </div>


    <!-- =================================================
         NOTIFIKASI
    ================================================== -->

    <?php if (isset($_GET["status"])): ?>


        <?php if ($_GET["status"] === "tambah"): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                Data kegiatan berhasil ditambahkan.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>


        <?php elseif ($_GET["status"] === "edit"): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                Data kegiatan berhasil diperbarui.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>


        <?php elseif ($_GET["status"] === "hapus"): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                Data kegiatan berhasil dihapus.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>


        <?php endif; ?>


    <?php endif; ?>


    <!-- =================================================
         CARD
    ================================================== -->

    <div class="content-card">


        <!-- HEADER CARD -->

        <div class="mb-4">

            <h5 class="mb-1">
                Daftar Kegiatan
            </h5>

            <small class="text-muted">
                Seluruh kegiatan yang terdaftar dalam sistem.
            </small>

        </div>


        <!-- =================================================
             TABLE
        ================================================== -->

        <div class="table-responsive">

            <table class="table table-hover align-middle">


                <thead>

                    <tr>

                        <th width="5%">
                            No
                        </th>

                        <th>
                            Kegiatan
                        </th>

                        <th>
                            Pegawai
                        </th>

                        <th>
                            Pagu
                        </th>

                        <th>
                            Target
                        </th>

                        <th>
                            Realisasi
                        </th>

                        <th>
                            Risiko
                        </th>

                        <th>
                            Status
                        </th>

                        <th width="12%">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php if (
                    $query &&
                    mysqli_num_rows($query) > 0
                ): ?>


                    <?php $no = 1; ?>


                    <?php while (
                        $row = mysqli_fetch_assoc($query)
                    ): ?>


                        <tr>


                            <!-- =================================================
                                 NO
                            ================================================== -->

                            <td>

                                <?= $no++; ?>

                            </td>


                            <!-- =================================================
                                 KEGIATAN
                            ================================================== -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $row["nama_kegiatan"]
                                    ); ?>

                                </strong>

                                <?php if (!empty($row["target_kegiatan"])): ?>

                                    <div>

                                        <small class="text-muted">

                                            <?= htmlspecialchars(
                                                $row["target_kegiatan"]
                                            ); ?>

                                        </small>

                                    </div>

                                <?php endif; ?>

                            </td>


                            <!-- =================================================
                                 PEGAWAI
                            ================================================== -->

                            <td>

                                <?= htmlspecialchars(
                                    $row["nama_pegawai"] ?? "-"
                                ); ?>

                            </td>


                            <!-- =================================================
                                 PAGU
                            ================================================== -->

                            <td>

                                <?php if (!empty($row["nama_pagu"])): ?>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $row["nama_pagu"]
                                        ); ?>

                                    </strong>

                                    <div>

                                        <small class="text-muted">

                                            Rp
                                            <?= number_format(
                                                (int) $row["anggaran_pagu"],
                                                0,
                                                ",",
                                                "."
                                            ); ?>

                                        </small>

                                    </div>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </td>


                            <!-- =================================================
                                 TARGET
                            ================================================== -->

                            <td>

                                <?php if (
                                    !empty($row["tgl_mulai"]) ||
                                    !empty($row["tgl_target"])
                                ): ?>

                                    <small>

                                        <?php if (!empty($row["tgl_mulai"])): ?>

                                            <?= date(
                                                "d/m/Y",
                                                strtotime($row["tgl_mulai"])
                                            ); ?>

                                        <?php endif; ?>


                                        <?php if (
                                            !empty($row["tgl_mulai"]) &&
                                            !empty($row["tgl_target"])
                                        ): ?>

                                            <br>
                                            s/d
                                            <br>

                                        <?php endif; ?>


                                        <?php if (!empty($row["tgl_target"])): ?>

                                            <?= date(
                                                "d/m/Y",
                                                strtotime($row["tgl_target"])
                                            ); ?>

                                        <?php endif; ?>

                                    </small>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </td>


                            <!-- =================================================
                                 REALISASI
                            ================================================== -->

                            <td>

                                <strong>

                                    Rp
                                    <?= number_format(
                                        (int) ($row["realisasi_pagu"] ?? 0),
                                        0,
                                        ",",
                                        "."
                                    ); ?>

                                </strong>


                                <div class="mt-1">

                                    <?php

                                    $persen = (int) (
                                        $row["realisasi_persen"] ?? 0
                                    );

                                    if ($persen < 0) {
                                        $persen = 0;
                                    }

                                    if ($persen > 100) {
                                        $persen = 100;
                                    }

                                    ?>


                                    <div
                                        class="progress"
                                        style="height: 6px;"
                                    >

                                        <div
                                            class="progress-bar"
                                            role="progressbar"
                                            style="width: <?= $persen; ?>%;"
                                        ></div>

                                    </div>


                                    <small class="text-muted">

                                        <?= $persen; ?>%

                                    </small>

                                </div>

                            </td>


                            <!-- =================================================
                                 RISIKO
                            ================================================== -->

                            <td>

                                <?php

                                $level_risiko = strtolower(
                                    trim(
                                        $row["level_risiko"] ?? ""
                                    )
                                );

                                ?>


                                <?php if (
                                    $level_risiko === "tinggi"
                                ): ?>

                                    <span class="badge bg-danger">
                                        Tinggi
                                    </span>


                                <?php elseif (
                                    $level_risiko === "sedang"
                                ): ?>

                                    <span class="badge bg-warning text-dark">
                                        Sedang
                                    </span>


                                <?php elseif (
                                    $level_risiko === "rendah"
                                ): ?>

                                    <span class="badge bg-success">
                                        Rendah
                                    </span>


                                <?php else: ?>

                                    <span class="badge bg-secondary">
                                        -
                                    </span>

                                <?php endif; ?>


                            </td>


                            <!-- =================================================
                                 STATUS
                            ================================================== -->

                            <td>

                                <?php

                                $status = strtolower(
                                    trim(
                                        $row["status_kesiapan"] ?? ""
                                    )
                                );

                                ?>


                                <?php if (
                                    $status === "siap"
                                ): ?>

                                    <span class="badge bg-success">
                                        Siap
                                    </span>


                                <?php elseif (
                                    $status === "belum siap"
                                ): ?>

                                    <span class="badge bg-warning text-dark">
                                        Belum Siap
                                    </span>


                                <?php elseif (
                                    $status === "proses"
                                ): ?>

                                    <span class="badge bg-info text-dark">
                                        Proses
                                    </span>


                                <?php else: ?>

                                    <span class="badge bg-secondary">

                                        <?= htmlspecialchars(
                                            $row["status_kesiapan"] ?? "-"
                                        ); ?>

                                    </span>

                                <?php endif; ?>


                            </td>


                            <!-- =================================================
                                 AKSI
                            ================================================== -->

                            <td>

                                <div class="d-flex gap-2">


                                    <!-- DETAIL -->

                                    <a
                                        href="detail.php?id=<?= urlencode($row["id_kegiatan"]); ?>"
                                        class="btn btn-sm btn-outline-info"
                                        title="Detail"
                                    >

                                        <i class="bi bi-eye"></i>

                                    </a>


                                    <!-- EDIT -->

                                    <a
                                        href="edit.php?id=<?= urlencode($row["id_kegiatan"]); ?>"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit"
                                    >

                                        <i class="bi bi-pencil"></i>

                                    </a>


                                    <!-- HAPUS -->

                                    <a
                                        href="hapus.php?id=<?= urlencode($row["id_kegiatan"]); ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus kegiatan ini?');"
                                    >

                                        <i class="bi bi-trash"></i>

                                    </a>


                                </div>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="9"
                            class="text-center text-muted py-5"
                        >

                            <i
                                class="bi bi-clipboard-check"
                                style="font-size: 40px;"
                            ></i>

                            <div class="mt-2">

                                Belum ada data kegiatan.

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

include "../../includes/footer.php";

?>