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

$title = "Tambah Kegiatan";


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
                Tambah Kegiatan
            </h2>

            <p class="text-muted mb-0">
                Tambahkan kegiatan baru ke dalam sistem.
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
                Form Data Kegiatan
            </h5>

            <small class="text-muted">
                Isi seluruh data kegiatan dengan benar.
            </small>

        </div>


        <!-- =================================================
             FORM
        ================================================== -->

        <form
            action="proses_tambah.php"
            method="POST"
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


                        <?php if (
                            $query_pegawai &&
                            mysqli_num_rows($query_pegawai) > 0
                        ): ?>


                            <?php while (
                                $pegawai = mysqli_fetch_assoc($query_pegawai)
                            ): ?>

                                <option
                                    value="<?= htmlspecialchars($pegawai["id_pegawai"]); ?>"
                                >

                                    <?= htmlspecialchars($pegawai["nama_pegawai"]); ?>

                                    <?php if (!empty($pegawai["nip_pegawai"])): ?>

                                        -
                                        <?= htmlspecialchars($pegawai["nip_pegawai"]); ?>

                                    <?php endif; ?>

                                </option>

                            <?php endwhile; ?>


                        <?php endif; ?>


                    </select>

                    <small class="text-muted">
                        Pilih pegawai yang bertanggung jawab atas kegiatan.
                    </small>

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


                        <?php if (
                            $query_pagu &&
                            mysqli_num_rows($query_pagu) > 0
                        ): ?>


                            <?php while (
                                $pagu = mysqli_fetch_assoc($query_pagu)
                            ): ?>

                                <option
                                    value="<?= htmlspecialchars($pagu["id_pagu"]); ?>"
                                    data-anggaran="<?= (int) $pagu["anggaran_pagu"]; ?>"
                                    data-sisa="<?= (int) $pagu["sisa"]; ?>"
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


                        <?php endif; ?>


                    </select>


                    <!-- INFO PAGU -->

                    <div
                        id="infoPagu"
                        class="mt-2 d-none"
                    >

                        <small class="text-muted">

                            Anggaran:
                            <strong id="infoAnggaran">
                                Rp 0
                            </strong>

                            &nbsp; | &nbsp;

                            Sisa:
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
                    placeholder="Masukkan nama kegiatan"
                    required
                >

            </div>


            <!-- =================================================
                 TANGGAL
            ================================================== -->

            <div class="row">


                <!-- TANGGAL MULAI -->

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Tanggal Mulai

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="date"
                        name="tgl_mulai"
                        class="form-control"
                        required
                    >

                </div>


                <!-- TANGGAL TARGET -->

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Tanggal Target Selesai

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="date"
                        name="tgl_target"
                        class="form-control"
                        required
                    >

                </div>


            </div>


            <!-- =================================================
                 TARGET KEGIATAN
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
                    placeholder="Jelaskan target yang ingin dicapai..."
                    required
                ></textarea>

            </div>


            <!-- =================================================
                 REALISASI
            ================================================== -->

            <div class="row">


                <!-- REALISASI PAGU -->

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
                            id="realisasi_pagu"
                            class="form-control"
                            value="0"
                            min="0"
                            step="1"
                            placeholder="0"
                        >

                    </div>

                    <small class="text-muted">

                        Masukkan jumlah anggaran yang sudah direalisasikan.

                    </small>

                </div>


                <!-- PERSENTASE -->

                <div class="col-md-6 mb-4">

                    <label class="form-label">

                        Persentase Realisasi

                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            name="realisasi_persen"
                            id="realisasi_persen"
                            class="form-control"
                            value="0"
                            min="0"
                            max="100"
                            step="1"
                        >

                        <span class="input-group-text">
                            %
                        </span>

                    </div>

                    <small class="text-muted">

                        Persentase realisasi kegiatan.

                    </small>

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
                    placeholder="Jelaskan risiko yang mungkin terjadi..."
                ></textarea>

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

                    <option value="rendah">
                        Rendah
                    </option>

                    <option value="sedang">
                        Sedang
                    </option>

                    <option value="tinggi">
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
                    placeholder="Jelaskan langkah mitigasi risiko..."
                ></textarea>

            </div>


            <!-- =================================================
                 STATUS KESIAPAN
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">

                    Status Kesiapan

                </label>

                <select
                    name="status_kesiapan"
                    class="form-select"
                >

                    <option value="Belum Siap">
                        Belum Siap
                    </option>

                    <option value="Proses">
                        Proses
                    </option>

                    <option value="Siap">
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
                    placeholder="Tambahkan catatan jika diperlukan..."
                ></textarea>

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

                    Simpan Kegiatan

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

const infoPagu = document.getElementById("infoPagu");

const infoAnggaran = document.getElementById("infoAnggaran");

const infoSisa = document.getElementById("infoSisa");


paguSelect.addEventListener("change", function () {

    const selectedOption =
        this.options[this.selectedIndex];


    if (!this.value) {

        infoPagu.classList.add("d-none");

        return;

    }


    const anggaran =
        parseInt(
            selectedOption.dataset.anggaran || 0
        );


    const sisa =
        parseInt(
            selectedOption.dataset.sisa || 0
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


    infoPagu.classList.remove("d-none");

});

</script>


<?php

include "../../includes/footer.php";

?>