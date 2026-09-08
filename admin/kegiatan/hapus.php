<?php

session_start();

require_once "../../config/database.php";

// =====================================
// CEK LOGIN & LEVEL ADMIN
// =====================================
if (!isset($_SESSION["id_user"]) || $_SESSION["level_user"] !== "admin") {
    header("Location: ../../auth/login.php");
    exit;
}

// =====================================
// CEK ID KEGIATAN
// =====================================
$id_kegiatan = $_GET["id"] ?? "";

if (empty($id_kegiatan)) {
    header("Location: index.php");
    exit;
}

// =====================================
// MULAI TRANSAKSI
// =====================================
mysqli_begin_transaction($conn);

try {

    // =====================================
    // AMBIL DATA KEGIATAN
    // =====================================
    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            id_kegiatan,
            id_pagu,
            nama_kegiatan,
            realisasi_pagu
         FROM tbl_kegiatan
         WHERE id_kegiatan = ?
         FOR UPDATE"
    );

    mysqli_stmt_bind_param($stmt, "s", $id_kegiatan);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $kegiatan = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$kegiatan) {
        throw new Exception("Data kegiatan tidak ditemukan.");
    }

    $id_pagu = $kegiatan["id_pagu"];
    $nama_kegiatan = $kegiatan["nama_kegiatan"];
    $realisasi_pagu = (int) $kegiatan["realisasi_pagu"];

    // =====================================
    // LOCK PAGU
    // =====================================
    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            id_pagu,
            sisa
         FROM tbl_pagu
         WHERE id_pagu = ?
         FOR UPDATE"
    );

    mysqli_stmt_bind_param($stmt, "s", $id_pagu);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $pagu = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$pagu) {
        throw new Exception("Pagu kegiatan tidak ditemukan.");
    }

    // =====================================
    // KEMBALIKAN REALISASI KE PAGU
    // =====================================
    $sisa_baru = (int) $pagu["sisa"] + $realisasi_pagu;

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE tbl_pagu
         SET sisa = ?
         WHERE id_pagu = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "is",
        $sisa_baru,
        $id_pagu
    );

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new Exception("Gagal mengembalikan anggaran pagu.");
    }

    mysqli_stmt_close($stmt);

    // =====================================
    // HAPUS DETAIL KOMPONEN
    // =====================================
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM tbl_dtkomponen
         WHERE id_kegiatan = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $id_kegiatan);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new Exception("Gagal menghapus komponen kegiatan.");
    }

    mysqli_stmt_close($stmt);

    // =====================================
    // HAPUS LOG KEGIATAN
    // =====================================
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM tbl_logkegiatan
         WHERE id_kegiatan = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $id_kegiatan);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new Exception("Gagal menghapus log kegiatan.");
    }

    mysqli_stmt_close($stmt);

    // =====================================
    // HAPUS KEGIATAN
    // =====================================
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM tbl_kegiatan
         WHERE id_kegiatan = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $id_kegiatan);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new Exception("Gagal menghapus kegiatan.");
    }

    mysqli_stmt_close($stmt);

    // =====================================
    // COMMIT
    // =====================================
    mysqli_commit($conn);

    header("Location: index.php?status=hapus");
    exit;

} catch (Exception $e) {

    // =====================================
    // ROLLBACK
    // =====================================
    mysqli_rollback($conn);

    die(
        "Gagal menghapus kegiatan: " .
        htmlspecialchars($e->getMessage())
    );
}
?>