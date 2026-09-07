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
// AMBIL DATA PEGAWAI
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
     LEFT JOIN tbl_user u
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
// CEK DATA PEGAWAI
// =====================================================

if (!$pegawai) {

    header("Location: index.php");

    exit;
}


// =====================================================
// CEK APAKAH SUDAH MEMILIKI AKUN
// =====================================================

if (!empty($pegawai["id_user"])) {

    header("Location: index.php");

    exit;
}


// =====================================================
// JUDUL
// =====================================================

$title = "Buat Akun Pegawai";


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
                Buat Akun Pegawai
            </h2>

            <p class="text-muted mb-0">
                Buat akun login untuk pegawai.
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


        <!-- =================================================
             INFORMASI PEGAWAI
        ================================================== -->

        <div class="mb-4">

            <h5 class="mb-3">
                Informasi Pegawai
            </h5>


            <div class="row">


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


                <!-- TELEPON -->

                <div class="col-md-4 mb-3">

                    <label class="form-label text-muted">
                        No. Telepon
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($pegawai["no_telp"] ?? "-"); ?>"
                        readonly
                    >

                </div>


            </div>

        </div>


        <hr>


        <!-- =================================================
             FORM AKUN
        ================================================== -->

        <div class="mt-4">

            <h5 class="mb-3">
                Data Akun Login
            </h5>


            <form
                action="proses_buat_akun.php"
                method="POST"
            >


                <!-- ID PEGAWAI -->

                <input
                    type="hidden"
                    name="id_pegawai"
                    value="<?= htmlspecialchars($pegawai["id_pegawai"]); ?>"
                >


                <div class="row">


                    <!-- USERNAME -->

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Username

                            <span class="text-danger">
                                *
                            </span>

                        </label>

                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            placeholder="Masukkan username"
                            required
                            autocomplete="off"
                        >

                        <small class="text-muted">

                            Username digunakan untuk login.

                        </small>

                    </div>


                    <!-- LEVEL -->

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Level Akun

                            <span class="text-danger">
                                *
                            </span>

                        </label>

                        <select
                            name="level"
                            class="form-select"
                            required
                        >

                            <option value="pegawai">
                                Pegawai
                            </option>

                            <option value="admin">
                                Admin
                            </option>

                        </select>

                    </div>


                </div>


                <div class="row">


                    <!-- PASSWORD -->

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Password

                            <span class="text-danger">
                                *
                            </span>

                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Masukkan password"
                            required
                        >

                        <small class="text-muted">

                            Minimal 6 karakter.

                        </small>

                    </div>


                    <!-- KONFIRMASI PASSWORD -->

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Konfirmasi Password

                            <span class="text-danger">
                                *
                            </span>

                        </label>

                        <input
                            type="password"
                            name="password_confirm"
                            class="form-control"
                            placeholder="Ulangi password"
                            required
                        >

                    </div>


                </div>


                <!-- =================================================
                     TOMBOL
                ================================================== -->

                <div class="d-flex justify-content-end gap-2 mt-3">


                    <a
                        href="index.php"
                        class="btn btn-secondary"
                    >

                        Batal

                    </a>


                    <button
                        type="submit"
                        class="btn btn-success"
                    >

                        <i class="bi bi-person-plus me-2"></i>

                        Buat Akun

                    </button>


                </div>


            </form>

        </div>


    </div>


</main>


<?php

include "../../includes/footer.php";

?>