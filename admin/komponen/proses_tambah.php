<?php

session_start();

require_once "../../config/database.php";

// =====================================
// CEK LOGIN & ADMIN
// =====================================
if (!isset($_SESSION["id_user"]) || $_SESSION["level_user"] !== "admin") {
    header("Location: ../../auth/login.php");
    exit;
}

// =====================================
// CEK METHOD
// =====================================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// =====================================
// AMBIL DATA
// =====================================
$id_komponen = trim($_POST["id_komponen"] ?? "");
$kesiapan_persen = $_POST["kesiapan_persen"] ?? 0;

// =====================================
// VALIDASI
// =====================================
if (empty($id_komponen)) {
    die("ID komponen wajib diisi.");
}

if (!is_numeric($kesiapan_persen)) {
    die("Kesiapan persen harus berupa angka.");
}

$kesiapan_persen = (int) $kesiapan_persen;

if ($kesiapan_persen < 0 || $kesiapan_persen > 100) {
    die("Kesiapan persen harus antara 0 sampai 100.");
}

// =====================================
// CEK ID SUDAH ADA
// =====================================
$stmt = mysqli_prepare(
    $conn,
    "SELECT id_komponen
     FROM tbl_komponen
     WHERE id_komponen = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id_komponen
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {

    mysqli_stmt_close($stmt);

    die("ID komponen sudah digunakan.");
}

mysqli_stmt_close($stmt);

// =====================================
// INSERT
// =====================================
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO tbl_komponen
        (id_komponen, kesiapan_persen)
     VALUES
        (?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $id_komponen,
    $kesiapan_persen
);

if (!mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    die(
        "Gagal menambahkan komponen: " .
        htmlspecialchars(mysqli_error($conn))
    );
}

mysqli_stmt_close($stmt);

// =====================================
// REDIRECT
// =====================================
header("Location: index.php?status=tambah");
exit;

?>