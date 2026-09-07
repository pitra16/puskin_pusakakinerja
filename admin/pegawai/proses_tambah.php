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


// =====================================================
// DATABASE
// =====================================================

require_once "../../config/database.php";


// =====================================================
// AMBIL DATA FORM
// =====================================================

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

if ($nama_pegawai === "") {

    die("Nama pegawai wajib diisi.");

}


// =====================================================
// BUAT ID
// =====================================================

$id_pegawai = bin2hex(
    random_bytes(16)
);


// =====================================================
// INSERT
// =====================================================

$query = "
    INSERT INTO tbl_pegawai
    (
        id_pegawai,
        nama_pegawai,
        nip_pegawai,
        no_telp
    )
    VALUES
    (?, ?, ?, ?)
";


$stmt = mysqli_prepare(
    $conn,
    $query
);


mysqli_stmt_bind_param(
    $stmt,
    "ssss",
    $id_pegawai,
    $nama_pegawai,
    $nip_pegawai,
    $no_telp
);


// =====================================================
// EKSEKUSI
// =====================================================

if (mysqli_stmt_execute($stmt)) {

    header(
        "Location: index.php?status=tambah"
    );

    exit;

} else {

    die(
        "Gagal menyimpan data: " .
        mysqli_error($conn)
    );

}