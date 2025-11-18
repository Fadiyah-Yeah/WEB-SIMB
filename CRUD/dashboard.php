<?php
session_start();
include "konek.php";

if(!isset($_SESSION['admin_id'])) {
    header("location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$query_admin = "SELECT * FROM admin WHERE id_admin = '$admin_id'";
$result_admin = mysqli_query($conn, $query_admin);
$admin = mysqli_fetch_assoc($result_admin);

// Hitung total data
$total_gunung = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM gunung"));
$total_erupsi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM erupsi"));
$total_mitigasi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM logistik_mitigasi"));
$gunung_waspada = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM gunung WHERE status IN ('Waspada','Siaga','Awas')"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Informasi Gunung Berapi</title>
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
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mt-3">
                    <div class="container-fluid">
                        <div class="navbar-nav">
                            <span class="navbar-text">
                                <i class="fas fa-user me-1"></i><?php echo $admin['username']; ?>
                            </span>
                        </div>
                        <span class="navbar-text">
                            <?php echo date('d F Y H:i:s'); ?>
                        </span>
                    </div>
                </nav>

                <!-- Content -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard Sistem Gunung Berapi</h1>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $total_gunung; ?></h4>
                                        <p>Total Gunung</p>
                                    </div>
                                    <i class="fas fa-mountain fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $total_erupsi; ?></h4>
                                        <p>Data Erupsi</p>
                                    </div>
                                    <i class="fas fa-fire fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $gunung_waspada; ?></h4>
                                        <p>Gunung Waspada</p>
                                    </div>
                                    <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $total_mitigasi; ?></h4>
                                        <p>Pos Mitigasi</p>
                                    </div>
                                    <i class="fas fa-shield-alt fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-mountain fa-3x text-primary mb-3"></i>
                                <h5>Kelola Gunung</h5>
                                <p>Tambahkan atau edit data gunung berapi</p>
                                <a href="gunung.php" class="btn btn-primary">Kelola</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-fire fa-3x text-danger mb-3"></i>
                                <h5>Data Erupsi</h5>
                                <p>Catat dan kelola data erupsi gunung</p>
                                <a href="erupsi.php" class="btn btn-danger">Kelola</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                                <h5>Logistik Mitigasi</h5>
                                <p>Kelola pos dan logistik mitigasi</p>
                                <a href="mitigasi.php" class="btn btn-success">Kelola</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>