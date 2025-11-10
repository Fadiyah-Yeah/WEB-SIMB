<?php
$current_page = basename($_SERVER['PHP_SELF']);
$admin_username = isset($admin['username']) ? htmlspecialchars($admin['username']) : 'Admin';
?>

<style>
    :root {
        --primary-color: #667eea; 
        --secondary-color: #764ba2; 
        --sidebar-width: 250px;
    }
    
    .sidebar-custom {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        padding-top: 20px;
        background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        box-shadow: 2px 0 5px rgba(0,0,0,0.15);
        color: white;
    }

    .sidebar-custom .sidebar-header {
        padding: 10px 20px 20px 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .sidebar-custom .nav-link {
        padding: 10px 20px;
        font-size: 1.05em;
        display: block;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        transition: all 0.2s;
        margin: 5px 0;
    }

    .sidebar-custom .nav-link:hover {
        color: var(--secondary-color);
        background: white;
        border-radius: 0 50px 50px 0;
        transform: translateX(5px);
    }

    .sidebar-custom .nav-link.active {
        color: var(--secondary-color);
        background: white;
        font-weight: bold;
        border-radius: 0 50px 50px 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    main {
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        padding: 20px;
    }
</style>
<nav class="col-md-3 col-lg-2 d-md-block sidebar-custom">
    <div class="position-sticky pt-3">
        <div class="sidebar-header">
            <i class="fas fa-mountain fa-3x mb-2"></i>
            <h5 class="text-white mb-0">Sistem Gunung Berapi</h5>
            <p class="small">Selamat datang, <strong><?php echo $admin_username; ?></strong></p>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'gunung.php' ? 'active' : ''; ?>" href="gunung.php">
                    <i class="fas fa-list me-2"></i>Data Gunung
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'erupsi.php' ? 'active' : ''; ?>" href="erupsi.php">
                    <i class="fas fa-fire me-2"></i>Data Erupsi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'mitigasi.php' ? 'active' : ''; ?>" href="mitigasi.php">
                    <i class="fas fa-shield-alt me-2"></i>Logistik Mitigasi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'pengaturan.php' ? 'active' : ''; ?>" href="pengaturan.php">
                    <i class="fas fa-cog me-2"></i>Pengaturan
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>