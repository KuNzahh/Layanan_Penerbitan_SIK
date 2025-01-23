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
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>
    body {
      background-image: url('dist/img/polresbg.jpg');
      background-size: cover;
      background-position: center;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-container {
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
      font-size: 1.5rem;
      font-weight: bold;
      color: #2c3e50;
      letter-spacing: 1px;
    }

    .logo-container .subtitle {
      font-size: 1rem;
      color: #34495e;
      margin-top: 5px;
    }

    .login-box {
      padding: 20px;
      flex: 1;
    }

    .login-box .card-body {
      padding: 20px;
    }

    .login-box .login-box-msg {
      font-size: 1.5rem;
      font-weight: bold;
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="logo-container">
      <img src="dist/img/POLDA KALSEL.svg" alt="Logo">
      <div class="title">SIKO</div>
      <div class="subtitle">Surat Izin Keramaian Online</div>
    </div>
    <div class="login-box">
      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg">LOGIN</p>
          <form action="include/login.php" method="post">
            <div class="input-group mb-3">
              <input type="text" name="username" class="form-control" placeholder="Username" required>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user"></span>
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
            <div class="row">
              <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">Login</button>
              </div>
            </div>
          </form>
          <hr>
          <p class="text-center">
            <a href="forget.php" class="text-danger">Lupa Password?</a>
          </p>
          <p class="text-center">
            <a href="register.php" class="btn btn-warning btn-block">Buat Akun Baru!</a>
          </p>
        </div>
      </div>
    </div>
  </div>
  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>

</html>
