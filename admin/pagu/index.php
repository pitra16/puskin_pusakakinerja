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
// CEK LEVEL ADMIN
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
// DATA PAGU
// =====================================================

$query = mysqli_query(
    $conn,
    "SELECT
        id_pagu,
        nama_pagu,
        anggaran_pagu
     FROM tbl_pagu
     ORDER BY nama_pagu ASC"
);


// =====================================================
// JUDUL
// =====================================================

$title = "Pagu Anggaran";


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
                Pagu Anggaran
            </h2>

            <p class="text-muted mb-0">
                Kelola data pagu dan anggaran PUSKIN.
            </p>

        </div>


        <a
            href="tambah.php"
            class="btn btn-primary"
        >

            <i class="bi bi-plus-lg me-2"></i>

            Tambah Pagu

        </a>

    </div>


    <!-- =================================================
         NOTIFIKASI
    ================================================== -->

    <?php if (isset($_GET["status"])): ?>


        <?php if ($_GET["status"] === "tambah"): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                Data pagu berhasil ditambahkan.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>


        <?php elseif ($_GET["status"] === "edit"): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                Data pagu berhasil diperbarui.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>


        <?php elseif ($_GET["status"] === "hapus"): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                Data pagu berhasil dihapus.

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
                Daftar Pagu Anggaran
            </h5>

            <small class="text-muted">
                Data pagu anggaran yang tersedia.
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
                            Nama Pagu
                        </th>

                        <th>
                            Anggaran
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


                            <!-- NAMA PAGU -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $row["nama_pagu"]
                                    ); ?>

                                </strong>

                            </td>


                            <!-- ANGGARAN -->

                            <td>

                                <strong>

                                    Rp
                                    <?= number_format(
                                        $row["anggaran_pagu"],
                                        0,
                                        ",",
                                        "."
                                    ); ?>

                                </strong>

                            </td>


                            <!-- AKSI -->

                            <td>

                                <div class="d-flex gap-2">


                                    <!-- EDIT -->

                                    <a
                                        href="edit.php?id=<?= urlencode($row["id_pagu"]); ?>"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit"
                                    >

                                        <i class="bi bi-pencil"></i>

                                    </a>


                                    <!-- HAPUS -->

                                    <a
                                        href="hapus.php?id=<?= urlencode($row["id_pagu"]); ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus pagu ini?');"
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
                            colspan="4"
                            class="text-center text-muted py-5"
                        >

                            <i
                                class="bi bi-wallet2"
                                style="font-size: 40px;"
                            ></i>

                            <div class="mt-2">

                                Belum ada data pagu anggaran.

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