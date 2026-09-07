<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
    $_SESSION["login_error"] = "Username dan password wajib diisi.";
    header("Location: login.php");
    exit;
}

$query = "SELECT 
            id_user,
            id_pegawai,
            username_user,
            password_user,
            level_user
          FROM tbl_user
          WHERE username_user = ?
          LIMIT 1";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

if (!$user || !password_verify($password, $user["password_user"])) {

    $_SESSION["login_error"] = "Username atau password salah.";

    header("Location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Login berhasil
|--------------------------------------------------------------------------
*/

session_regenerate_id(true);

$_SESSION["id_user"] = $user["id_user"];
$_SESSION["id_pegawai"] = $user["id_pegawai"];
$_SESSION["username"] = $user["username_user"];
$_SESSION["level"] = $user["level_user"];

header("Location: ../index.php");
exit;