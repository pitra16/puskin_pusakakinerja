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

$nama_pagu = trim($_POST["nama_pagu"] ?? "");

$anggaran_pagu = $_POST["anggaran_pagu"] ?? "";


// =====================================================
// VALIDASI NAMA
// =====================================================

if (empty($nama_pagu)) {

    die("Nama pagu wajib diisi.");
}


// =====================================================
// VALIDASI ANGGARAN
// =====================================================

if ($anggaran_pagu === "" || !is_numeric($anggaran_pagu)) {

    die("Anggaran harus berupa angka.");
}


$anggaran_pagu = (int) $anggaran_pagu;


// =====================================================
// VALIDASI NILAI
// =====================================================

if ($anggaran_pagu < 0) {

    die("Anggaran tidak boleh kurang dari 0.");
}


// =====================================================
// GENERATE ID PAGU
// =====================================================

$id_pagu = bin2hex(random_bytes(16));


// =====================================================
// SISA AWAL = ANGGARAN
// =====================================================

$sisa = $anggaran_pagu;


// =====================================================
// SIMPAN DATA
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO tbl_pagu
    (
        id_pagu,
        nama_pagu,
        anggaran_pagu,
        sisa
    )
    VALUES (?, ?, ?, ?)"
);


// =====================================================
// BIND PARAMETER
// =====================================================

mysqli_stmt_bind_param(
    $stmt,
    "ssii",
    $id_pagu,
    $nama_pagu,
    $anggaran_pagu,
    $sisa
);


// =====================================================
// EKSEKUSI
// =====================================================

if (mysqli_stmt_execute($stmt)) {

    header("Location: index.php?status=tambah");

    exit;

} else {

    die(
        "Gagal menambahkan pagu: " .
        mysqli_error($conn)
    );
}

?>