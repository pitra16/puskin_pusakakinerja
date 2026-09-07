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
// CEK ADMIN
// =====================================================

if (
    !isset($_SESSION["level"]) ||
    strtolower($_SESSION["level"]) !== "admin"
) {

    header("Location: ../../index.php");

    exit;
}


require_once "../../config/database.php";


// =====================================================
// ID PEGAWAI
// =====================================================

$id_pegawai = $_GET["id"] ?? "";


if ($id_pegawai === "") {

    header("Location: index.php");

    exit;
}


// =====================================================
// AMBIL DATA
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id_pegawai,
        nama_pegawai,
        nip_pegawai,
        no_telp
     FROM tbl_pegawai
     WHERE id_pegawai = ?
     LIMIT 1"
);


mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id_pegawai
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$pegawai = mysqli_fetch_assoc($result);


if (!$pegawai) {

    die("Data pegawai tidak ditemukan.");

}


$title = "Edit Pegawai";


include "../../includes/header.php";

include "../../includes/sidebar.php";

include "../../includes/navbar.php";

?>


<main class="main-content">


    <!-- HEADER -->

    <div class="mb-4">

        <h2 class="page-title mb-1">
            Edit Pegawai
        </h2>

        <p class="text-muted mb-0">
            Perbarui data pegawai.
        </p>

    </div>


    <!-- FORM -->

    <div class="content-card">

        <form
            action="proses_edit.php"
            method="POST"
        >


            <input
                type="hidden"
                name="id_pegawai"
                value="<?= htmlspecialchars(
                    $pegawai["id_pegawai"]
                ); ?>"
            >


            <!-- NAMA -->

            <div class="mb-3">

                <label class="form-label">
                    Nama Pegawai
                </label>

                <input
                    type="text"
                    name="nama_pegawai"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $pegawai["nama_pegawai"]
                    ); ?>"
                    maxlength="64"
                    required
                >

            </div>


            <!-- NIP -->

            <div class="mb-3">

                <label class="form-label">
                    NIP
                </label>

                <input
                    type="text"
                    name="nip_pegawai"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $pegawai["nip_pegawai"] ?? ""
                    ); ?>"
                    maxlength="64"
                >

            </div>


            <!-- TELEPON -->

            <div class="mb-4">

                <label class="form-label">
                    Nomor Telepon
                </label>

                <input
                    type="text"
                    name="no_telp"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $pegawai["no_telp"] ?? ""
                    ); ?>"
                    maxlength="16"
                >

            </div>


            <!-- BUTTON -->

            <div class="d-flex gap-2">

                <a
                    href="index.php"
                    class="btn btn-secondary"
                >

                    <i class="bi bi-arrow-left me-2"></i>

                    Kembali

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