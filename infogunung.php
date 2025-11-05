<?php
// Koneksi ke database
$host = 'localhost';
$dbname = 'gunung_berapi';
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Query untuk mengambil data gunung berapi dengan erupsi terakhir
$sql = "
    SELECT 
        g.id_gunung,
        g.nama_gunung,
        g.lokasi,
        g.ketinggian,
        g.status,
        g.tingkat_aktivitas,
        MAX(e.Tanggal_Erupsi) as last_eruption_date
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
$volcanoData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fungsi untuk menentukan warna status berdasarkan level
function getStatusColor($status) {
    switch($status) {
        case 'Siaga':
        case 'Awas':
            return 'bg-orange-500';
        case 'Waspada':
            return 'bg-yellow-500';
        case 'Normal':
            return 'bg-green-500';
        default:
            return 'bg-gray-500';
    }
}

// Fungsi untuk menentukan level berdasarkan status
function getLevel($status) {
    switch($status) {
        case 'Awas':
            return 'Level IV';
        case 'Siaga':
            return 'Level III';
        case 'Waspada':
            return 'Level II';
        case 'Normal':
            return 'Level I';
        default:
            return 'Tidak Diketahui';
    }
}

// Fungsi untuk memformat ketinggian
function formatHeight($height) {
    if ($height && is_numeric($height)) {
        return number_format($height, 0, ',', '.') . ' m';
    }
    return 'Tidak Diketahui';
}

// Fungsi untuk memformat tanggal erupsi
function formatEruptionDate($date) {
    if ($date && $date != '0000-00-00 00:00:00') {
        return date('Y-m-d', strtotime($date));
    }
    return 'Tidak Diketahui';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Gunung Berapi Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lucide/1.28.0/icon-font.min.css" rel="stylesheet">
    <style>
        .card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .card:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border-color: rgba(59, 130, 246, 0.3);
        }
        .text-primary {
            color: #3b82f6;
        }
        .bg-primary {
            background-color: #3b82f6;
        }
        .text-muted-foreground {
            color: #6b7280;
        }
        .border-border {
            border-color: #e5e7eb;
        }
        .bg-card {
            background-color: white;
        }
        .text-card-foreground {
            color: #1f2937;
        }
    </style>
</head>
<body class="bg-gray-50">
    <section id="data-gunung" class="py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    Data <span class="text-primary">Gunung Berapi</span> Indonesia
                </h2>
                <p class="text-lg text-muted-foreground">
                    Pemantauan real-time status dan aktivitas gunung berapi aktif di Indonesia
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
                <?php foreach ($volcanoData as $volcano): ?>
                    <?php
                    $statusColor = getStatusColor($volcano['status']);
                    $levelText = getLevel($volcano['status']);
                    $heightText = formatHeight($volcano['ketinggian']);
                    $eruptionDate = formatEruptionDate($volcano['last_eruption_date']);
                    ?>
                    
                    <div class="card group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="mountain" class="w-6 h-6 text-primary"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg"><?php echo htmlspecialchars($volcano['nama_gunung']); ?></h3>
                                    <p class="text-sm text-muted-foreground flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                                        <?php echo htmlspecialchars($volcano['lokasi'] ?? 'Tidak Diketahui'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground">Ketinggian</span>
                                <span class="font-semibold"><?php echo $heightText; ?></span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground">Status</span>
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 <?php echo $statusColor; ?> text-white">
                                    <?php echo htmlspecialchars($volcano['status'] ?? 'Tidak Diketahui'); ?>
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground">Level</span>
                                <span class="font-semibold text-sm"><?php echo $levelText; ?></span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground flex items-center gap-1">
                                    <i data-lucide="trending-up" class="w-3 h-3"></i>
                                    Aktivitas
                                </span>
                                <span class="font-semibold text-sm"><?php echo htmlspecialchars($volcano['tingkat_aktivitas'] ?? 'Tidak Diketahui'); ?></span>
                            </div>

                            <div class="pt-3 border-t">
                                <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                    <span>Erupsi terakhir: <?php echo $eruptionDate; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($volcanoData)): ?>
                <div class="text-center py-12">
                    <i data-lucide="mountain" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada data gunung berapi</h3>
                    <p class="text-gray-500">Data gunung berapi tidak ditemukan dalam database.</p>
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg max-w-md mx-auto">
                        <p class="text-sm text-yellow-800">
                            <strong>Note:</strong> Database terdeteksi kosong. Silakan tambahkan data gunung berapi terlebih dahulu.
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Info tentang struktur data -->
            <div class="mt-12 max-w-4xl mx-auto">
                <div class="bg-blue-50 p-6 rounded-lg">
                    <h3 class="font-semibold text-blue-900 mb-3">Informasi Database</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm text-blue-800">
                        <div>
                            <p><strong>Tabel:</strong> gunung</p>
                            <p><strong>Total Data:</strong> <?php echo count($volcanoData); ?> gunung</p>
                        </div>
                        <div>
                            <p><strong>Status yang didukung:</strong> Normal, Waspada, Siaga, Awas</p>
                            <p><strong>Terakhir diakses:</strong> <?php echo date('d-m-Y H:i:s'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/1.28.0/lucide.min.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
