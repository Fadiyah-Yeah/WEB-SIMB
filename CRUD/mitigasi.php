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
        $nama_pos = $_POST['nama_pos'];
        $tipe_lokasi = $_POST['tipe_lokasi'];
        $koordinat = $_POST['koordinat'];
        $kapasitas = $_POST['kapasitas'];
        $fasilitas = $_POST['fasilitas'];
        
        $query = "INSERT INTO logistik_mitigasi (id_Gunung, Nama_Pos, Tipe_Lokasi, Koordinat, Kapasitas, Fasilitas) 
                  VALUES ('$id_gunung', '$nama_pos', '$tipe_lokasi', '$koordinat', '$kapasitas', '$fasilitas')";
        mysqli_query($conn, $query);
        
    } elseif(isset($_POST['edit'])) {
        $id_mitigasi = $_POST['id_mitigasi'];
        $id_gunung = $_POST['id_gunung'];
        $nama_pos = $_POST['nama_pos'];
        $tipe_lokasi = $_POST['tipe_lokasi'];
        $koordinat = $_POST['koordinat'];
        $kapasitas = $_POST['kapasitas'];
        $fasilitas = $_POST['fasilitas'];
        
        $query = "UPDATE logistik_mitigasi SET 
                  id_Gunung = '$id_gunung',
                  Nama_Pos = '$nama_pos',
                  Tipe_Lokasi = '$tipe_lokasi',
                  Koordinat = '$koordinat',
                  Kapasitas = '$kapasitas',
                  Fasilitas = '$fasilitas'
                  WHERE id_Mitigasi = '$id_mitigasi'";
        mysqli_query($conn, $query);
    }
    
    header("location: mitigasi.php");
    exit();
}

// Proses Hapus
if(isset($_GET['hapus'])) {
    $id_mitigasi = $_GET['hapus'];
    $query = "DELETE FROM logistik_mitigasi WHERE id_Mitigasi = '$id_mitigasi'";
    mysqli_query($conn, $query);
    header("location: mitigasi.php");
    exit();
}

// Ambil data gunung untuk dropdown
$query_gunung = "SELECT * FROM gunung";
$result_gunung = mysqli_query($conn, $query_gunung);

// Ambil data mitigasi dengan join gunung
$query_mitigasi = "SELECT m.*, g.nama_gunung 
                   FROM logistik_mitigasi m 
                   LEFT JOIN gunung g ON m.id_Gunung = g.id_gunung 
                   ORDER BY m.id_Mitigasi DESC";
$result_mitigasi = mysqli_query($conn, $query_mitigasi);

// Ambil data untuk edit
$edit_data = [];
if(isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $query_edit = "SELECT * FROM logistik_mitigasi WHERE id_Mitigasi = '$id_edit'";
    $result_edit = mysqli_query($conn, $query_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistik Mitigasi - Sistem Informasi Gunung Berapi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .bg-orange { background-color: #fd7e14 !important; }
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
                    <h1 class="h2">Logistik dan Mitigasi</h1>
                </div>

                <!-- Form Tambah/Edit -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo isset($edit_data) ? 'Edit Data Mitigasi' : 'Tambah Data Mitigasi'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if(isset($edit_data)): ?>
                                <input type="hidden" name="id_mitigasi" value="<?php echo $edit_data['id_Mitigasi']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gunung</label>
                                        <select class="form-control" name="id_gunung" required>
                                            <option value="">Pilih Gunung</option>
                                            <?php 
                                            // Reset pointer result gunung
                                            mysqli_data_seek($result_gunung, 0);
                                            while($gunung = mysqli_fetch_assoc($result_gunung)): 
                                            ?>
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
                                        <label class="form-label">Nama Pos</label>
                                        <input type="text" class="form-control" name="nama_pos" 
                                               value="<?php echo $edit_data['Nama_Pos'] ?? ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Tipe Lokasi</label>
                                        <select class="form-control" name="tipe_lokasi" required>
                                            <option value="Pos Pengamatan" <?php echo ($edit_data['Tipe_Lokasi'] ?? '') == 'Pos Pengamatan' ? 'selected' : ''; ?>>Pos Pengamatan</option>
                                            <option value="Shelter" <?php echo ($edit_data['Tipe_Lokasi'] ?? '') == 'Shelter' ? 'selected' : ''; ?>>Shelter</option>
                                            <option value="Gudang Logistik" <?php echo ($edit_data['Tipe_Lokasi'] ?? '') == 'Gudang Logistik' ? 'selected' : ''; ?>>Gudang Logistik</option>
                                            <option value="Pos Kesehatan" <?php echo ($edit_data['Tipe_Lokasi'] ?? '') == 'Pos Kesehatan' ? 'selected' : ''; ?>>Pos Kesehatan</option>
                                            <option value="Lainnya" <?php echo ($edit_data['Tipe_Lokasi'] ?? '') == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Koordinat</label>
                                        <input type="text" class="form-control" name="koordinat" 
                                               value="<?php echo $edit_data['Koordinat'] ?? ''; ?>" placeholder="Contoh: -7.540,110.446" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Kapasitas (orang)</label>
                                        <input type="number" class="form-control" name="kapasitas" 
                                               value="<?php echo $edit_data['Kapasitas'] ?? ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Fasilitas</label>
                                <textarea class="form-control" name="fasilitas" rows="3" required><?php echo $edit_data['Fasilitas'] ?? ''; ?></textarea>
                                <div class="form-text">Sebutkan fasilitas yang tersedia (pisahkan dengan koma)</div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <?php if(isset($edit_data)): ?>
                                    <button type="submit" name="edit" class="btn btn-warning">Update Data</button>
                                    <a href="mitigasi.php" class="btn btn-secondary">Batal</a>
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
                        <h5 class="mb-0">Daftar Logistik dan Mitigasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Gunung</th>
                                        <th>Nama Pos</th>
                                        <th>Tipe Lokasi</th>
                                        <th>Koordinat</th>
                                        <th>Kapasitas</th>
                                        <th>Fasilitas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Reset pointer result mitigasi
                                    mysqli_data_seek($result_mitigasi, 0);
                                    while($row = mysqli_fetch_assoc($result_mitigasi)): 
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id_Mitigasi']; ?></td>
                                        <td><?php echo $row['nama_gunung']; ?></td>
                                        <td><?php echo $row['Nama_Pos']; ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $row['Tipe_Lokasi']; ?></span>
                                        </td>
                                        <td><?php echo $row['Koordinat']; ?></td>
                                        <td><?php echo number_format($row['Kapasitas']); ?> orang</td>
                                        <td>
                                            <small><?php echo $row['Fasilitas']; ?></small>
                                        </td>
                                        <td>
                                            <a href="mitigasi.php?edit=<?php echo $row['id_Mitigasi']; ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="mitigasi.php?hapus=<?php echo $row['id_Mitigasi']; ?>" class="btn btn-danger btn-sm" 
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
    <script>
        // Validasi form
        document.querySelector('form').addEventListener('submit', function(e) {
            const kapasitas = document.querySelector('input[name="kapasitas"]').value;
            const koordinat = document.querySelector('input[name="koordinat"]').value;
            
            // Validasi kapasitas
            if (kapasitas <= 0) {
                e.preventDefault();
                alert('Kapasitas harus lebih dari 0!');
                return false;
            }
            
            // Validasi format koordinat sederhana
            if (!koordinat.match(/^-?\d+\.?\d*,\s*-?\d+\.?\d*$/)) {
                e.preventDefault();
                alert('Format koordinat tidak valid! Gunakan format: latitude,longitude (contoh: -7.540,110.446)');
                return false;
            }
        });

        // Auto-capitalize untuk nama pos
        document.querySelector('input[name="nama_pos"]').addEventListener('input', function(e) {
            this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
        });
    </script>
</body>
</html>