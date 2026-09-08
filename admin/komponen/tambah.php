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

?>

<?php require_once "../../includes/header.php"; ?>

<div class="container-fluid">

    <!-- =====================================
         JUDUL
    ====================================== -->
    <div class="mb-4">

        <h4 class="fw-bold mb-1">
            <i class="bi bi-plus-circle me-2"></i>
            Tambah Komponen
        </h4>

        <p class="text-muted mb-0">
            Tambahkan master komponen kegiatan
        </p>

    </div>


    <!-- =====================================
         FORM
    ====================================== -->
    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form action="proses_tambah.php"
                  method="POST">

                <!-- ID KOMPONEN -->
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        ID Komponen
                    </label>

                    <input type="text"
                           name="id_komponen"
                           class="form-control"
                           placeholder="Contoh: K001"
                           required>

                    <small class="text-muted">
                        Gunakan ID yang unik.
                    </small>

                </div>


                <!-- KESIAPAN -->
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Kesiapan Persen
                    </label>

                    <div class="input-group">

                        <input type="number"
                               name="kesiapan_persen"
                               class="form-control"
                               min="0"
                               max="100"
                               value="0"
                               required>

                        <span class="input-group-text">
                            %
                        </span>

                    </div>

                </div>


                <!-- TOMBOL -->
                <div class="d-flex gap-2">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-save me-1"></i>
                        Simpan

                    </button>

                    <a href="index.php"
                       class="btn btn-secondary">

                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<?php require_once "../../includes/footer.php"; ?>