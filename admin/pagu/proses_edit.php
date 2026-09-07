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
// CEK METHOD
// =====================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");

    exit;
}


// =====================================================
// AMBIL DATA
// =====================================================

$id_pagu = trim($_POST["id_pagu"] ?? "");

$nama_pagu = trim($_POST["nama_pagu"] ?? "");

$anggaran_baru = $_POST["anggaran_pagu"] ?? "";


// =====================================================
// VALIDASI
// =====================================================

if (
    empty($id_pagu) ||
    empty($nama_pagu)
) {

    die("Data pagu belum lengkap.");
}


// =====================================================
// VALIDASI ANGGARAN
// =====================================================

if (
    $anggaran_baru === "" ||
    !is_numeric($anggaran_baru)
) {

    die("Anggaran harus berupa angka.");
}


$anggaran_baru = (int) $anggaran_baru;


// =====================================================
// VALIDASI NILAI
// =====================================================

if ($anggaran_baru < 0) {

    die("Anggaran tidak boleh kurang dari 0.");
}


// =====================================================
// AMBIL DATA LAMA
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
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

$pagu_lama = mysqli_fetch_assoc($result);


// =====================================================
// CEK PAGU
// =====================================================

if (!$pagu_lama) {

    die("Data pagu tidak ditemukan.");
}


// =====================================================
// HITUNG SISA BARU
// =====================================================
//
// Selisih anggaran tetap mempertahankan jumlah
// anggaran yang sudah digunakan.
//
// Contoh:
//
// Anggaran lama = 100 juta
// Sisa lama     = 70 juta
//
// Anggaran baru = 120 juta
//
// Sisa baru = 70 + (120 - 100)
//            = 90 juta
//
// =====================================================

$anggaran_lama = (int) $pagu_lama["anggaran_pagu"];

$sisa_lama = (int) $pagu_lama["sisa"];

$selisih = $anggaran_baru - $anggaran_lama;

$sisa_baru = $sisa_lama + $selisih;


// =====================================================
// VALIDASI SISA
// =====================================================

if ($sisa_baru < 0) {

    die(
        "Anggaran baru tidak dapat digunakan karena "
        . "lebih kecil dari jumlah anggaran yang sudah direalisasikan."
    );
}


// =====================================================
// UPDATE PAGU
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "UPDATE tbl_pagu
     SET
        nama_pagu = ?,
        anggaran_pagu = ?,
        sisa = ?
     WHERE id_pagu = ?"
);


mysqli_stmt_bind_param(
    $stmt,
    "siis",
    $nama_pagu,
    $anggaran_baru,
    $sisa_baru,
    $id_pagu
);


// =====================================================
// EKSEKUSI
// =====================================================

if (mysqli_stmt_execute($stmt)) {

    header("Location: index.php?status=edit");

    exit;

} else {

    die(
        "Gagal memperbarui pagu: "
        . mysqli_error($conn)
    );
}

?>