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
// CEK ID PEGAWAI
// =====================================================

if (!isset($_GET["id"]) || empty($_GET["id"])) {

    header("Location: index.php");

    exit;
}

$id_pegawai = $_GET["id"];


// =====================================================
// AMBIL DATA PEGAWAI + AKUN
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        p.id_pegawai,
        p.nama_pegawai,
        p.nip_pegawai,
        p.no_telp,
        u.id_user,
        u.username_user
     FROM tbl_pegawai p
     INNER JOIN tbl_user u
        ON p.id_pegawai = u.id_pegawai
     WHERE p.id_pegawai = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id_pegawai
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$pegawai = mysqli_fetch_assoc($result);


// =====================================================
// CEK DATA
// =====================================================

if (!$pegawai) {

    header("Location: index.php");

    exit;
}


// =====================================================
// JUDUL
// =====================================================

$title = "Reset Password";


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
                Reset Password
            </h2>

            <p class="text-muted mb-0">
                Reset password akun pegawai.
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


        <div class="alert alert-warning">

            <i class="bi bi-exclamation-triangle me-2"></i>

            <strong>Perhatian!</strong>

            Password akun pegawai akan diubah menjadi
            <strong>NIP pegawai</strong>.

        </div>


        <!-- =================================================
             INFORMASI PEGAWAI
        ================================================== -->

        <h5 class="mb-4">
            Informasi Akun
        </h5>


        <div class="row mb-4">


            <!-- NAMA -->

            <div class="col-md-4 mb-3">

                <label class="form-label text-muted">
                    Nama Pegawai
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?= htmlspecialchars($pegawai["nama_pegawai"]); ?>"
                    readonly
                >

            </div>


            <!-- NIP -->

            <div class="col-md-4 mb-3">

                <label class="form-label text-muted">
                    NIP
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?= htmlspecialchars($pegawai["nip_pegawai"] ?? "-"); ?>"
                    readonly
                >

            </div>


            <!-- USERNAME -->

            <div class="col-md-4 mb-3">

                <label class="form-label text-muted">
                    Username
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?= htmlspecialchars($pegawai["username_user"]); ?>"
                    readonly
                >

            </div>


        </div>


        <hr>


        <!-- =================================================
             KONFIRMASI RESET
        ================================================== -->

        <div class="mt-4">

            <h5 class="mb-3">
                Konfirmasi Reset Password
            </h5>


            <p class="text-muted">

                Setelah reset, pegawai dapat login menggunakan:

            </p>


            <div class="bg-light rounded p-3 mb-4">

                <div class="mb-2">

                    <strong>
                        Username:
                    </strong>

                    <?= htmlspecialchars($pegawai["username_user"]); ?>

                </div>


                <div>

                    <strong>
                        Password:
                    </strong>

                    <?= htmlspecialchars($pegawai["nip_pegawai"] ?? "-"); ?>

                </div>

            </div>


            <?php if (empty($pegawai["nip_pegawai"])): ?>

                <div class="alert alert-danger">

                    <i class="bi bi-x-circle me-2"></i>

                    Pegawai ini belum memiliki NIP.
                    Password tidak dapat direset.

                </div>

            <?php else: ?>


                <form
                    action="proses_reset_password.php"
                    method="POST"
                    onsubmit="return confirm('Yakin ingin mereset password pegawai ini menjadi NIP?');"
                >


                    <input
                        type="hidden"
                        name="id_pegawai"
                        value="<?= htmlspecialchars($pegawai["id_pegawai"]); ?>"
                    >


                    <div class="d-flex justify-content-end gap-2">


                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >

                            Batal

                        </a>


                        <button
                            type="submit"
                            class="btn btn-warning"
                        >

                            <i class="bi bi-key me-2"></i>

                            Reset Password

                        </button>


                    </div>


                </form>


            <?php endif; ?>


        </div>


    </div>


</main>


<?php

include "../../includes/footer.php";

?>