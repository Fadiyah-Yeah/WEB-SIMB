<?php
function getVolcanoData() {
    $host = 'localhost';
    $dbname = 'gunung_berapi';
    $username = 'root'; 
    $password = ''; 

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "
            SELECT 
                g.id_gunung,
                g.nama_gunung as name,
                g.lokasi as location,
                CONCAT(FORMAT(g.ketinggian, 0), ' m') as height,
                g.status,
                g.tingkat_aktivitas as activity,
                MAX(e.Tanggal_Erupsi) as last_eruption,
                CASE 
                    WHEN g.status = 'Awas' THEN 'Level IV'
                    WHEN g.status = 'Siaga' THEN 'Level III'
                    WHEN g.status = 'Waspada' THEN 'Level II'
                    WHEN g.status = 'Normal' THEN 'Level I'
                    ELSE 'Tidak Diketahui'
                END as level
            FROM gunung g
            LEFT JOIN erupsi e ON g.id_gunung = e.id_Gunung
            GROUP BY g.id_gunung, g.nama_gunung, g.lokasi, g.ketinggian, g.status, g.tingkat_aktivitas
            ORDER BY 
                CASE 
                    WHEN g.status = 'Awas' THEN 1
                    WHEN g.status = 'Siaga' THEN 2
                    WHEN g.status = 'Waspada' THEN 3
                    WHEN g.status = 'Normal' THEN 4
                    ELSE 5
                END, 
                g.nama_gunung ASC
        ";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        error_log("Koneksi database gagal: " . $e->getMessage());
        return [];
    }
}

function getStatusClass($level) {
    switch($level) {
        case 'Level IV':
            return 'status-level-4';
        case 'Level III':
            return 'status-level-3';
        case 'Level II':
            return 'status-level-2';
        case 'Level I':
            return 'status-level-1';
        default:
            return 'status-unknown';
    }
}

function getStatus($level) {
    switch($level) {
        case 'Level IV':
            return 'Awas';
        case 'Level III':
            return 'Siaga';
        case 'Level II':
            return 'Waspada';
        case 'Level I':
            return 'Normal';
        default:
            return 'Tidak Diketahui';
    }
}

function getActivity($level) {
    switch($level) {
        case 'Level IV':
            return 'Sangat Tinggi';
        case 'Level III':
            return 'Tinggi';
        case 'Level II':
            return 'Sedang';
        case 'Level I':
            return 'Rendah';
        default:
            return 'Tidak Diketahui';
    }
}

function formatEruptionDate($date) {
    if ($date && $date != '0000-00-00 00:00:00') {
        return date('Y-m-d', strtotime($date));
    }
    return 'Tidak Diketahui';
}

// Ambil data dari database
$volcanoData = getVolcanoData();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Gunung Berapi - MagmaCare</title>
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
            padding-top: 80px; /* Untuk mengkompensasi navbar fixed */
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

        /* Night Mode State */
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

        .theme-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px hsl(var(--primary) / 0.3);
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

        .logo-btn:hover .logo-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 0 20px hsl(var(--primary) / 0.3);
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

        .cta-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .cta-btn:hover::before {
            left: 100%;
        }

        .cta-btn:hover {
            background: hsl(var(--primary-dark));
            transform: translateY(-3px);
            box-shadow: 0 8px 25px hsl(var(--primary) / 0.5);
        }

        .cta-btn:active {
            transform: translateY(-1px);
        }

        @media (min-width: 640px) {
            .cta-btn {
                display: inline-block;
            }
        }

        /* Info Cards Section Styles */
        .info-cards {
            padding: 4rem 0;
            background-color: hsl(var(--background));
            transition: background-color 0.3s ease;
            min-height: calc(100vh - 160px);
        }

        .info-cards h2 {
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
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        @media (max-width: 768px) {
            .cards-grid {
                grid-template-columns: 1fr;
                padding: 0.5rem;
            }
        }

        .info-card {
            background: hsl(var(--card-bg));
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            transition: all 0.3s ease;
            border: 1px solid hsl(var(--border));
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: hsl(var(--primary) / 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .empty-icon-inner {
            width: 24px;
            height: 24px;
            background: hsl(var(--primary));
            border-radius: 6px;
            position: relative;
        }

        .empty-icon-inner::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 12px;
            height: 12px;
            background: white;
            border-radius: 2px;
        }

        .info-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: hsl(var(--foreground));
            margin: 0;
        }

        .info-card > p {
            color: hsl(var(--muted-foreground));
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0;
        }

        .card-content {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .card-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }

        .card-label {
            color: hsl(var(--muted-foreground));
            font-size: 0.9rem;
            font-weight: 500;
        }

        .card-value {
            color: hsl(var(--foreground));
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-level-4 {
            background: #dc2626;
            color: white;
        }

        .status-level-3 {
            background: #ea580c;
            color: white;
        }

        .status-level-2 {
            background: #d97706;
            color: white;
        }

        .status-level-1 {
            background: #16a34a;
            color: white;
        }

        .status-unknown {
            background: hsl(var(--muted-foreground));
            color: white;
        }

        .card-divider {
            border-top: 1px solid hsl(var(--border));
            margin-top: 1rem;
            padding-top: 1rem;
        }

        .card-footer {
            color: hsl(var(--muted-foreground));
            font-size: 0.85rem;
            text-align: center;
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

        /* Background efek untuk night mode */
        [data-theme="night"] footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 80%, hsl(var(--primary) / 0.03) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
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

        @media (max-width: 1024px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }
            
            .brand-section {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 640px) {
            footer {
                padding: 3rem 0 1.5rem;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding: 0 0.5rem 1.5rem;
            }
        }

        /* Brand Section */
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

        @media (max-width: 640px) {
            .brand-section {
                text-align: center;
            }
            
            .brand-logo {
                justify-content: center;
            }
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

        /* Footer Sections */
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

        @media (max-width: 640px) {
            .footer-section h4 {
                text-align: center;
            }
            
            .footer-section h4::after {
                left: 50%;
                transform: translateX(-50%);
            }
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .footer-links li {
            margin: 0;
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

        /* Contact Section Specific Styles */
        .footer-section:last-child .footer-links li {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .footer-section:last-child .footer-links li::before {
            content: '•';
            color: hsl(var(--primary));
            font-weight: bold;
            flex-shrink: 0;
        }

        /* Social Icons */
        .social-links {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        @media (max-width: 640px) {
            .social-links {
                justify-content: center;
            }
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: hsl(var(--border));
            color: hsl(var(--foreground));
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        [data-theme="night"] .social-link {
            background: hsl(var(--border) / 0.3);
            border: 1px solid hsl(var(--border) / 0.5);
        }

        .social-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, hsl(var(--primary) / 0.1), transparent);
            transition: left 0.5s ease;
        }

        .social-link:hover::before {
            left: 100%;
        }

        .social-link:hover {
            background: hsl(var(--primary));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px hsl(var(--primary) / 0.3);
        }

        [data-theme="night"] .social-link:hover {
            background: hsl(var(--primary));
            border-color: hsl(var(--primary));
        }

        /* Footer Bottom */
        .footer-bottom {
            padding-top: 2rem;
            border-top: 1px solid hsl(var(--border));
            margin-top: 2rem;
            text-align: center;
            max-width: 1200px;
            margin: 2rem auto 0;
            padding: 2rem 1rem 0;
        }

        @media (max-width: 640px) {
            .footer-bottom {
                padding-top: 1.5rem;
                margin-top: 1.5rem;
            }
        }

        .footer-bottom p {
            color: hsl(var(--muted-foreground));
            font-size: 0.9rem;
            margin: 0;
        }

        /* Animation untuk footer elements */
        .footer-section {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }

        .footer-section:nth-child(1) { animation-delay: 0.1s; }
        .footer-section:nth-child(2) { animation-delay: 0.2s; }
        .footer-section:nth-child(3) { animation-delay: 0.3s; }
        .footer-section:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: hsl(var(--background));
        }

        ::-webkit-scrollbar-thumb {
            background: hsl(var(--primary));
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: hsl(var(--primary-dark));
        }

        /* Selection Color */
        ::selection {
            background: hsl(var(--primary) / 0.3);
            color: hsl(var(--foreground));
        }

        /* Background efek untuk night mode */
        [data-theme="night"] body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, hsl(var(--primary) / 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, hsl(var(--primary) / 0.05) 0%, transparent 50%);
            z-index: -1;
            pointer-events: none;
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
            
            .info-cards h2 {
                font-size: 2rem;
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
                <!-- Logo -->
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

    <!-- Data Gunung Section -->
    <section id="data-gunung" class="info-cards">
        <div class="container">
            <h2>Data <span class="text-primary">Gunung Berapi</span> Indonesia</h2>
            <p class="section-desc">Pemantauan real-time status dan aktivitas gunung berapi aktif di Indonesia</p>

            <div class="cards-grid">
                <?php foreach ($volcanoData as $volcano): ?>
                    <?php
                    $statusClass = getStatusClass($volcano['level']);
                    $statusText = getStatus($volcano['level']);
                    $activityText = getActivity($volcano['level']);
                    $eruptionDate = formatEruptionDate($volcano['last_eruption']);
                    ?>
                    
                    <div class="info-card">
                        <div class="card-icon">
                            <div class="empty-icon-inner"></div>
                        </div>
                        
                        <h3><?php echo htmlspecialchars($volcano['name']); ?></h3>
                        <p><?php echo htmlspecialchars($volcano['location']); ?></p>

                        <div class="card-content">
                            <div class="card-row">
                                <span class="card-label">Ketinggian</span>
                                <span class="card-value"><?php echo htmlspecialchars($volcano['height']); ?></span>
                            </div>

                            <div class="card-row">
                                <span class="card-label">Status</span>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </div>

                            <div class="card-row">
                                <span class="card-label">Level</span>
                                <span class="card-value"><?php echo htmlspecialchars($volcano['level']); ?></span>
                            </div>

                            <div class="card-row">
                                <span class="card-label">Aktivitas</span>
                                <span class="card-value"><?php echo $activityText; ?></span>
                            </div>

                            <div class="card-divider">
                                <div class="card-footer">
                                    <span>Erupsi terakhir: <?php echo $eruptionDate; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($volcanoData)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <div class="empty-icon-inner"></div>
                    </div>
                    <h3 class="empty-title">Tidak ada data gunung berapi</h3>
                    <p class="empty-desc">Data gunung berapi tidak ditemukan dalam database.</p>
                    <div class="empty-note">
                        <p><strong>Note:</strong> Database terdeteksi kosong. Silakan tambahkan data gunung berapi terlebih dahulu.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

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
                    <li><a href="#beranda">Beranda</a></li>
                    <li><a href="GUNUNG/gunung.html">Informasi Gunung</a></li>
                    <li><a href="pengertian.html">Pengertian & Jenis</a></li>
                    <li><a href="MITIGASI/mitigasi.html">Mitigasi</a></li>
                    <li><a href="dampak.html">Dampak</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Layanan</h4>
                <ul class="footer-links">
                    <li><a href="infogunung.php">Data Gunung</a></li>
                    <li><a href="erupsi.php">Riwayat Erupsi</a></li>
                    <li><a href="logistik_mitigasi.php">Logistik</a></li>
                    <li><a href="#peta">Peta Kawasan</a></li>
                    <li><a href="#peringatan">Peringatan Dini</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Kontak</h4>
                <ul class="footer-links">
                    <li>Jl. Vulkanologi No. 123</li>
                    <li>Jakarta, Indonesia</li>
                    <li>info@magmacare.id</li>
                    <li>+62 21 1234 5678</li>
                    <li>24/7 Emergency Hotline</li>
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
