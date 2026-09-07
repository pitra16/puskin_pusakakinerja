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
// CEK ID
// =====================================================

if (!isset($_GET["id"]) || empty($_GET["id"])) {

    header("Location: index.php");

    exit;
}


$id_pagu = $_GET["id"];


// =====================================================
// HAPUS DATA
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM tbl_pagu
     WHERE id_pagu = ?"
);


mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id_pagu
);


// =====================================================
// EKSEKUSI
// =====================================================

if (mysqli_stmt_execute($stmt)) {

    header("Location: index.php?status=hapus");

    exit;

} else {

    die(
        "Gagal menghapus pagu. "
        . "Kemungkinan pagu sudah digunakan oleh data kegiatan."
    );
}

?>