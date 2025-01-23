<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Surat Izin Keramaian">
    <meta name="author" content="Azijah imut">
    <title>Surat Izin Keramaian</title>
    <link rel="icon" href="dist/img/POLDA KALSEL.svg">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
        body {
            background-image: url('dist/img/polresbg.jpg');
            background-size: cover;
            background-position: center;
            overflow: hidden;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-container {
            display: flex;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .logo-container {
            background-color: #f8f9fa;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-right: 1px solid #ddd;
        }

        .logo-container img {
            max-width: 200px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .logo-container .title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
        }

        .logo-container .subtitle {
            font-size: 1rem;
            color: #34495e;
            text-align: center;
        }

        .form-container {
            padding: 20px;
            flex: 1;
        }

        .form-container .card-body {
            padding: 20px;
        }

        .form-container .login-box-msg {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
        }

        .form-container .btn {
            width: 100%;
        }

        .form-container .text-center {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <!-- Logo Section -->
        <div class="logo-container">
            <img src="dist/img/POLDA KALSEL.svg" alt="Logo">
            <div class="title">SIKO</div>
            <div class="subtitle">Surat Izin Keramaian Online</div>
        </div>

        <!-- Form Section -->
        <div class="form-container">
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">Daftar Akun</p>
                    <form action="check_regis.php" method="post">
                        <div class="input-group mb-3">
                            <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            <h6 id="username-feedback"></h6>
                        </div>
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-eye" onclick="togglePassword()" style="cursor: pointer;"></span>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                    </form>
                    <div class="text-center">
                        <a href="index.php" class="text-primary">Sudah Punya Akun? Masuk</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function togglePassword() {
            const passwordField = document.getElementById("password");
            passwordField.type = passwordField.type === "password" ? "text" : "password";
        }
    </script>
</body>

</html>