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
// CEK ID KEGIATAN
// =====================================================

if (!isset($_GET["id"]) || empty($_GET["id"])) {

    header("Location: index.php");

    exit;
}

$id_kegiatan = $_GET["id"];


// =====================================================
// AMBIL DATA KEGIATAN
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        k.id_kegiatan,
        k.id_pegawai,
        k.id_pagu,
        k.nama_kegiatan,
        k.tgl_mulai,
        k.tgl_target,
        k.target_kegiatan,
        k.realisasi_pagu,
        k.realisasi_persen,
        k.risiko,
        k.level_risiko,
        k.mitigasi,
        k.status_kesiapan,
        k.catatan,

        p.nama_pegawai,

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

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id_kegiatan
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$kegiatan = mysqli_fetch_assoc($result);


// =====================================================
// CEK DATA
// =====================================================

if (!$kegiatan) {

    header("Location: index.php");

    exit;
}


// =====================================================
// DATA PEGAWAI
// =====================================================

$query_pegawai = mysqli_query(
    $conn,
    "SELECT
        id_pegawai,
        nama_pegawai,
        nip_pegawai
     FROM tbl_pegawai
     ORDER BY nama_pegawai ASC"
);


// =====================================================
// DATA PAGU
// =====================================================

$query_pagu = mysqli_query(
    $conn,
    "SELECT
        id_pagu,
        nama_pagu,
        anggaran_pagu,
        sisa
     FROM tbl_pagu
     ORDER BY nama_pagu ASC"
);


// =====================================================
// JUDUL
// =====================================================

$title = "Edit Kegiatan";


// =====================================================
// TEMPLATE
// =====================================================

include "../../includes/header.php";

include "../../includes/sidebar.php";

include "../../includes/navbar.php";

?>


<main class="main-content">


    <!-- =================================================
         HEADER
    ================================================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="page-title mb-1">
                Edit Kegiatan
            </h2>

            <p class="text-muted mb-0">
                Perbarui data kegiatan.
            </p>

        </div>


        <a
            href="index.php"
            class="btn btn-secondary"
        >

            <i class="bi bi-arrow-left me-2"></i>

            Kembali

        </a>

    </div>


    <!-- =================================================
         CARD
    ================================================== -->

    <div class="content-card">


        <div class="mb-4">

            <h5 class="mb-1">
                Form Edit Kegiatan
            </h5>

            <small class="text-muted">
                Perbarui data kegiatan dengan benar.
            </small>

        </div>


        <form
            action="proses_edit.php"
            method="POST"
        >


            <!-- =================================================
                 ID KEGIATAN
            ================================================== -->

            <input
                type="hidden"
                name="id_kegiatan"
                value="<?= htmlspecialchars($kegiatan["id_kegiatan"]); ?>"
            >


            <!-- =================================================
                 PEGAWAI & PAGU
            ================================================== -->

            <div class="row">


                <!-- PEGAWAI -->

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Pegawai

                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="id_pegawai"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Pegawai --
                        </option>


                        <?php while (
                            $pegawai = mysqli_fetch_assoc($query_pegawai)
                        ): ?>

                            <option
                                value="<?= htmlspecialchars($pegawai["id_pegawai"]); ?>"
                                <?= $pegawai["id_pegawai"] === $kegiatan["id_pegawai"] ? "selected" : ""; ?>
                            >

                                <?= htmlspecialchars($pegawai["nama_pegawai"]); ?>

                                <?php if (!empty($pegawai["nip_pegawai"])): ?>

                                    -
                                    <?= htmlspecialchars($pegawai["nip_pegawai"]); ?>

                                <?php endif; ?>

                            </option>

                        <?php endwhile; ?>


                    </select>

                </div>


                <!-- PAGU -->

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Pagu Anggaran

                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="id_pagu"
                        id="id_pagu"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Pagu --
                        </option>


                        <?php while (
                            $pagu = mysqli_fetch_assoc($query_pagu)
                        ): ?>

                            <?php

                            /*
                             * Saat edit, sisa pagu masih mengandung
                             * realisasi kegiatan yang sedang diedit.
                             *
                             * Karena itu kita tambahkan kembali
                             * realisasi lama untuk informasi sementara.
                             */

                            $sisa_tersedia =
                                (int) $pagu["sisa"];

                            if (
                                $pagu["id_pagu"] ===
                                $kegiatan["id_pagu"]
                            ) {

                                $sisa_tersedia +=
                                    (int) $kegiatan["realisasi_pagu"];
                            }

                            ?>


                            <option
                                value="<?= htmlspecialchars($pagu["id_pagu"]); ?>"
                                data-anggaran="<?= (int) $pagu["anggaran_pagu"]; ?>"
                                data-sisa="<?= $sisa_tersedia; ?>"
                                <?= $pagu["id_pagu"] === $kegiatan["id_pagu"] ? "selected" : ""; ?>
                            >

                                <?= htmlspecialchars($pagu["nama_pagu"]); ?>

                                -
                                Rp
                                <?= number_format(
                                    (int) $pagu["anggaran_pagu"],
                                    0,
                                    ",",
                                    "."
                                ); ?>

                            </option>

                        <?php endwhile; ?>


                    </select>


                    <div
                        id="infoPagu"
                        class="mt-2"
                    >

                        <small class="text-muted">

                            Anggaran:
                            <strong id="infoAnggaran">
                                Rp 0
                            </strong>

                            &nbsp; | &nbsp;

                            Sisa tersedia:
                            <strong
                                id="infoSisa"
                                class="text-success"
                            >
                                Rp 0
                            </strong>

                        </small>

                    </div>

                </div>


            </div>


            <!-- =================================================
                 NAMA KEGIATAN
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">

                    Nama Kegiatan

                    <span class="text-danger">*</span>

                </label>

                <input
                    type="text"
                    name="nama_kegiatan"
                    class="form-control"
                    value="<?= htmlspecialchars($kegiatan["nama_kegiatan"]); ?>"
                    required
                >

            </div>


            <!-- =================================================
                 TANGGAL
            ================================================== -->

            <div class="row">


                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Tanggal Mulai

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="date"
                        name="tgl_mulai"
                        class="form-control"
                        value="<?= htmlspecialchars($kegiatan["tgl_mulai"]); ?>"
                        required
                    >

                </div>


                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Tanggal Target Selesai

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="date"
                        name="tgl_target"
                        class="form-control"
                        value="<?= htmlspecialchars($kegiatan["tgl_target"]); ?>"
                        required
                    >

                </div>


            </div>


            <!-- =================================================
                 TARGET
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">

                    Target Kegiatan

                    <span class="text-danger">*</span>

                </label>

                <textarea
                    name="target_kegiatan"
                    class="form-control"
                    rows="3"
                    required
                ><?= htmlspecialchars($kegiatan["target_kegiatan"]); ?></textarea>

            </div>


            <!-- =================================================
                 REALISASI
            ================================================== -->

            <div class="row">


                <div class="col-md-6 mb-4">

                    <label class="form-label">
                        Realisasi Anggaran
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            Rp
                        </span>

                        <input
                            type="number"
                            name="realisasi_pagu"
                            class="form-control"
                            value="<?= (int) $kegiatan["realisasi_pagu"]; ?>"
                            min="0"
                            step="1"
                        >

                    </div>

                </div>


                <div class="col-md-6 mb-4">

                    <label class="form-label">
                        Persentase Realisasi
                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            name="realisasi_persen"
                            class="form-control"
                            value="<?= (int) $kegiatan["realisasi_persen"]; ?>"
                            min="0"
                            max="100"
                            step="1"
                        >

                        <span class="input-group-text">
                            %
                        </span>

                    </div>

                </div>


            </div>


            <!-- =================================================
                 RISIKO
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">
                    Risiko
                </label>

                <textarea
                    name="risiko"
                    class="form-control"
                    rows="3"
                ><?= htmlspecialchars($kegiatan["risiko"] ?? ""); ?></textarea>

            </div>


            <!-- =================================================
                 LEVEL RISIKO
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">
                    Level Risiko
                </label>

                <select
                    name="level_risiko"
                    class="form-select"
                >

                    <option value="">
                        -- Pilih Level Risiko --
                    </option>

                    <option
                        value="rendah"
                        <?= strtolower($kegiatan["level_risiko"] ?? "") === "rendah" ? "selected" : ""; ?>
                    >
                        Rendah
                    </option>

                    <option
                        value="sedang"
                        <?= strtolower($kegiatan["level_risiko"] ?? "") === "sedang" ? "selected" : ""; ?>
                    >
                        Sedang
                    </option>

                    <option
                        value="tinggi"
                        <?= strtolower($kegiatan["level_risiko"] ?? "") === "tinggi" ? "selected" : ""; ?>
                    >
                        Tinggi
                    </option>

                </select>

            </div>


            <!-- =================================================
                 MITIGASI
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">
                    Mitigasi
                </label>

                <textarea
                    name="mitigasi"
                    class="form-control"
                    rows="3"
                ><?= htmlspecialchars($kegiatan["mitigasi"] ?? ""); ?></textarea>

            </div>


            <!-- =================================================
                 STATUS
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">
                    Status Kesiapan
                </label>

                <select
                    name="status_kesiapan"
                    class="form-select"
                >

                    <option
                        value="Belum Siap"
                        <?= strtolower($kegiatan["status_kesiapan"] ?? "") === "belum siap" ? "selected" : ""; ?>
                    >
                        Belum Siap
                    </option>

                    <option
                        value="Proses"
                        <?= strtolower($kegiatan["status_kesiapan"] ?? "") === "proses" ? "selected" : ""; ?>
                    >
                        Proses
                    </option>

                    <option
                        value="Siap"
                        <?= strtolower($kegiatan["status_kesiapan"] ?? "") === "siap" ? "selected" : ""; ?>
                    >
                        Siap
                    </option>

                </select>

            </div>


            <!-- =================================================
                 CATATAN
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">
                    Catatan
                </label>

                <textarea
                    name="catatan"
                    class="form-control"
                    rows="3"
                ><?= htmlspecialchars($kegiatan["catatan"] ?? ""); ?></textarea>

            </div>


            <!-- =================================================
                 TOMBOL
            ================================================== -->

            <div class="d-flex justify-content-end gap-2 mt-4">

                <a
                    href="index.php"
                    class="btn btn-secondary"
                >

                    Batal

                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="bi bi-save me-2"></i>

                    Simpan Perubahan

                </button>

            </div>


        </form>


    </div>


</main>


<!-- =====================================================
     SCRIPT INFORMASI PAGU
====================================================== -->

<script>

const paguSelect = document.getElementById("id_pagu");

const infoAnggaran = document.getElementById("infoAnggaran");

const infoSisa = document.getElementById("infoSisa");


function tampilkanInfoPagu() {

    const option =
        paguSelect.options[paguSelect.selectedIndex];


    if (!option || !option.value) {

        infoAnggaran.textContent = "Rp 0";

        infoSisa.textContent = "Rp 0";

        return;
    }


    const anggaran =
        parseInt(
            option.dataset.anggaran || 0
        );


    const sisa =
        parseInt(
            option.dataset.sisa || 0
        );


    infoAnggaran.textContent =
        "Rp " +
        new Intl.NumberFormat("id-ID").format(
            anggaran
        );


    infoSisa.textContent =
        "Rp " +
        new Intl.NumberFormat("id-ID").format(
            sisa
        );

}


paguSelect.addEventListener(
    "change",
    tampilkanInfoPagu
);


tampilkanInfoPagu();

</script>


<?php

include "../../includes/footer.php";

?>