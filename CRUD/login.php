<?php
session_start();
include "konek.php";

// 1. Jika sudah ada session, langsung redirect ke dashboard
if(isset($_SESSION['admin_id'])) {
    header("location: dashboard.php");
    exit();
}

$error_message = ""; // Variabel untuk menyimpan pesan error

// 2. Proses data saat form disubmit
if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil input dari form
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password_dari_form = $_POST['password']; // Password plaintext dari form

    // 3. Cari admin berdasarkan username
    $query_admin = "SELECT * FROM admin WHERE username = '$username'";
    $result_admin = mysqli_query($conn, $query_admin);

    if($result_admin && mysqli_num_rows($result_admin) == 1) {
        // Username ditemukan
        $admin = mysqli_fetch_assoc($result_admin);

        // 4. Verifikasi password DENGAN HASH (CARA AMAN)
        // Membandingkan password dari form dengan HASH di database
        if(password_verify($password_dari_form, $admin['password'])) {
            // Password cocok!
            
            // 5. Simpan ID admin ke session
            $_SESSION['admin_id'] = $admin['id_admin'];
            
            // 6. Redirect ke dashboard
            header("location: dashboard.php");
            exit();
        } else {
            // Password salah
            $error_message = "Username atau password salah.";
        }
    } else {
        // Username tidak ditemukan
        $error_message = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Informasi Gunung Berapi</title>
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
        <h3 class="text-center mb-4"><i class="fas fa-user-shield me-2"></i>Login Admin</h3>
        
        <?php 
        // Tampilkan pesan error jika ada
        if(!empty($error_message)): 
        ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>