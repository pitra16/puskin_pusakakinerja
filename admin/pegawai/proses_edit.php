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
// DATA FORM
// =====================================================

$id_pegawai = $_POST["id_pegawai"] ?? "";

$nama_pegawai = trim(
    $_POST["nama_pegawai"] ?? ""
);

$nip_pegawai = trim(
    $_POST["nip_pegawai"] ?? ""
);

$no_telp = trim(
    $_POST["no_telp"] ?? ""
);


// =====================================================
// VALIDASI
// =====================================================

if (
    $id_pegawai === "" ||
    $nama_pegawai === ""
) {

    die("Data tidak lengkap.");

}


// =====================================================
// UPDATE
// =====================================================

$query = "
    UPDATE tbl_pegawai
    SET
        nama_pegawai = ?,
        nip_pegawai = ?,
        no_telp = ?
    WHERE id_pegawai = ?
";


$stmt = mysqli_prepare(
    $conn,
    $query
);


mysqli_stmt_bind_param(
    $stmt,
    "ssss",
    $nama_pegawai,
    $nip_pegawai,
    $no_telp,
    $id_pegawai
);


// =====================================================
// EKSEKUSI
// =====================================================

if (mysqli_stmt_execute($stmt)) {

    header(
        "Location: index.php?status=edit"
    );

    exit;

} else {

    die(
        "Gagal mengubah data: " .
        mysqli_error($conn)
    );

}