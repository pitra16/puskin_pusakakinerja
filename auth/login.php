<?php

session_start();

if (isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - PUSKIN</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f3f6fb;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            border-radius: 16px;
            background: #173b67;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .login-title {
            text-align: center;
            color: #173b67;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .login-subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control {
            height: 48px;
            border-radius: 8px;
        }

        .btn-login {
            height: 48px;
            background: #173b67;
            border: none;
            color: white;
            border-radius: 8px;
            font-weight: bold;
        }

        .btn-login:hover {
            background: #0f2d50;
            color: white;
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            color: #999;
            font-size: 13px;
        }

    </style>

</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="logo">
            <i class="bi bi-building"></i>
        </div>

        <h3 class="login-title">
            PUSKIN
        </h3>

        <p class="login-subtitle">
            Sistem Monitoring
        </p>

        <?php if ($error): ?>

            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?= htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <form action="proses_login.php" method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    class="form-control"
                    placeholder="Masukkan username"
                    required
                    autocomplete="username"
                >

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Masukkan password"
                    required
                    autocomplete="current-password"
                >

            </div>

            <button
                type="submit"
                class="btn btn-login w-100"
            >
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Masuk
            </button>

        </form>

        <div class="footer-text">
            &copy; <?= date('Y'); ?> PUSKIN
        </div>

    </div>

</div>

</body>
</html>