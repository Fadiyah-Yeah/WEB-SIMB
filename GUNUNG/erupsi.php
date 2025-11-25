<?php
function getEruptionData() {
    $host = 'localhost';
    $dbname = 'gunung_berapi';
    $username = 'root'; 
    $password = ''; 

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "
            SELECT 
                e.id_Erupsi,
                g.nama_gunung as volcano_name,
                g.lokasi as location,
                e.Tanggal_Erupsi as eruption_date,
                e.Tipe_Erupsi as eruption_type,
                e.Dampak as impact,
                DATE_FORMAT(e.Tanggal_Erupsi, '%d %M %Y') as formatted_date,
                DATE_FORMAT(e.Tanggal_Erupsi, '%H:%i') as eruption_time
            FROM erupsi e
            JOIN gunung g ON e.id_Gunung = g.id_gunung
            ORDER BY e.Tanggal_Erupsi DESC
        ";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        error_log("Koneksi database gagal: " . $e->getMessage());
        return [];
    }
}

function getEruptionTypeClass($type) {
    switch($type) {
        case 'Letusan Magmatik':
            return 'eruption-type-magmatic';
        case 'Letusan Freatik':
            return 'eruption-type-phreatic';
        case 'Letusan Eksplosif':
            return 'eruption-type-explosive';
        case 'Letusan Strombolian':
            return 'eruption-type-strombolian';
        case 'Letusan Efusif':
            return 'eruption-type-effusive';
        default:
            return 'eruption-type-unknown';
    }
}

function getEruptionTypeIcon($type) {
    switch($type) {
        case 'Letusan Magmatik':
            return '🔥';
        case 'Letusan Freatik':
            return '💧';
        case 'Letusan Eksplosif':
            return '💥';
        case 'Letusan Strombolian':
            return '🌋';
        case 'Letusan Efusif':
            return '🌊';
        default:
            return '❓';
    }
}

function formatImpact($impact) {
    if (strlen($impact) > 100) {
        return substr($impact, 0, 100) . '...';
    }
    return $impact;
}

$eruptionData = getEruptionData();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Erupsi - MagmaCare</title>
    <style>
        /* Reset dan variabel CSS */
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --primary: 0 72% 56%;  
            --primary-dark: 0 72% 45%;
            --primary-light: 0 72% 65%;
            --primary-foreground: 0 0% 100%;
            --muted-foreground: 215.4 16.3% 46.9%;
            --border: 214.3 31.8% 91.4%;
            --card-bg: 0 0% 100%;
            --nav-bg: rgba(255, 255, 255, 0.95);
            --footer-bg: 210 12% 96%;
        }

        /* Night Mode Variables */
        [data-theme="night"] {
            --background: 222.2 84% 4.9%;
            --foreground: 0 0% 98%;
            --primary: 0 72% 56%;
            --primary-dark: 0 72% 45%;
            --primary-light: 0 72% 65%;
            --primary-foreground: 0 0% 100%;
            --muted-foreground: 215.4 16.3% 56.9%;
            --border: 214.3 31.8% 21.4%;
            --card-bg: 222.2 84% 8%;
            --nav-bg: rgba(15, 23, 42, 0.95);
            --footer-bg: 222.2 84% 6%;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        body {
            color: hsl(var(--foreground));
            background-color: hsl(var(--background));
            line-height: 1.5;
            padding-top: 80px;
        }

        /* Theme Toggle */
        .theme-toggle-container {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .theme-label {
            font-size: 0.8rem;
            color: hsl(var(--muted-foreground));
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .theme-toggle {
            position: relative;
            width: 60px;
            height: 30px;
            background: hsl(var(--border));
            border-radius: 25px;
            cursor: pointer;
            border: 2px solid hsl(var(--border));
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .theme-toggle::before {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 22px;
            height: 22px;
            background: hsl(var(--primary));
            border-radius: 50%;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .theme-toggle::after {
            content: '☀️';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 8px;
            font-size: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
            opacity: 0.7;
        }

        [data-theme="night"] .theme-toggle {
            background: hsl(var(--border));
            border-color: hsl(var(--primary) / 0.5);
        }

        [data-theme="night"] .theme-toggle::before {
            transform: translateX(30px);
            background: hsl(var(--primary));
        }

        [data-theme="night"] .theme-toggle::after {
            content: '🌙';
            left: 8px;
            right: auto;
            opacity: 1;
        }

        /* Navbar Styles */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            transition: all 0.3s ease;
            background: transparent;
            backdrop-filter: blur(8px);
        }

        nav.scrolled {
            background: var(--nav-bg);
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid hsl(var(--border));
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
        }

        /* Logo Styles */
        .logo-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
        }

        .logo-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, hsl(var(--primary) / 0.1), transparent);
            transition: left 0.5s ease;
        }

        .logo-btn:hover::before {
            left: 100%;
        }

        .logo-btn:hover {
            transform: translateY(-2px);
            background: hsl(var(--primary) / 0.05);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background-color: hsl(var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .logo-icon::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .logo-text {
            display: none;
        }

        @media (min-width: 640px) {
            .logo-text {
                display: block;
            }
        }

        .logo-title {
            font-weight: bold;
            font-size: 1.125rem;
            color: hsl(var(--foreground));
            transition: color 0.3s ease;
            margin: 0;
        }

        .logo-subtitle {
            font-size: 0.75rem;
            color: hsl(var(--muted-foreground));
            transition: color 0.3s ease;
            margin: 0;
        }

        /* Navigation Links */
        .nav-links {
            display: none;
            align-items: center;
            gap: 0.25rem;
        }

        @media (min-width: 768px) {
            .nav-links {
                display: flex;
            }
        }

        .nav-btn {
            background: none;
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            cursor: pointer;
            color: hsl(var(--foreground));
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-block;
        }

        .nav-btn::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: hsl(var(--primary));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-btn:hover {
            color: hsl(var(--primary));
            background: hsl(var(--primary) / 0.05);
            transform: translateY(-2px);
        }

        .nav-btn:hover::before {
            width: 80%;
        }

        /* CTA Button */
        .cta-btn {
            display: none;
            background: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px hsl(var(--primary) / 0.3);
        }

        @media (min-width: 640px) {
            .cta-btn {
                display: inline-block;
            }
        }

        /* Eruption Cards Section */
        .eruption-cards {
            padding: 4rem 0;
            background-color: hsl(var(--background));
            transition: background-color 0.3s ease;
            min-height: calc(100vh - 160px);
        }

        .eruption-cards h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: hsl(var(--foreground));
        }

        .text-primary {
            color: hsl(var(--primary));
        }

        .section-desc {
            text-align: center;
            color: hsl(var(--muted-foreground));
            margin-bottom: 3rem;
            font-size: 1.1rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        @media (max-width: 768px) {
            .cards-grid {
                grid-template-columns: 1fr;
                padding: 0.5rem;
            }
        }

        .eruption-card {
            background: hsl(var(--card-bg));
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            transition: all 0.3s ease;
            border: 1px solid hsl(var(--border));
            position: relative;
            overflow: hidden;
        }

        .eruption-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .eruption-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .volcano-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: hsl(var(--foreground));
            margin: 0;
        }

        .eruption-type-badge {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .eruption-type-magmatic {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
        }

        .eruption-type-phreatic {
            background: linear-gradient(135deg, #0369a1, #0ea5e9);
            color: white;
        }

        .eruption-type-explosive {
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: white;
        }

        .eruption-type-strombolian {
            background: linear-gradient(135deg, #854d0e, #ca8a04);
            color: white;
        }

        .eruption-type-effusive {
            background: linear-gradient(135deg, #15803d, #22c55e);
            color: white;
        }

        .eruption-type-unknown {
            background: hsl(var(--muted-foreground));
            color: white;
        }

        .location {
            color: hsl(var(--muted-foreground));
            font-size: 0.95rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .eruption-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .detail-label {
            color: hsl(var(--muted-foreground));
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            color: hsl(var(--foreground));
            font-size: 1rem;
            font-weight: 600;
        }

        .impact-section {
            border-top: 1px solid hsl(var(--border));
            padding-top: 1rem;
            margin-top: 0.5rem;
        }

        .impact-label {
            color: hsl(var(--muted-foreground));
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .impact-text {
            color: hsl(var(--foreground));
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: hsl(var(--muted-foreground));
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: hsl(var(--primary) / 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .empty-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: hsl(var(--foreground));
        }

        .empty-desc {
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .empty-note {
            background: hsl(var(--primary) / 0.05);
            padding: 1rem;
            border-radius: 0.5rem;
            border-left: 4px solid hsl(var(--primary));
        }

        .empty-note p {
            margin: 0;
            font-size: 0.9rem;
        }

        /* Footer styles */
        footer {
            background-color: hsl(var(--footer-bg));
            padding: 4rem 0 2rem;
            color: hsl(var(--foreground));
            border-top: 1px solid hsl(var(--border));
            transition: all 0.3s ease;
            position: relative;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
            align-items: start;
            padding: 0 1rem 2rem;
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .footer-grid {
                grid-template-columns: 2fr 1fr 1fr 1.5fr;
                gap: 3rem;
            }
        }

        .brand-section {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-logo .logo-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background-color: hsl(var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-text h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: hsl(var(--foreground));
            margin: 0 0 0.25rem 0;
        }

        .brand-text p {
            font-size: 0.875rem;
            color: hsl(var(--muted-foreground));
            margin: 0;
        }

        .brand-description {
            color: hsl(var(--muted-foreground));
            line-height: 1.6;
            font-size: 0.95rem;
            margin: 0;
        }

        .footer-section h4 {
            font-size: 1.125rem;
            font-weight: 600;
            color: hsl(var(--foreground));
            margin-bottom: 1.25rem;
            position: relative;
        }

        .footer-section h4::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 0;
            width: 40px;
            height: 2px;
            background: hsl(var(--primary));
            border-radius: 1px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .footer-links a,
        .footer-links li {
            color: hsl(var(--muted-foreground));
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .footer-links a:hover {
            color: hsl(var(--primary));
            transform: translateX(5px);
        }

        .footer-bottom {
            padding-top: 2rem;
            border-top: 1px solid hsl(var(--border));
            margin-top: 2rem;
            text-align: center;
            max-width: 1200px;
            margin: 2rem auto 0;
            padding: 2rem 1rem 0;
        }

        .footer-bottom p {
            color: hsl(var(--muted-foreground));
            font-size: 0.9rem;
            margin: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .theme-toggle-container {
                top: 1rem;
                right: 1rem;
            }
            
            .theme-label {
                display: none;
            }
            
            .nav-content {
                flex-wrap: wrap;
                height: auto;
                padding: 1rem 0;
            }
            
            .nav-links {
                order: 3;
                width: 100%;
                justify-content: center;
                margin-top: 1rem;
                gap: 0.5rem;
            }
            
            .nav-btn {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }
            
            .eruption-cards h2 {
                font-size: 2rem;
            }
            
            .eruption-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Theme Toggle -->
    <div class="theme-toggle-container">
        <span class="theme-label"></span>
        <div class="theme-toggle" id="themeToggle"></div>
    </div>

    <!-- Navigation -->
    <nav id="navbar">
        <div class="container">
            <div class="nav-content">
                <a class="logo-btn" href="../index.html">
                    <div class="logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m8 3 4 8 5-5 5 15H2L8 3z"></path>
                        </svg>
                    </div>
                    <div class="logo-text">
                        <h1 class="logo-title">MagmaCare</h1>
                        <p class="logo-subtitle">Sistem Informasi Gunung</p>
                    </div>
                </a>

                <!-- Navigation Links -->
                <div class="nav-links">
                    <a class="nav-btn" href="../index.html">Beranda</a>
                    <a class="nav-btn" href="gunung.html">Informasi Gunung</a>
                    <a class="nav-btn" href="../pengertian.html">Pengertian Gunung & Jenis</a>
                    <a class="nav-btn" href="../MITIGASI/mitigasi.html">Mitigasi</a>
                    <a class="nav-btn" href="../dampak.html">Dampak</a>
                </div>

                <!-- Login Button -->
                <a class="" href=""></a>
            </div>
        </div>
    </nav>

    <!-- Data Erupsi -->
    <section class="eruption-cards">
        <div class="container">
            <h2>Data <span class="text-primary">Erupsi Gunung</span></h2>
            <p class="section-desc">Riwayat dan informasi terkini tentang aktivitas erupsi gunung berapi di Indonesia</p>

            <div class="cards-grid">
                <?php foreach ($eruptionData as $eruption): ?>
                    <?php
                    $typeClass = getEruptionTypeClass($eruption['eruption_type']);
                    $typeIcon = getEruptionTypeIcon($eruption['eruption_type']);
                    $formattedImpact = formatImpact($eruption['impact']);
                    ?>
                    
                    <div class="eruption-card">
                        <div class="eruption-header">
                            <h3 class="volcano-name"><?php echo htmlspecialchars($eruption['volcano_name']); ?></h3>
                            <span class="eruption-type-badge <?php echo $typeClass; ?>">
                                <?php echo $typeIcon; ?>
                                <?php echo htmlspecialchars($eruption['eruption_type']); ?>
                            </span>
                        </div>

                        <div class="location">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <?php echo htmlspecialchars($eruption['location']); ?>
                        </div>

                        <div class="eruption-details">
                            <div class="detail-item">
                                <span class="detail-label">Tanggal Erupsi</span>
                                <span class="detail-value"><?php echo htmlspecialchars($eruption['formatted_date']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Waktu</span>
                                <span class="detail-value"><?php echo htmlspecialchars($eruption['eruption_time']); ?> WIB</span>
                            </div>
                        </div>

                        <div class="impact-section">
                            <div class="impact-label">Dampak Erupsi:</div>
                            <div class="impact-text"><?php echo htmlspecialchars($formattedImpact); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($eruptionData)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        🌋
                    </div>
                    <h3 class="empty-title">Tidak ada data erupsi</h3>
                    <p class="empty-desc">Data erupsi gunung berapi tidak ditemukan dalam database.</p>
                    <div class="empty-note">
                        <p><strong>Note:</strong> Database erupsi terdeteksi kosong. Silakan tambahkan data erupsi terlebih dahulu.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-grid">
            <div class="brand-section">
                <div class="brand-logo">
                    <div class="logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m8 3 4 8 5-5 5 15H2L8 3z"></path>
                        </svg>
                    </div>
                    <div class="brand-text">
                        <h3>MagmaCare</h3>
                        <p>Sistem Informasi Gunung</p>
                    </div>
                </div>
                <p class="brand-description">
                    Sistem pemantauan gunung berapi terintegrasi untuk keselamatan masyarakat 
                    dan mitigasi bencana vulkanik di Indonesia.
                </p>
            </div>

             <div class="footer-section">
                    <h4>Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="gunung.html">Informasi Gunung</a></li>
                        <li><a href="../dampak.html">Dampak Erupsi</a></li>
                        <li><a href="../MITIGASI/mitigasi.html">Mitigasi Bencana</a></li>
                        <li><a href="../pengertian.html">Pengertian & Jenis</a></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div class="footer-section">
                    <h4>Sumber Daya</h4>
                    <ul class="footer-links">
                        <li><a href="../MITIGASI/mitigasi.html">Panduan Evakuasi</a></li>
                        <li><a href="../MITIGASI/mitigasi.html">Edukasi Bencana</a></li>
                        <li><a href="gunung.html">Data & Statistik</a></li>
                        <li><a href=""></a></li>
                    </ul>
                </div>

                <!-- Contact -->
               <div class="footer-section">
                    <h4>Sumber Informasi Resmi</h4>
                    <ul class="footer-links">
                        <!-- <li>Email: info@magmacare.id</li> -->
                        <li><a href="https://www.bnpb.go.id/">BNPB</a></li>
                        <li><a href="https://www.bmkg.go.id/">BMKG</a></li>
                        <li><a href="https://magma.esdm.go.id/">PVMBG</a></li>
                    </ul>
                </div>
            </div>

        <div class="footer-bottom">
            <p>&copy; 2025 MagmaCare - Sistem Informasi Gunung Berapi. All rights reserved.</p>
        </div>
    </footer>

    <script>
        
        const themeToggle = document.getElementById('themeToggle');
        const currentTheme = localStorage.getItem('theme') || 'light';
        
        document.documentElement.setAttribute('data-theme', currentTheme);

        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'night' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });

       
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>