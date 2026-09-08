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
// AMBIL ID KEGIATAN
// =====================================
$id_kegiatan = $_GET["id"] ?? "";

if (empty($id_kegiatan)) {
    header("Location: index.php");
    exit;
}

// =====================================
// AMBIL DATA KEGIATAN
// =====================================
$stmt = mysqli_prepare(
    $conn,
    "SELECT
        k.*,
        p.nama_pegawai,
        p.nip_pegawai,
        p.no_telp,
        a.nama_pagu,
        a.anggaran_pagu,
        a.sisa
     FROM tbl_kegiatan k
     LEFT JOIN tbl_pegawai p
        ON k.id_pegawai = p.id_pegawai
     LEFT JOIN tbl_pagu a
        ON k.id_pagu = a.id_pagu
     WHERE k.id_kegiatan = ?"
);

mysqli_stmt_bind_param($stmt, "s", $id_kegiatan);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$kegiatan = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// =====================================
// CEK DATA
// =====================================
if (!$kegiatan) {
    die("Data kegiatan tidak ditemukan.");
}

// =====================================
// AMBIL KOMPONEN
// =====================================
$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id_detail,
        id_komponen,
        nama_komponen,
        data_komponen,
        kesiapan_komponen,
        file_komponen
     FROM tbl_dtkomponen
     WHERE id_kegiatan = ?
     ORDER BY id_detail ASC"
);

mysqli_stmt_bind_param($stmt, "s", $id_kegiatan);
mysqli_stmt_execute($stmt);

$result_komponen = mysqli_stmt_get_result($stmt);

$komponen = [];

while ($row = mysqli_fetch_assoc($result_komponen)) {
    $komponen[] = $row;
}

mysqli_stmt_close($stmt);

// =====================================
// AMBIL LOG KEGIATAN
// =====================================
$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id_log,
        log_time,
        data_log
     FROM tbl_logkegiatan
     WHERE id_kegiatan = ?
     ORDER BY log_time DESC, id_log DESC"
);

mysqli_stmt_bind_param($stmt, "s", $id_kegiatan);
mysqli_stmt_execute($stmt);

$result_log = mysqli_stmt_get_result($stmt);

$logs = [];

while ($row = mysqli_fetch_assoc($result_log)) {
    $logs[] = $row;
}

mysqli_stmt_close($stmt);

// =====================================
// FORMAT DATA
// =====================================
$anggaran_pagu = (int) $kegiatan["anggaran_pagu"];
$realisasi_pagu = (int) $kegiatan["realisasi_pagu"];
$sisa_pagu = (int) $kegiatan["sisa"];

$persen = (int) $kegiatan["realisasi_persen"];

?>

<?php require_once "../../includes/header.php"; ?>

<div class="container-fluid">

    <!-- =====================================
         JUDUL HALAMAN
    ====================================== -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-eye me-2"></i>
                Detail Kegiatan
            </h4>

            <p class="text-muted mb-0">
                Informasi lengkap kegiatan
            </p>
        </div>

        <div>

            <a href="edit.php?id=<?= urlencode($id_kegiatan); ?>"
               class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>

            <a href="index.php"
               class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>

        </div>

    </div>


    <!-- =====================================
         INFORMASI UTAMA
    ====================================== -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-info-circle me-2"></i>
                Informasi Kegiatan
            </h5>

        </div>

        <div class="card-body">

            <div class="row g-4">

                <!-- NAMA KEGIATAN -->
                <div class="col-md-8">

                    <label class="text-muted small">
                        Nama Kegiatan
                    </label>

                    <div class="fw-bold fs-5">
                        <?= htmlspecialchars($kegiatan["nama_kegiatan"]); ?>
                    </div>

                </div>


                <!-- STATUS -->
                <div class="col-md-4">

                    <label class="text-muted small">
                        Status Kesiapan
                    </label>

                    <div class="mt-1">

                        <?php if ($kegiatan["status_kesiapan"] === "Siap"): ?>

                            <span class="badge bg-success fs-6">
                                Siap
                            </span>

                        <?php elseif ($kegiatan["status_kesiapan"] === "Proses"): ?>

                            <span class="badge bg-warning text-dark fs-6">
                                Proses
                            </span>

                        <?php else: ?>

                            <span class="badge bg-secondary fs-6">
                                Belum Siap
                            </span>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- PEGAWAI -->
                <div class="col-md-4">

                    <label class="text-muted small">
                        Pegawai Penanggung Jawab
                    </label>

                    <div class="fw-semibold">
                        <?= htmlspecialchars($kegiatan["nama_pegawai"] ?? "-"); ?>
                    </div>

                    <small class="text-muted">
                        NIP:
                        <?= htmlspecialchars($kegiatan["nip_pegawai"] ?? "-"); ?>
                    </small>

                </div>


                <!-- PAGU -->
                <div class="col-md-4">

                    <label class="text-muted small">
                        Pagu Anggaran
                    </label>

                    <div class="fw-semibold">
                        <?= htmlspecialchars($kegiatan["nama_pagu"] ?? "-"); ?>
                    </div>

                    <small class="text-muted">
                        Total:
                        Rp <?= number_format($anggaran_pagu, 0, ",", "."); ?>
                    </small>

                </div>


                <!-- TANGGAL -->
                <div class="col-md-4">

                    <label class="text-muted small">
                        Periode Kegiatan
                    </label>

                    <div class="fw-semibold">

                        <?= date(
                            "d/m/Y",
                            strtotime($kegiatan["tgl_mulai"])
                        ); ?>

                        s/d

                        <?= date(
                            "d/m/Y",
                            strtotime($kegiatan["tgl_target"])
                        ); ?>

                    </div>

                </div>


                <!-- TARGET -->
                <div class="col-12">

                    <label class="text-muted small">
                        Target Kegiatan
                    </label>

                    <div class="p-3 bg-light rounded">
                        <?= nl2br(
                            htmlspecialchars(
                                $kegiatan["target_kegiatan"] ?? "-"
                            )
                        ); ?>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================
         ANGGARAN
    ====================================== -->
    <div class="row g-4 mb-4">

        <!-- PAGU -->
        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small">
                        Total Pagu
                    </div>

                    <h4 class="fw-bold mt-2">
                        Rp <?= number_format(
                            $anggaran_pagu,
                            0,
                            ",",
                            "."
                        ); ?>
                    </h4>

                </div>

            </div>

        </div>


        <!-- REALISASI -->
        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small">
                        Realisasi Kegiatan
                    </div>

                    <h4 class="fw-bold text-primary mt-2">
                        Rp <?= number_format(
                            $realisasi_pagu,
                            0,
                            ",",
                            "."
                        ); ?>
                    </h4>

                    <div class="progress mt-3" style="height: 8px;">

                        <div
                            class="progress-bar"
                            role="progressbar"
                            style="width: <?= $persen; ?>%;"
                            aria-valuenow="<?= $persen; ?>"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>

                    </div>

                    <small class="text-muted">
                        <?= $persen; ?>% realisasi
                    </small>

                </div>

            </div>

        </div>


        <!-- SISA -->
        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small">
                        Sisa Pagu
                    </div>

                    <h4 class="fw-bold text-success mt-2">
                        Rp <?= number_format(
                            $sisa_pagu,
                            0,
                            ",",
                            "."
                        ); ?>
                    </h4>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================
         RISIKO
    ====================================== -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Risiko dan Mitigasi
            </h5>

        </div>

        <div class="card-body">

            <div class="row g-4">

                <!-- LEVEL RISIKO -->
                <div class="col-md-4">

                    <label class="text-muted small">
                        Level Risiko
                    </label>

                    <div class="mt-2">

                        <?php if ($kegiatan["level_risiko"] === "tinggi"): ?>

                            <span class="badge bg-danger fs-6">
                                Tinggi
                            </span>

                        <?php elseif ($kegiatan["level_risiko"] === "sedang"): ?>

                            <span class="badge bg-warning text-dark fs-6">
                                Sedang
                            </span>

                        <?php elseif ($kegiatan["level_risiko"] === "rendah"): ?>

                            <span class="badge bg-success fs-6">
                                Rendah
                            </span>

                        <?php else: ?>

                            <span class="text-muted">
                                -
                            </span>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- RISIKO -->
                <div class="col-md-8">

                    <label class="text-muted small">
                        Risiko
                    </label>

                    <div class="p-3 bg-light rounded mt-1">
                        <?= nl2br(
                            htmlspecialchars(
                                $kegiatan["risiko"] ?? "-"
                            )
                        ); ?>
                    </div>

                </div>


                <!-- MITIGASI -->
                <div class="col-12">

                    <label class="text-muted small">
                        Mitigasi
                    </label>

                    <div class="p-3 bg-light rounded mt-1">
                        <?= nl2br(
                            htmlspecialchars(
                                $kegiatan["mitigasi"] ?? "-"
                            )
                        ); ?>
                    </div>

                </div>


                <!-- CATATAN -->
                <div class="col-12">

                    <label class="text-muted small">
                        Catatan
                    </label>

                    <div class="p-3 bg-light rounded mt-1">
                        <?= nl2br(
                            htmlspecialchars(
                                $kegiatan["catatan"] ?? "-"
                            )
                        ); ?>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================
         KOMPONEN KEGIATAN
    ====================================== -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-list-check me-2"></i>
                Komponen Kegiatan
            </h5>

        </div>

        <div class="card-body">

            <?php if (count($komponen) === 0): ?>

                <div class="text-center py-4 text-muted">

                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>

                    Belum ada komponen kegiatan.

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-bordered align-middle">

                        <thead class="table-light">

                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Komponen</th>
                                <th>Data Komponen</th>
                                <th>Kesiapan</th>
                                <th>File</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php $no = 1; ?>

                            <?php foreach ($komponen as $item): ?>

                                <tr>

                                    <td>
                                        <?= $no++; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $item["nama_komponen"]
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= nl2br(
                                            htmlspecialchars(
                                                $item["data_komponen"] ?? "-"
                                            )
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= nl2br(
                                            htmlspecialchars(
                                                $item["kesiapan_komponen"] ?? "-"
                                            )
                                        ); ?>
                                    </td>

                                    <td>

                                        <?php if (!empty($item["file_komponen"])): ?>

                                            <span class="badge bg-success">
                                                <i class="bi bi-file-earmark"></i>
                                                Ada file
                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                Tidak ada

                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- =====================================
         RIWAYAT LOG
    ====================================== -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-clock-history me-2"></i>
                Riwayat Kegiatan
            </h5>

        </div>

        <div class="card-body">

            <?php if (count($logs) === 0): ?>

                <div class="text-center py-4 text-muted">

                    <i class="bi bi-clock fs-1 d-block mb-2"></i>

                    Belum ada riwayat kegiatan.

                </div>

            <?php else: ?>

                <div class="list-group list-group-flush">

                    <?php foreach ($logs as $log): ?>

                        <div class="list-group-item px-0">

                            <div class="d-flex">

                                <div class="me-3">

                                    <span
                                        class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white"
                                        style="width: 38px; height: 38px;">

                                        <i class="bi bi-clock"></i>

                                    </span>

                                </div>

                                <div>

                                    <div class="fw-semibold">

                                        <?= htmlspecialchars(
                                            $log["data_log"]
                                        ); ?>

                                    </div>

                                    <small class="text-muted">

                                        <?= date(
                                            "d/m/Y H:i",
                                            strtotime($log["log_time"])
                                        ); ?>

                                    </small>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php require_once "../../includes/footer.php"; ?>