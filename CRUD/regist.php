<?php
// Buat Kode Logic PHP nya jika ada
include "konek.php";

$error_message = "";
$success_message = "";

if(isset($_POST["submit"])){
    $username = strtolower(trim($_POST["username"]));
    $password = $_POST["password"];
    
    // Validasi input
    if(empty($username) || empty($password)) {
        $error_message = "Username dan password harus diisi!";
    } else {
        // cek apakah username sudah ada
        $chek = mysqli_query($conn, "SELECT * FROM admin WHERE username = '$username'");
        if(mysqli_num_rows($chek) > 0){
            $error_message = "Username sudah terdaftar!";
        } else {
            // hash password
            $hasedPassword = password_hash($password, PASSWORD_DEFAULT);

            // SIMPAN KE DATABASE
            $query = "INSERT INTO admin VALUES ('','$username','$hasedPassword')";
            $q = mysqli_query($conn, $query);
            if($q){
                $success_message = "Registrasi Berhasil!";
                // Tunggu sebentar sebelum redirect
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
                </script>";
            } else {
                $error_message = "Terjadi kesalahan: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Admin - Sistem Informasi Gunung Berapi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background-color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="card login-card">
        <h3 class="text-center mb-4"><i class="fas fa-user-shield me-2"></i>Signup Admin</h3>
        
        <?php 
        // Tampilkan pesan error jika ada
        if(!empty($error_message)): 
        ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php 
        // Tampilkan pesan sukses jika ada
        if(!empty($success_message)): 
        ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100 mt-3">Daftar</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Sudah punya akun? Login</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>