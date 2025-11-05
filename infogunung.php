<?php
// Koneksi ke database
$host = 'localhost';
$dbname = 'gunung_berapi';
$username = 'root'; // Ganti dengan username database Anda
$password = ''; // Ganti dengan password database Anda

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
$volcanoData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fungsi untuk menentukan warna status berdasarkan level
function getStatusColor($level) {
    switch($level) {
        case 'Level IV':
            return 'bg-red-500';
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

// Fungsi untuk menentukan tingkat aktivitas
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

// Format tanggal erupsi
function formatEruptionDate($date) {
    if ($date && $date != '0000-00-00 00:00:00') {
        return date('Y-m-d', strtotime($date));
    }
    return 'Tidak Diketahui';
}
?>

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
                $eruptionDate = formatEruptionDate($volcano['last_eruption']);
                ?>
                
                <div class="p-6 hover:shadow-xl transition-all duration-300 group border-2 hover:border-primary/30 rounded-lg border-border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg"><?php echo htmlspecialchars($volcano['name']); ?></h3>
                                <p class="text-sm text-muted-foreground flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
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
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                Aktivitas
                            </span>
                            <span class="font-semibold text-sm"><?php echo $activityText; ?></span>
                        </div>

                        <div class="pt-3 border-t">
                            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Erupsi terakhir: <?php echo $eruptionDate; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($volcanoData)): ?>
            <div class="text-center py-12">
                <div class="w-16 h-16 rounded-xl bg-primary/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak ada data gunung berapi</h3>
                <p class="text-gray-500">Data gunung berapi tidak ditemukan dalam database.</p>
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg max-w-md mx-auto">
                    <p class="text-sm text-yellow-800">
                        <strong>Note:</strong> Database terdeteksi kosong. Silakan tambahkan data gunung berapi terlebih dahulu.
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
