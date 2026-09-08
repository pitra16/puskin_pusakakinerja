<?php

session_start();

require_once "../../config/database.php";

// ===============================
// CEK LOGIN & LEVEL ADMIN
// ===============================
if (!isset($_SESSION["id_user"]) || $_SESSION["level_user"] !== "admin") {
    header("Location: ../../auth/login.php");
    exit;
}

// ===============================
// CEK REQUEST
// ===============================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// ===============================
// AMBIL DATA FORM
// ===============================
$id_kegiatan       = $_POST["id_kegiatan"] ?? "";
$id_pegawai        = $_POST["id_pegawai"] ?? "";
$id_pagu           = $_POST["id_pagu"] ?? "";
$nama_kegiatan     = trim($_POST["nama_kegiatan"] ?? "");
$tgl_mulai         = $_POST["tgl_mulai"] ?? "";
$tgl_target        = $_POST["tgl_target"] ?? "";
$target_kegiatan   = trim($_POST["target_kegiatan"] ?? "");
$realisasi_pagu    = $_POST["realisasi_pagu"] ?? 0;
$realisasi_persen  = $_POST["realisasi_persen"] ?? 0;
$risiko            = trim($_POST["risiko"] ?? "");
$level_risiko      = $_POST["level_risiko"] ?? "";
$mitigasi          = trim($_POST["mitigasi"] ?? "");
$status_kesiapan   = $_POST["status_kesiapan"] ?? "";
$catatan           = trim($_POST["catatan"] ?? "");

// ===============================
// VALIDASI DASAR
// ===============================
if (
    empty($id_kegiatan) ||
    empty($id_pegawai) ||
    empty($id_pagu) ||
    empty($nama_kegiatan) ||
    empty($tgl_mulai) ||
    empty($tgl_target)
) {
    die("Data kegiatan belum lengkap.");
}

if ($tgl_target < $tgl_mulai) {
    die("Tanggal target tidak boleh lebih awal dari tanggal mulai.");
}

if (!is_numeric($realisasi_pagu) || $realisasi_pagu < 0) {
    die("Realisasi anggaran tidak valid.");
}

if (!is_numeric($realisasi_persen) || $realisasi_persen < 0 || $realisasi_persen > 100) {
    die("Persentase realisasi harus antara 0 sampai 100.");
}

$realisasi_pagu   = (int) $realisasi_pagu;
$realisasi_persen = (int) $realisasi_persen;

// Validasi level risiko
$level_risiko_valid = ["rendah", "sedang", "tinggi"];

if (!in_array($level_risiko, $level_risiko_valid)) {
    die("Level risiko tidak valid.");
}

// Validasi status kesiapan
$status_valid = ["Belum Siap", "Proses", "Siap"];

if (!in_array($status_kesiapan, $status_valid)) {
    die("Status kesiapan tidak valid.");
}

// ===============================
// MULAI TRANSAKSI
// ===============================
mysqli_begin_transaction($conn);

try {

    // ===============================
    // AMBIL DATA KEGIATAN LAMA
    // DAN LOCK BARIS
    // ===============================
    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            id_kegiatan,
            id_pagu,
            realisasi_pagu
         FROM tbl_kegiatan
         WHERE id_kegiatan = ?
         FOR UPDATE"
    );

    mysqli_stmt_bind_param($stmt, "s", $id_kegiatan);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $kegiatan_lama = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$kegiatan_lama) {
        throw new Exception("Data kegiatan tidak ditemukan.");
    }

    $id_pagu_lama = $kegiatan_lama["id_pagu"];
    $realisasi_lama = (int) $kegiatan_lama["realisasi_pagu"];

    // ===============================
    // CEK PEGAWAI
    // ===============================
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id_pegawai
         FROM tbl_pegawai
         WHERE id_pegawai = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $id_pegawai);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        throw new Exception("Pegawai tidak ditemukan.");
    }

    mysqli_stmt_close($stmt);

    // ===============================
    // LOCK PAGU LAMA
    // ===============================
    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            id_pagu,
            nama_pagu,
            anggaran_pagu,
            sisa
         FROM tbl_pagu
         WHERE id_pagu = ?
         FOR UPDATE"
    );

    mysqli_stmt_bind_param($stmt, "s", $id_pagu_lama);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $pagu_lama = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$pagu_lama) {
        throw new Exception("Pagu lama tidak ditemukan.");
    }

    // ===============================
    // JIKA PAGU TIDAK BERUBAH
    // ===============================
    if ($id_pagu_lama === $id_pagu) {

        $sisa_lama = (int) $pagu_lama["sisa"];

        /*
         * Kembalikan dulu realisasi lama,
         * kemudian kurangi realisasi baru.
         */
        $sisa_baru = $sisa_lama + $realisasi_lama - $realisasi_pagu;

        if ($sisa_baru < 0) {
            throw new Exception(
                "Realisasi baru melebihi sisa anggaran pagu."
            );
        }

        // Update sisa pagu
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
            throw new Exception("Gagal memperbarui sisa pagu.");
        }

        mysqli_stmt_close($stmt);

    } else {

        // ===============================
        // PAGU BERUBAH
        // ===============================

        // --------------------------------
        // LOCK PAGU BARU
        // --------------------------------
        $stmt = mysqli_prepare(
            $conn,
            "SELECT
                id_pagu,
                nama_pagu,
                anggaran_pagu,
                sisa
             FROM tbl_pagu
             WHERE id_pagu = ?
             FOR UPDATE"
        );

        mysqli_stmt_bind_param($stmt, "s", $id_pagu);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $pagu_baru = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$pagu_baru) {
            throw new Exception("Pagu baru tidak ditemukan.");
        }

        $sisa_pagu_baru = (int) $pagu_baru["sisa"];

        // Cek apakah realisasi baru cukup
        if ($realisasi_pagu > $sisa_pagu_baru) {
            throw new Exception(
                "Realisasi anggaran melebihi sisa pagu yang dipilih."
            );
        }

        // --------------------------------
        // KEMBALIKAN REALISASI KE PAGU LAMA
        // --------------------------------
        $sisa_pagu_lama_baru =
            (int) $pagu_lama["sisa"] + $realisasi_lama;

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE tbl_pagu
             SET sisa = ?
             WHERE id_pagu = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "is",
            $sisa_pagu_lama_baru,
            $id_pagu_lama
        );

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception(
                "Gagal mengembalikan anggaran ke pagu lama."
            );
        }

        mysqli_stmt_close($stmt);

        // --------------------------------
        // KURANGI PAGU BARU
        // --------------------------------
        $sisa_pagu_baru_setelah =
            $sisa_pagu_baru - $realisasi_pagu;

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE tbl_pagu
             SET sisa = ?
             WHERE id_pagu = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "is",
            $sisa_pagu_baru_setelah,
            $id_pagu
        );

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception(
                "Gagal mengurangi sisa pagu baru."
            );
        }

        mysqli_stmt_close($stmt);
    }

    // ===============================
    // UPDATE DATA KEGIATAN
    // ===============================
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE tbl_kegiatan SET
            id_pegawai = ?,
            id_pagu = ?,
            nama_kegiatan = ?,
            tgl_mulai = ?,
            tgl_target = ?,
            target_kegiatan = ?,
            realisasi_pagu = ?,
            realisasi_persen = ?,
            risiko = ?,
            level_risiko = ?,
            mitigasi = ?,
            status_kesiapan = ?,
            catatan = ?
         WHERE id_kegiatan = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssiiisssss",
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
        $catatan,
        $id_kegiatan
    );

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new Exception(
            "Gagal memperbarui data kegiatan."
        );
    }

    mysqli_stmt_close($stmt);

    // ===============================
    // SIMPAN LOG
    // ===============================
    $data_log =
        "Kegiatan diperbarui oleh admin. " .
        "Realisasi anggaran: Rp " .
        number_format($realisasi_pagu, 0, ",", ".");

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO tbl_logkegiatan
            (id_kegiatan, log_time, data_log)
         VALUES
            (?, NOW(), ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $id_kegiatan,
        $data_log
    );

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new Exception("Gagal menyimpan log kegiatan.");
    }

    mysqli_stmt_close($stmt);

    // ===============================
    // COMMIT
    // ===============================
    mysqli_commit($conn);

    header("Location: index.php?status=edit");
    exit;

} catch (Exception $e) {

    // ===============================
    // ROLLBACK
    // ===============================
    mysqli_rollback($conn);

    die(
        "Gagal memperbarui kegiatan: " .
        htmlspecialchars($e->getMessage())
    );
}
?>