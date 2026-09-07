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
// ID
// =====================================================

$id_pegawai = $_GET["id"] ?? "";


if ($id_pegawai === "") {

    header("Location: index.php");

    exit;
}


// =====================================================
// DELETE
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM tbl_pegawai
     WHERE id_pegawai = ?"
);


mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id_pegawai
);


if (mysqli_stmt_execute($stmt)) {

    header(
        "Location: index.php?status=hapus"
    );

    exit;

} else {

    die(
        "Gagal menghapus data. " .
        "Kemungkinan pegawai masih digunakan oleh tabel lain.<br><br>" .
        "Detail: " .
        mysqli_error($conn)
    );

}