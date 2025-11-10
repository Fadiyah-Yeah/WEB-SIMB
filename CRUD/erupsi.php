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
        $id_gunung = $_POST['id_gunung'];
        $tanggal_erupsi = $_POST['tanggal_erupsi'];
        $tipe_erupsi = $_POST['tipe_erupsi'];
        $dampak = $_POST['dampak'];
        
        $query = "INSERT INTO erupsi (id_Gunung, Tanggal_Erupsi, Tipe_Erupsi, Dampak) 
                  VALUES ('$id_gunung', '$tanggal_erupsi', '$tipe_erupsi', '$dampak')";
        mysqli_query($conn, $query);
        
    } elseif(isset($_POST['edit'])) {
        $id_erupsi = $_POST['id_erupsi'];
        $id_gunung = $_POST['id_gunung'];
        $tanggal_erupsi = $_POST['tanggal_erupsi'];
        $tipe_erupsi = $_POST['tipe_erupsi'];
        $dampak = $_POST['dampak'];
        
        $query = "UPDATE erupsi SET 
                  id_Gunung = '$id_gunung',
                  Tanggal_Erupsi = '$tanggal_erupsi',
                  Tipe_Erupsi = '$tipe_erupsi',
                  Dampak = '$dampak'
                  WHERE id_Erupsi = '$id_erupsi'";
        mysqli_query($conn, $query);
    }
    
    header("location: erupsi.php");
    exit();
}

// Proses Hapus
if(isset($_GET['hapus'])) {
    $id_erupsi = $_GET['hapus'];
    $query = "DELETE FROM erupsi WHERE id_Erupsi = '$id_erupsi'";
    mysqli_query($conn, $query);
    header("location: erupsi.php");
    exit();
}

// Ambil data gunung untuk dropdown
$query_gunung = "SELECT * FROM gunung";
$result_gunung = mysqli_query($conn, $query_gunung);

// Ambil data erupsi dengan join gunung
$query_erupsi = "SELECT e.*, g.nama_gunung 
                 FROM erupsi e 
                 LEFT JOIN gunung g ON e.id_Gunung = g.id_gunung 
                 ORDER BY e.Tanggal_Erupsi DESC";
$result_erupsi = mysqli_query($conn, $query_erupsi);

// Ambil data untuk edit
$edit_data = [];
if(isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $query_edit = "SELECT * FROM erupsi WHERE id_Erupsi = '$id_edit'";
    $result_edit = mysqli_query($conn, $query_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Erupsi - Sistem Informasi Gunung Berapi</title>
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
                    <h1 class="h2">Data Erupsi Gunung Berapi</h1>
                </div>

                <!-- Form Tambah/Edit -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo isset($edit_data) ? 'Edit Data Erupsi' : 'Tambah Data Erupsi'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if(isset($edit_data)): ?>
                                <input type="hidden" name="id_erupsi" value="<?php echo $edit_data['id_Erupsi']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gunung</label>
                                        <select class="form-control" name="id_gunung" required>
                                            <option value="">Pilih Gunung</option>
                                            <?php while($gunung = mysqli_fetch_assoc($result_gunung)): ?>
                                                <option value="<?php echo $gunung['id_gunung']; ?>"
                                                    <?php echo ($edit_data['id_Gunung'] ?? '') == $gunung['id_gunung'] ? 'selected' : ''; ?>>
                                                    <?php echo $gunung['nama_gunung']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Erupsi</label>
                                        <input type="datetime-local" class="form-control" name="tanggal_erupsi" 
                                               value="<?php echo isset($edit_data['Tanggal_Erupsi']) ? date('Y-m-d\TH:i', strtotime($edit_data['Tanggal_Erupsi'])) : ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tipe Erupsi</label>
                                        <input type="text" class="form-control" name="tipe_erupsi" 
                                               value="<?php echo $edit_data['Tipe_Erupsi'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Dampak</label>
                                        <textarea class="form-control" name="dampak" rows="1" required><?php echo $edit_data['Dampak'] ?? ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <?php if(isset($edit_data)): ?>
                                    <button type="submit" name="edit" class="btn btn-warning">Update Data</button>
                                    <a href="erupsi.php" class="btn btn-secondary">Batal</a>
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
                        <h5 class="mb-0">Daftar Erupsi</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Gunung</th>
                                        <th>Tanggal Erupsi</th>
                                        <th>Tipe Erupsi</th>
                                        <th>Dampak</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result_erupsi)): ?>
                                    <tr>
                                        <td><?php echo $row['id_Erupsi']; ?></td>
                                        <td><?php echo $row['nama_gunung']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['Tanggal_Erupsi'])); ?></td>
                                        <td><?php echo $row['Tipe_Erupsi']; ?></td>
                                        <td><?php echo $row['Dampak']; ?></td>
                                        <td>
                                            <a href="erupsi.php?edit=<?php echo $row['id_Erupsi']; ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="erupsi.php?hapus=<?php echo $row['id_Erupsi']; ?>" class="btn btn-danger btn-sm" 
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