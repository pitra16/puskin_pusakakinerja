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
// DATA PEGAWAI
// =====================================================

$query = mysqli_query(
    $conn,
    "SELECT
        p.id_pegawai,
        p.nama_pegawai,
        p.nip_pegawai,
        p.no_telp,
        u.username_user,
        u.level_user
     FROM tbl_pegawai p
     LEFT JOIN tbl_user u
        ON p.id_pegawai = u.id_pegawai
     ORDER BY p.nama_pegawai ASC"
);


// =====================================================
// JUDUL
// =====================================================

$title = "Data Pegawai";


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
                Data Pegawai
            </h2>

            <p class="text-muted mb-0">
                Kelola data pegawai PUSKIN.
            </p>

        </div>


        <a
            href="tambah.php"
            class="btn btn-primary"
        >

            <i class="bi bi-plus-lg me-2"></i>

            Tambah Pegawai

        </a>

    </div>


    <!-- =================================================
         CARD
    ================================================== -->

    <?php if (isset($_GET["status"])): ?>

    <?php if ($_GET["status"] === "tambah"): ?>

        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            Data pegawai berhasil ditambahkan.
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>

    <?php elseif ($_GET["status"] === "edit"): ?>

        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            Data pegawai berhasil diperbarui.
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>

    <?php elseif ($_GET["status"] === "hapus"): ?>

        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            Data pegawai berhasil dihapus.
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>

    <?php endif; ?>

<?php endif; ?>

    <div class="content-card">


        <!-- HEADER CARD -->

        <div class="mb-4">

            <h5 class="mb-1">
                Daftar Pegawai
            </h5>

            <small class="text-muted">
                Data seluruh pegawai yang terdaftar.
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
                            Nama Pegawai
                        </th>

                        <th>
                            NIP
                        </th>

                        <th>
                            No. Telepon
                        </th>

                        <th width="15%">
                             Akun
                        </th>

                        <th width="15%">
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


                            <!-- NO -->

                            <td>
                                <?= $no++; ?>
                            </td>


                            <!-- NAMA -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $row["nama_pegawai"]
                                    ); ?>

                                </strong>

                            </td>


                            <!-- NIP -->

                            <td>

                                <?= htmlspecialchars(
                                    $row["nip_pegawai"] ?? "-"
                                ); ?>

                            </td>


                            <!-- TELEPON -->

                            <td>

                                <?= htmlspecialchars(
                                    $row["no_telp"] ?? "-"
                                ); ?>

                            </td>


                            <!-- AKSI -->

                            <td>

                                <div class="d-flex gap-2">


                                    <!-- EDIT -->

                                    <a
                                        href="edit.php?id=<?= urlencode($row["id_pegawai"]); ?>"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit"
                                    >

                                        <i class="bi bi-pencil"></i>

                                    </a>


                                    <!-- HAPUS -->

                                    <a
                                        href="hapus.php?id=<?= urlencode($row["id_pegawai"]); ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus pegawai ini?');"
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
                            colspan="5"
                            class="text-center text-muted py-5"
                        >

                            <i
                                class="bi bi-people"
                                style="font-size: 40px;"
                            ></i>

                            <div class="mt-2">

                                Belum ada data pegawai.

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