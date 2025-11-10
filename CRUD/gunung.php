<?php
session_start();
include "konek.php";

// if(!isset($_SESSION['admin_id'])) {
//     header("location: login.php");
//     exit();
// }

$admin_id = $_SESSION['admin_id'];
$query_admin = "SELECT * FROM admin WHERE id_admin = '$admin_id'";
$result_admin = mysqli_query($conn, $query_admin);
$admin = mysqli_fetch_assoc($result_admin);

// Proses CRUD
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['tambah'])) {
        $nama_gunung = $_POST['nama_gunung'];
        $lokasi = $_POST['lokasi'];
        $ketinggian = $_POST['ketinggian'];
        $status = $_POST['status'];
        $tingkat_aktivitas = $_POST['tingkat_aktivitas'];
        
        $query = "INSERT INTO gunung (nama_gunung, lokasi, ketinggian, status, tingkat_aktivitas) 
                  VALUES ('$nama_gunung', '$lokasi', '$ketinggian', '$status', '$tingkat_aktivitas')";
        mysqli_query($conn, $query);
        
    } elseif(isset($_POST['edit'])) {
        $id_gunung = $_POST['id_gunung'];
        $nama_gunung = $_POST['nama_gunung'];
        $lokasi = $_POST['lokasi'];
        $ketinggian = $_POST['ketinggian'];
        $status = $_POST['status'];
        $tingkat_aktivitas = $_POST['tingkat_aktivitas'];
        
        $query = "UPDATE gunung SET 
                  nama_gunung = '$nama_gunung',
                  lokasi = '$lokasi',
                  ketinggian = '$ketinggian',
                  status = '$status',
                  tingkat_aktivitas = '$tingkat_aktivitas'
                  WHERE id_gunung = '$id_gunung'";
        mysqli_query($conn, $query);
    }
    
    header("location: gunung.php");
    exit();
}

// Proses Hapus
if(isset($_GET['hapus'])) {
    $id_gunung = $_GET['hapus'];
    $query = "DELETE FROM gunung WHERE id_gunung = '$id_gunung'";
    mysqli_query($conn, $query);
    header("location: gunung.php");
    exit();
}

// Ambil data gunung
$query_gunung = "SELECT * FROM gunung ORDER BY id_gunung DESC";
$result_gunung = mysqli_query($conn, $query_gunung);

// Ambil data untuk edit
$edit_data = [];
if(isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $query_edit = "SELECT * FROM gunung WHERE id_gunung = '$id_edit'";
    $result_edit = mysqli_query($conn, $query_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Gunung - Sistem Informasi Gunung Berapi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <h1 class="h2">Data Gunung Berapi</h1>
                </div>

                <!-- Form Tambah/Edit -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo isset($edit_data) ? 'Edit Data Gunung' : 'Tambah Data Gunung'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if(isset($edit_data)): ?>
                                <input type="hidden" name="id_gunung" value="<?php echo $edit_data['id_gunung']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Gunung</label>
                                        <input type="text" class="form-control" name="nama_gunung" 
                                               value="<?php echo $edit_data['nama_gunung'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Lokasi</label>
                                        <input type="text" class="form-control" name="lokasi" 
                                               value="<?php echo $edit_data['lokasi'] ?? ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Ketinggian (meter)</label>
                                        <input type="number" class="form-control" name="ketinggian" 
                                               value="<?php echo $edit_data['ketinggian'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-control" name="status" required>
                                            <option value="Normal" <?php echo ($edit_data['status'] ?? '') == 'Normal' ? 'selected' : ''; ?>>Normal</option>
                                            <option value="Waspada" <?php echo ($edit_data['status'] ?? '') == 'Waspada' ? 'selected' : ''; ?>>Waspada</option>
                                            <option value="Siaga" <?php echo ($edit_data['status'] ?? '') == 'Siaga' ? 'selected' : ''; ?>>Siaga</option>
                                            <option value="Awas" <?php echo ($edit_data['status'] ?? '') == 'Awas' ? 'selected' : ''; ?>>Awas</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Tingkat Aktivitas</label>
                                        <input type="text" class="form-control" name="tingkat_aktivitas" 
                                               value="<?php echo $edit_data['tingkat_aktivitas'] ?? ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <?php if(isset($edit_data)): ?>
                                    <button type="submit" name="edit" class="btn btn-warning">Update Data</button>
                                    <a href="gunung.php" class="btn btn-secondary">Batal</a>
                                <?php else: ?>
                                    <button type="submit" name="tambah" class="btn btn-primary">Tambah Data</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Daftar Gunung Berapi</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Gunung</th>
                                        <th>Lokasi</th>
                                        <th>Ketinggian</th>
                                        <th>Status</th>
                                        <th>Tingkat Aktivitas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result_gunung)): ?>
                                    <tr>
                                        <td><?php echo $row['id_gunung']; ?></td>
                                        <td><?php echo $row['nama_gunung']; ?></td>
                                        <td><?php echo $row['lokasi']; ?></td>
                                        <td><?php echo number_format($row['ketinggian']); ?> m</td>
                                        <td>
                                            <span class="badge 
                                                <?php 
                                                switch($row['status']) {
                                                    case 'Normal': echo 'bg-success'; break;
                                                    case 'Waspada': echo 'bg-warning'; break;
                                                    case 'Siaga': echo 'bg-orange'; break;
                                                    case 'Awas': echo 'bg-danger'; break;
                                                    default: echo 'bg-secondary';
                                                }
                                                ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $row['tingkat_aktivitas']; ?></td>
                                        <td>
                                            <a href="gunung.php?edit=<?php echo $row['id_gunung']; ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="gunung.php?hapus=<?php echo $row['id_gunung']; ?>" class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Yakin hapus data?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>