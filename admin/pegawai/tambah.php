<?php

session_start();


// Cek login

if (!isset($_SESSION["id_user"])) {

    header("Location: ../../auth/login.php");

    exit;
}


// Cek admin

if (
    !isset($_SESSION["level"]) ||
    strtolower($_SESSION["level"]) !== "admin"
) {

    header("Location: ../../index.php");

    exit;
}


require_once "../../config/database.php";

$title = "Tambah Pegawai";


include "../../includes/header.php";

include "../../includes/sidebar.php";

include "../../includes/navbar.php";

?>


<main class="main-content">


    <!-- HEADER -->

    <div class="mb-4">

        <h2 class="page-title mb-1">
            Tambah Pegawai
        </h2>

        <p class="text-muted mb-0">
            Tambahkan data pegawai baru.
        </p>

    </div>


    <!-- FORM -->

    <div class="content-card">

        <form
            action="proses_tambah.php"
            method="POST"
        >


            <!-- NAMA -->

            <div class="mb-3">

                <label class="form-label">
                    Nama Pegawai
                </label>

                <input
                    type="text"
                    name="nama_pegawai"
                    class="form-control"
                    placeholder="Masukkan nama pegawai"
                    maxlength="64"
                    required
                >

            </div>


            <!-- NIP -->

            <div class="mb-3">

                <label class="form-label">
                    NIP
                </label>

                <input
                    type="text"
                    name="nip_pegawai"
                    class="form-control"
                    placeholder="Masukkan NIP"
                    maxlength="64"
                >

            </div>


            <!-- TELEPON -->

            <div class="mb-4">

                <label class="form-label">
                    Nomor Telepon
                </label>

                <input
                    type="text"
                    name="no_telp"
                    class="form-control"
                    placeholder="Masukkan nomor telepon"
                    maxlength="16"
                >

            </div>


            <!-- BUTTON -->

            <div class="d-flex gap-2">

                <a
                    href="index.php"
                    class="btn btn-secondary"
                >

                    <i class="bi bi-arrow-left me-2"></i>

                    Kembali

                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="bi bi-save me-2"></i>

                    Simpan Pegawai

                </button>

            </div>


        </form>

    </div>


</main>


<?php

include "../../includes/footer.php";

?>