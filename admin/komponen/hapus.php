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
// AMBIL ID
// =====================================
$id_komponen = $_GET["id"] ?? "";

if (empty($id_komponen)) {
    header("Location: index.php");
    exit;
}

// =====================================
// CEK APAKAH KOMPONEN SUDAH DIGUNAKAN
// =====================================
$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS jumlah
     FROM tbl_dtkomponen
     WHERE id_komponen = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id_komponen
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// =====================================
// JIKA MASIH DIGUNAKAN
// =====================================
if ((int) $data["jumlah"] > 0) {

    die(
        "Komponen tidak dapat dihapus karena masih digunakan " .
        "pada kegiatan."
    );
}

// =====================================
// HAPUS
// =====================================
$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM tbl_komponen
     WHERE id_komponen = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id_komponen
);

if (!mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    die(
        "Gagal menghapus komponen: " .
        htmlspecialchars(mysqli_error($conn))
    );
}

mysqli_stmt_close($stmt);

// =====================================
// REDIRECT
// =====================================
header("Location: index.php?status=hapus");
exit;

?>