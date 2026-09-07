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
// AMBIL ID PEGAWAI
// =====================================================

$id_pegawai = trim($_POST["id_pegawai"] ?? "");


if (empty($id_pegawai)) {

    die("ID pegawai tidak ditemukan.");
}


// =====================================================
// AMBIL NIP PEGAWAI
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        p.nip_pegawai,
        u.id_user
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

$data = mysqli_fetch_assoc($result);


// =====================================================
// CEK DATA
// =====================================================

if (!$data) {

    die("Data pegawai atau akun tidak ditemukan.");
}


// =====================================================
// CEK NIP
// =====================================================

$nip = trim($data["nip_pegawai"] ?? "");

if (empty($nip)) {

    die("Pegawai belum memiliki NIP. Password tidak dapat direset.");
}


// =====================================================
// HASH PASSWORD BARU
// =====================================================

$password_baru = password_hash(
    $nip,
    PASSWORD_DEFAULT
);


// =====================================================
// UPDATE PASSWORD
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "UPDATE tbl_user
     SET password_user = ?
     WHERE id_user = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ss",
    $password_baru,
    $data["id_user"]
);


// =====================================================
// EKSEKUSI
// =====================================================

if (mysqli_stmt_execute($stmt)) {

    header("Location: index.php?status=reset");

    exit;

} else {

    die(
        "Gagal mereset password: " .
        mysqli_error($conn)
    );
}

?>