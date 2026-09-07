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
// JUDUL
// =====================================================

$title = "Tambah Pagu Anggaran";


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
                Tambah Pagu Anggaran
            </h2>

            <p class="text-muted mb-0">
                Tambahkan data pagu anggaran baru.
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
                Form Pagu Anggaran
            </h5>

            <small class="text-muted">
                Silakan isi data pagu dengan benar.
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
                 NAMA PAGU
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">

                    Nama Pagu

                    <span class="text-danger">
                        *
                    </span>

                </label>

                <input
                    type="text"
                    name="nama_pagu"
                    class="form-control"
                    placeholder="Contoh: Pagu Belanja Operasional"
                    required
                    maxlength="64"
                    autocomplete="off"
                >

                <small class="text-muted">

                    Masukkan nama atau sumber pagu anggaran.

                </small>

            </div>


            <!-- =================================================
                 ANGGARAN
            ================================================== -->

            <div class="mb-4">

                <label class="form-label">

                    Anggaran

                    <span class="text-danger">
                        *
                    </span>

                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        Rp
                    </span>

                    <input
                        type="number"
                        name="anggaran_pagu"
                        class="form-control"
                        placeholder="0"
                        min="0"
                        step="1"
                        required
                    >

                </div>

                <small class="text-muted">

                    Masukkan jumlah anggaran tanpa tanda titik atau koma.
                    Contoh: 50000000

                </small>

            </div>


            <!-- =================================================
                 INFORMASI SISA
            ================================================== -->

            <div class="alert alert-info">

                <i class="bi bi-info-circle me-2"></i>

                <strong>Informasi:</strong>

                Saat pagu pertama kali dibuat,
                <strong>sisa anggaran</strong> akan otomatis disamakan
                dengan jumlah anggaran awal.

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

                    Simpan Pagu

                </button>


            </div>


        </form>


    </div>


</main>


<?php

include "../../includes/footer.php";

?>