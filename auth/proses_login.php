<?php

session_start();

require_once "../config/database.php";

// =====================================
// CEK REQUEST
// =====================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login.php");
    exit;

}


// =====================================
// AMBIL DATA LOGIN
// =====================================

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";


// =====================================
// VALIDASI
// =====================================

if ($username === "" || $password === "") {

    $_SESSION["login_error"] = "Username dan password wajib diisi.";

    header("Location: login.php");
    exit;

}


// =====================================
// CARI USER
// =====================================

$query = "
    SELECT
        id_user,
        id_pegawai,
        username_user,
        password_user,
        level_user
    FROM tbl_user
    WHERE username_user = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $username
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


// =====================================
// CEK USER & PASSWORD
// =====================================

if (
    !$user ||
    !password_verify(
        $password,
        $user["password_user"]
    )
) {

    $_SESSION["login_error"] =
        "Username atau password salah.";

    header("Location: login.php");
    exit;

}


// =====================================
// LOGIN BERHASIL
// =====================================

session_regenerate_id(true);


// =====================================
// SIMPAN SESSION
// =====================================

$_SESSION["id_user"] = $user["id_user"];

$_SESSION["id_pegawai"] = $user["id_pegawai"];

$_SESSION["username"] = $user["username_user"];


// Session utama
$_SESSION["level"] = $user["level_user"];


// Session kompatibilitas
$_SESSION["level_user"] = $user["level_user"];


// =====================================
// MASUK DASHBOARD
// =====================================

header("Location: ../index.php");

exit;

?>