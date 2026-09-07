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
// AMBIL DATA FORM
// =====================================================

$id_pegawai = trim($_POST["id_pegawai"] ?? "");
$username   = trim($_POST["username"] ?? "");
$password   = $_POST["password"] ?? "";
$password_confirm = $_POST["password_confirm"] ?? "";
$level      = trim($_POST["level"] ?? "pegawai");


// =====================================================
// VALIDASI DATA
// =====================================================

if (
    empty($id_pegawai) ||
    empty($username) ||
    empty($password) ||
    empty($password_confirm)
) {

    die("Semua data wajib diisi.");
}


// =====================================================
// VALIDASI PASSWORD
// =====================================================

if (strlen($password) < 6) {

    die("Password minimal 6 karakter.");
}


if ($password !== $password_confirm) {

    die("Konfirmasi password tidak sama.");
}


// =====================================================
// VALIDASI LEVEL
// =====================================================

if (
    $level !== "admin" &&
    $level !== "pegawai"
) {

    die("Level akun tidak valid.");
}


// =====================================================
// CEK PEGAWAI
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT id_pegawai
     FROM tbl_pegawai
     WHERE id_pegawai = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id_pegawai
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {

    die("Data pegawai tidak ditemukan.");
}


// =====================================================
// CEK APAKAH PEGAWAI SUDAH MEMILIKI AKUN
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT id_user
     FROM tbl_user
     WHERE id_pegawai = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id_pegawai
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {

    die("Pegawai ini sudah memiliki akun.");
}


// =====================================================
// CEK USERNAME
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT id_user
     FROM tbl_user
     WHERE username_user = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $username
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {

    die("Username sudah digunakan. Silakan gunakan username lain.");
}


// =====================================================
// HASH PASSWORD
// =====================================================

$password_hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// =====================================================
// GENERATE ID USER
// =====================================================

$id_user = bin2hex(random_bytes(16));


// =====================================================
// SIMPAN AKUN
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO tbl_user
    (
        id_user,
        id_pegawai,
        username_user,
        password_user,
        level_user
    )
    VALUES (?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "sssss",
    $id_user,
    $id_pegawai,
    $username,
    $password_hash,
    $level
);


// =====================================================
// EKSEKUSI
// =====================================================

if (mysqli_stmt_execute($stmt)) {

    header("Location: index.php?status=akun");

    exit;

} else {

    die(
        "Gagal membuat akun: " .
        mysqli_error($conn)
    );
}

?>