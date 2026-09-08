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

$id_pagu = trim($_POST["id_pagu"] ?? "");

$nama_kegiatan = trim($_POST["nama_kegiatan"] ?? "");

$tgl_mulai = trim($_POST["tgl_mulai"] ?? "");

$tgl_target = trim($_POST["tgl_target"] ?? "");

$target_kegiatan = trim($_POST["target_kegiatan"] ?? "");

$realisasi_pagu = $_POST["realisasi_pagu"] ?? 0;

$realisasi_persen = $_POST["realisasi_persen"] ?? 0;

$risiko = trim($_POST["risiko"] ?? "");

$level_risiko = trim($_POST["level_risiko"] ?? "");

$mitigasi = trim($_POST["mitigasi"] ?? "");

$status_kesiapan = trim($_POST["status_kesiapan"] ?? "Belum Siap");

$catatan = trim($_POST["catatan"] ?? "");


// =====================================================
// VALIDASI DATA WAJIB
// =====================================================

if (
    empty($id_pegawai) ||
    empty($id_pagu) ||
    empty($nama_kegiatan) ||
    empty($tgl_mulai) ||
    empty($tgl_target) ||
    empty($target_kegiatan)
) {

    die("Data kegiatan wajib diisi dengan lengkap.");
}


// =====================================================
// VALIDASI TANGGAL
// =====================================================

if ($tgl_target < $tgl_mulai) {

    die("Tanggal target tidak boleh lebih awal dari tanggal mulai.");
}


// =====================================================
// VALIDASI REALISASI
// =====================================================

if (
    $realisasi_pagu === "" ||
    !is_numeric($realisasi_pagu)
) {

    die("Realisasi anggaran harus berupa angka.");
}


$realisasi_pagu = (int) $realisasi_pagu;


// =====================================================
// VALIDASI PERSENTASE
// =====================================================

if (
    $realisasi_persen === "" ||
    !is_numeric($realisasi_persen)
) {

    die("Persentase realisasi harus berupa angka.");
}


$realisasi_persen = (int) $realisasi_persen;


if ($realisasi_persen < 0 || $realisasi_persen > 100) {

    die("Persentase realisasi harus antara 0 sampai 100.");
}


// =====================================================
// VALIDASI REALISASI TIDAK NEGATIF
// =====================================================

if ($realisasi_pagu < 0) {

    die("Realisasi anggaran tidak boleh kurang dari 0.");
}


// =====================================================
// VALIDASI LEVEL RISIKO
// =====================================================

if (
    $level_risiko !== "" &&
    !in_array(
        strtolower($level_risiko),
        ["rendah", "sedang", "tinggi"]
    )
) {

    die("Level risiko tidak valid.");
}


// =====================================================
// VALIDASI STATUS KESIAPAN
// =====================================================

if (
    !in_array(
        strtolower($status_kesiapan),
        ["belum siap", "proses", "siap"]
    )
) {

    die("Status kesiapan tidak valid.");
}


// =====================================================
// MULAI TRANSAKSI DATABASE
// =====================================================

mysqli_begin_transaction($conn);


try {


    // =================================================
    // CEK PEGAWAI
    // =================================================

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

        throw new Exception(
            "Pegawai yang dipilih tidak ditemukan."
        );
    }


    // =================================================
    // AMBIL PAGU
    // =================================================

    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            id_pagu,
            anggaran_pagu,
            sisa
         FROM tbl_pagu
         WHERE id_pagu = ?
         FOR UPDATE"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $id_pagu
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $pagu = mysqli_fetch_assoc($result);


    // =================================================
    // CEK PAGU
    // =================================================

    if (!$pagu) {

        throw new Exception(
            "Pagu anggaran yang dipilih tidak ditemukan."
        );
    }


    // =================================================
    // AMBIL SISA PAGU
    // =================================================

    $sisa_pagu = (int) $pagu["sisa"];


    // =================================================
    // CEK REALISASI
    // =================================================

    if ($realisasi_pagu > $sisa_pagu) {

        throw new Exception(
            "Realisasi Rp " .
            number_format(
                $realisasi_pagu,
                0,
                ",",
                "."
            ) .
            " melebihi sisa pagu Rp " .
            number_format(
                $sisa_pagu,
                0,
                ",",
                "."
            ) .
            "."
        );
    }


    // =================================================
    // HITUNG SISA PAGU BARU
    // =================================================

    $sisa_baru = $sisa_pagu - $realisasi_pagu;


    // =================================================
    // GENERATE ID KEGIATAN
    // =================================================

    $id_kegiatan = bin2hex(
        random_bytes(16)
    );


    // =================================================
    // SIMPAN KEGIATAN
    // =================================================

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO tbl_kegiatan
        (
            id_kegiatan,
            id_pegawai,
            id_pagu,
            nama_kegiatan,
            tgl_mulai,
            tgl_target,
            target_kegiatan,
            realisasi_pagu,
            realisasi_persen,
            risiko,
            level_risiko,
            mitigasi,
            status_kesiapan,
            catatan
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )"
    );


    mysqli_stmt_bind_param(
        $stmt,
        "sssssssiiissss",
        $id_kegiatan,
        $id_pegawai,
        $id_pagu,
        $nama_kegiatan,
        $tgl_mulai,
        $tgl_target,
        $target_kegiatan,
        $realisasi_pagu,
        $realisasi_persen,
        $risiko,
        $level_risiko,
        $mitigasi,
        $status_kesiapan,
        $catatan
    );


    // =================================================
    // EKSEKUSI INSERT
    // =================================================

    if (!mysqli_stmt_execute($stmt)) {

        throw new Exception(
            "Gagal menyimpan kegiatan."
        );
    }


    // =================================================
    // UPDATE SISA PAGU
    // =================================================

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

        throw new Exception(
            "Gagal memperbarui sisa pagu."
        );
    }


    // =================================================
    // SIMPAN LOG KEGIATAN
    // =================================================

    $log_time = date("Y-m-d H:i:s");

    $data_log =
        "Kegiatan ditambahkan oleh admin. "
        . "Realisasi anggaran: Rp "
        . number_format(
            $realisasi_pagu,
            0,
            ",",
            "."
        );


    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO tbl_logkegiatan
        (
            id_kegiatan,
            log_time,
            data_log
        )
        VALUES (?, ?, ?)"
    );


    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $id_kegiatan,
        $log_time,
        $data_log
    );


    if (!mysqli_stmt_execute($stmt)) {

        throw new Exception(
            "Gagal menyimpan log kegiatan."
        );
    }


    // =================================================
    // COMMIT
    // =================================================

    mysqli_commit($conn);


    // =================================================
    // REDIRECT
    // =================================================

    header(
        "Location: index.php?status=tambah"
    );

    exit;


} catch (Exception $e) {


    // =================================================
    // ROLLBACK
    // =================================================

    mysqli_rollback($conn);


    // =================================================
    // TAMPILKAN ERROR
    // =================================================

    die(
        "Gagal menambahkan kegiatan: "
        . htmlspecialchars($e->getMessage())
    );
}

?>