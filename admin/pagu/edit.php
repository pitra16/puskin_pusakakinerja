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
// CEK ID PAGU
// =====================================================

if (!isset($_GET["id"]) || empty($_GET["id"])) {

    header("Location: index.php");

    exit;
}

$id_pagu = $_GET["id"];


// =====================================================
// AMBIL DATA PAGU
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id_pagu,
        nama_pagu,
        anggaran_pagu,
        sisa
     FROM tbl_pagu
     WHERE id_pagu = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id_pagu
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$pagu = mysqli_fetch_assoc($result);


// =====================================================
// CEK DATA
// =====================================================

if (!$pagu) {

    header("Location: index.php");

    exit;
}


// =====================================================
// JUDUL
// =====================================================

$title = "Edit Pagu Anggaran";


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
                Edit Pagu Anggaran
            </h2>

            <p class="text-muted mb-0">
                Perbarui data pagu anggaran.
            </p>

        </div>


        <a
            href="index.php"
            class="btn btn-secondary"
        >

            <i class="bi bi-arrow-left me-2"></i>

            Kembali

        </a>

    </div>


    <!-- =================================================
         CARD
    ================================================== -->

    <div class="content-card">


        <div class="mb-4">

            <h5 class="mb-1">
                Form Edit Pagu
            </h5>

            <small class="text-muted">
                Perbarui nama dan jumlah anggaran pagu.
            </small>

        </div>


        <!-- =================================================
             INFORMASI SISA
        ================================================== -->

        <div class="alert alert-info">

            <i class="bi bi-info-circle me-2"></i>

            <strong>Sisa anggaran saat ini:</strong>

            Rp
            <?= number_format(
                $pagu["sisa"],
                0,
                ",",
                "."
            ); ?>

            <br>

            <small>
                Sisa anggaran akan dikelola otomatis berdasarkan
                realisasi kegiatan.
            </small>

        </div>


        <!-- =================================================
             FORM
        ================================================== -->

        <form
            action="proses_edit.php"
            method="POST"
        >


            <!-- ID PAGU -->

            <input
                type="hidden"
                name="id_pagu"
                value="<?= htmlspecialchars($pagu["id_pagu"]); ?>"
            >


            <!-- =================================================
                 NAMA PAGU
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">

                    Nama Pagu

                    <span class="text-danger">
                        *
                    </span>

                </label>

                <input
                    type="text"
                    name="nama_pagu"
                    class="form-control"
                    value="<?= htmlspecialchars($pagu["nama_pagu"]); ?>"
                    maxlength="64"
                    required
                >

            </div>


            <!-- =================================================
                 ANGGARAN
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">

                    Anggaran

                    <span class="text-danger">
                        *
                    </span>

                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        Rp
                    </span>

                    <input
                        type="number"
                        name="anggaran_pagu"
                        class="form-control"
                        value="<?= (int) $pagu["anggaran_pagu"]; ?>"
                        min="0"
                        step="1"
                        required
                    >

                </div>

                <small class="text-muted">

                    Masukkan jumlah anggaran tanpa titik atau koma.

                </small>

            </div>


            <!-- =================================================
                 TOMBOL
            ================================================== -->

            <div class="d-flex justify-content-end gap-2 mt-4">


                <a
                    href="index.php"
                    class="btn btn-secondary"
                >

                    Batal

                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="bi bi-save me-2"></i>

                    Simpan Perubahan

                </button>


            </div>


        </form>


    </div>


</main>


<?php

include "../../includes/footer.php";

?>