<?php
session_start();
include "konek.php";

// if(!isset($_SESSION['admin_id'])) {
//     header("location: login.php");
//     exit();
// }

$admin_id = $_SESSION['admin_id'];
$message = '';
$message_type = '';

// Ambil data admin
$query_admin = "SELECT * FROM admin WHERE id_admin = '$admin_id'";
$result_admin = mysqli_query($conn, $query_admin);
$admin = mysqli_fetch_assoc($result_admin);

// Proses update data
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    // Validasi username
    if(empty($username)) {
        $errors[] = "Username tidak boleh kosong";
    }

    // Jika ingin ganti password
    if(!empty($new_password)) {
        // Verifikasi password saat ini
        if(empty($current_password)) {
            $errors[] = "Password saat ini harus diisi untuk mengubah password";
        } elseif(!password_verify($current_password, $admin['password'])) {
            $errors[] = "Password saat ini salah!";
        } elseif($new_password !== $confirm_password) {
            $errors[] = "Password baru tidak cocok!";
        } elseif(strlen($new_password) < 6) {
            $errors[] = "Password baru minimal 6 karakter";
        }
    }

    if(empty($errors)) {
        // Update username
        $query = "UPDATE admin SET username = '$username'";
        
        // Update password jika diisi
        if(!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query .= ", password = '$hashed_password'";
        }
        
        $query .= " WHERE id_admin = '$admin_id'";
        
        if(mysqli_query($conn, $query)) {
            $message = "Data berhasil diperbarui!";
            $message_type = "success";
            
            // Refresh data admin
            $result_admin = mysqli_query($conn, $query_admin);
            $admin = mysqli_fetch_assoc($result_admin);
            
            // Update session username
            $_SESSION['username'] = $admin['username'];
        } else {
            $message = "Gagal memperbarui data: " . mysqli_error($conn);
            $message_type = "danger";
        }
    } else {
        $message = implode("<br>", $errors);
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Sistem Informasi Gunung Berapi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <nav class="navbar navbar-light bg-white border-bottom mt-3">
                    <div class="container-fluid">
                        <span class="navbar-text">
                            <i class="fas fa-user me-1"></i><?php echo $admin['username']; ?>
                        </span>
                    </div>
                </nav>

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Pengaturan Akun</h1>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-user-cog me-2"></i>Edit Profil Admin</h5>
                            </div>
                            <div class="card-body">
                                <?php if($message): ?>
                                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                        <?php echo $message; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="POST">
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-user me-2"></i>Informasi Akun</h6>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">ID Admin</label>
                                                    <input type="text" class="form-control" value="<?php echo $admin['id_admin']; ?>" readonly>
                                                    <div class="form-text">ID admin tidak dapat diubah</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="username" 
                                                           value="<?php echo $admin['username']; ?>" required>
                                                    <div class="form-text">Username untuk login ke sistem</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-lock me-2"></i>Ubah Password</h6>
                                        <hr>
                                        <div class="alert alert-info">
                                            <small><i class="fas fa-info-circle me-1"></i>Isi bagian ini hanya jika ingin mengubah password. Kosongkan jika tidak ingin mengubah.</small>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Password Saat Ini</label>
                                                    <input type="password" class="form-control" name="current_password">
                                                    <div class="form-text">Wajib diisi untuk verifikasi</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Password Baru</label>
                                                    <input type="password" class="form-control" name="new_password" minlength="6">
                                                    <div class="form-text">Minimal 6 karakter</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Konfirmasi Password Baru</label>
                                                    <input type="password" class="form-control" name="confirm_password">
                                                    <div class="form-text">Harus sama dengan password baru</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                                        </button>
                                        <a href="dashboard.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i>Kembali
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Info Akun -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Sistem</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Terakhir Login:</small>
                                        <p class="mb-2"><?php echo date('d F Y H:i:s'); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Status Akun:</small>
                                        <p class="mb-2"><span class="badge bg-success">Aktif</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validasi form
        document.querySelector('form').addEventListener('submit', function(e) {
            const newPassword = document.querySelector('input[name="new_password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            const currentPassword = document.querySelector('input[name="current_password"]').value;
            
            if (newPassword && newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Password baru dan konfirmasi password tidak cocok!');
                return false;
            }
            
            if (newPassword && !currentPassword) {
                e.preventDefault();
                alert('Harap masukkan password saat ini untuk mengubah password!');
                return false;
            }
        });
    </script>
</body>
</html>