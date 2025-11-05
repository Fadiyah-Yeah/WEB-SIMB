<?php
// Koneksi ke database
$host = 'localhost';
$dbname = 'gunung_berapi';
$username = 'username';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Query untuk mengambil data gunung berapi
$sql = "SELECT * FROM volcanoes ORDER BY 
        CASE 
            WHEN level = 'Level III' THEN 1
            WHEN level = 'Level II' THEN 2
            WHEN level = 'Level I' THEN 3
            ELSE 4
        END, name ASC";
$stmt = $pdo->query($sql);
$volcanoData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fungsi untuk menentukan warna status berdasarkan level
function getStatusColor($level) {
    switch($level) {
        case 'Level III':
            return 'bg-orange-500';
        case 'Level II':
            return 'bg-yellow-500';
        case 'Level I':
            return 'bg-green-500';
        default:
            return 'bg-gray-500';
    }
}

// Fungsi untuk menentukan status berdasarkan level
function getStatus($level) {
    switch($level) {
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

// Fungsi untuk menentukan tingkat aktivitas
function getActivity($level) {
    switch($level) {
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
                    $statusColor = getStatusColor($volcano['level']);
                    $statusText = getStatus($volcano['level']);
                    $activityText = getActivity($volcano['level']);
                    ?>
                    
                    <div class="card group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="mountain" class="w-6 h-6 text-primary"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg"><?php echo htmlspecialchars($volcano['name']); ?></h3>
                                    <p class="text-sm text-muted-foreground flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                                        <?php echo htmlspecialchars($volcano['location']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground">Ketinggian</span>
                                <span class="font-semibold"><?php echo htmlspecialchars($volcano['height']); ?></span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground">Status</span>
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 <?php echo $statusColor; ?> text-white">
                                    <?php echo $statusText; ?>
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground">Level</span>
                                <span class="font-semibold text-sm"><?php echo htmlspecialchars($volcano['level']); ?></span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground flex items-center gap-1">
                                    <i data-lucide="trending-up" class="w-3 h-3"></i>
                                    Aktivitas
                                </span>
                                <span class="font-semibold text-sm"><?php echo $activityText; ?></span>
                            </div>

                            <div class="pt-3 border-t">
                                <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                    <span>Erupsi terakhir: <?php echo htmlspecialchars($volcano['last_eruption']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($volcanoData)): ?>
                <div class="text-center py-12">
                    <i data-lucide="mountain" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada data</h3>
                    <p class="text-gray-500">Data gunung berapi tidak ditemukan dalam database.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/1.28.0/lucide.min.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
