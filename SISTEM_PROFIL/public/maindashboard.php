<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// Dapatkan semua jenis profil
$stmt = $pdo->query("
    SELECT id_jenisprofil, jenisprofil
    FROM lookup_jenisprofil
    WHERE id_jenisprofil NOT IN (1, 2)
    ORDER BY jenisprofil ASC
");
$jenis_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Setup tahun
$currentYear = date('Y');
$years = [];
for ($i = 0; $i < 5; $i++) {
    $years[] = $currentYear - $i;
}

// Prepare query
$stmtTotal = $pdo->prepare("
    SELECT COUNT(*) AS jumlah
    FROM profil
    WHERE id_jenisprofil = ?
");

$stmtYear = $pdo->prepare("
    SELECT COUNT(*) AS jumlah
    FROM profil
    WHERE id_jenisprofil = ?
    AND YEAR(tarikh_mula) = ?
");

// Kira jumlah setiap jenis profil
$data_kiraan = [];

foreach ($jenis_list as $row) {

    // Total per jenis
    $stmtTotal->execute([$row['id_jenisprofil']]);
    $jumlah = $stmtTotal->fetch(PDO::FETCH_ASSOC)['jumlah'];

    // Yearly data
    $tahunData = [];
    foreach ($years as $yr) {
        $stmtYear->execute([$row['id_jenisprofil'], $yr]);
        $tahunData[$yr] = $stmtYear->fetch(PDO::FETCH_ASSOC)['jumlah'];
    }

    // Store final data
    $data_kiraan[] = [
        'jenisprofil' => $row['jenisprofil'],
        'jumlah' => $jumlah,
        'tahunData' => $tahunData
    ];
}

// STATUS PROFIL (PIE CHART)
$stmtStatus = $pdo->query("
    SELECT ls.status, COUNT(*) AS jumlah
    FROM profil p
    JOIN lookup_status ls ON p.id_status = ls.id_status
    GROUP BY ls.status
");
$statusData = $stmtStatus->fetchAll(PDO::FETCH_ASSOC);

// PROFIL MENGIKUT BAHAGIAN (BAR CHART)
$stmtBahagian = $pdo->query("
    SELECT bu.bahagianunit, COUNT(*) AS jumlah
    FROM profil p
    JOIN lookup_bahagianunit bu ON p.id_pemilik_profil = bu.id_bahagianunit
    GROUP BY bu.bahagianunit
    ORDER BY jumlah DESC
");
$bahagianData = $stmtBahagian->fetchAll(PDO::FETCH_ASSOC);

// KAEDAH PEMBANGUNAN (DONUT CHART)
$stmtKaedah = $pdo->query("
    SELECT kp.kaedahPembangunan, COUNT(*) AS jumlah
    FROM profil p
    JOIN lookup_kaedahpembangunan kp ON p.id_kaedahpembangunan = kp.id_kaedahPembangunan
    GROUP BY kp.kaedahPembangunan
");
$kaedahData = $stmtKaedah->fetchAll(PDO::FETCH_ASSOC);

// JUMLAH SEMUA SISTEM
$stmtTotalAll = $pdo->query("SELECT COUNT(*) AS total_semua FROM profil");
$total_semua = $stmtTotalAll->fetch(PDO::FETCH_ASSOC)['total_semua'];

?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Sistem Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    
    <link rel="stylesheet" href="css/maindashboard.css">
    
    <script src="js/sidebar.js" defer></script> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <!-- FIXED HEADER -->
        <?php include 'header.php'; ?>

        <div class="main-header mt-4 mb-2"><i class="bi bi-grid-1x2-fill"></i>Dashboard</div>

        <div class="dashboard-container">

            <!-- ===== KPI CARD ROW ===== -->
            <div class="kpi-row">
                <!-- TOTAL SISTEM -->
                <div class="dash-card" style="background: linear-gradient(135deg, #0b4f89, #0a3a63); color: white;">
                    <div class="dash-card-icon" style="background: rgba(255,255,255,0.2);">
                        <i class="bi bi-collection" style="color:white;"></i>
                    </div>
                    <div class="dash-card-title text-white">Jumlah Keseluruhan Profil</div>
                    <div class="dash-card-number text-white"><?= $total_semua ?></div>
                    <div class="dash-card-footer" style="color: #e1e8f0;">Semua Jenis Profil</div>
                </div>

                <!-- EACH PROFILE CATEGORY -->
                <?php foreach ($data_kiraan as $item): ?>
                    <div class="dash-card">
                        <div class="dash-card-icon">
                            <i class="bi bi-grid-fill"></i>
                        </div>
                        <div class="dash-card-title"><?= htmlspecialchars($item['jenisprofil']); ?></div>
                        <div class="dash-card-number"><?= $item['jumlah']; ?></div>
                        <div class="dash-card-footer">Jumlah Rekod</div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- GRAPHS -->
            <div class="section-row">
                <!-- PIE CHART -->
                <div class="card">
                    <h5><i class="bi bi-pie-chart"></i> Status Profil</h5>
                    <canvas id="statusChart"></canvas>
                </div>

                <!-- TABLE -->
                <div class="card">
                    <h5><i class="bi bi-calendar-range"></i> Rekod Mengikut Tahun Pembangunan</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Jenis Profil</th>
                                <?php foreach ($years as $yr): ?>
                                    <th><?= $yr ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data_kiraan as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['jenisprofil']); ?></td>
                                    <?php foreach ($years as $yr): ?>
                                        <td><?= $item['tahunData'][$yr] ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>                
            </div>

            <!-- BAR CHART -->
            <div class="section-row">
                <div class="card">
                    <h5><i class="bi bi-bar-chart"></i> Graf Pembangunan Profil</h5>
                    <canvas id="profilChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <script>
        const labels = <?= json_encode($years) ?>;

        const datasets = [
            <?php foreach($data_kiraan as $item): ?>
            {
                label: <?= json_encode($item['jenisprofil']) ?>,
                data: <?= json_encode(array_values($item['tahunData'])) ?>,
                backgroundColor: 'rgba(<?= rand(0,255) ?>, <?= rand(0,255) ?>, <?= rand(0,255) ?>, 0.5)',
                borderColor: 'rgba(0,0,0,0.7)',
                borderWidth: 1
            },
            <?php endforeach; ?>
        ];

        const ctx = document.getElementById('profilChart').getContext('2d');
        const profilChart = new Chart(ctx, {
            type: 'bar', // boleh tukar ke 'line' kalau mahu line chart
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Profil Mengikut Tahun'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>

    <script>
        // ================= PIE CHART STATUS =================
        const statusLabels = <?= json_encode(array_column($statusData, 'status')) ?>;
        const statusCounts = <?= json_encode(array_column($statusData, 'jumlah')) ?>;

        new Chart(document.getElementById("statusChart"), {
            type: 'pie',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: statusLabels.map(() => 
                        'rgba(' + Math.floor(Math.random()*255) + ',' + 
                        Math.floor(Math.random()*255) + ',' + 
                        Math.floor(Math.random()*255) + ', 0.7)'
                    )
                }]
            }
        });

        // ================= BAR CHART BAHAGIAN UNIT =================
        const bahagianLabels = <?= json_encode(array_column($bahagianData, 'bahagianunit')) ?>;
        const bahagianCounts = <?= json_encode(array_column($bahagianData, 'jumlah')) ?>;

        new Chart(document.getElementById("bahagianChart"), {
            type: 'bar',
            data: {
                labels: bahagianLabels,
                datasets: [{
                    label: 'Bilangan Profil',
                    data: bahagianCounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        // ================= DONUT CHART KAEDAH PEMBANGUNAN =================
        const kaedahLabels = <?= json_encode(array_column($kaedahData, 'kaedahPembangunan')) ?>;
        const kaedahCounts = <?= json_encode(array_column($kaedahData, 'jumlah')) ?>;

        new Chart(document.getElementById("kaedahChart"), {
            type: 'doughnut',
            data: {
                labels: kaedahLabels,
                datasets: [{
                    data: kaedahCounts,
                    backgroundColor: kaedahLabels.map(() => 
                        'rgba(' + Math.floor(Math.random()*255) + ',' + 
                        Math.floor(Math.random()*255) + ',' + 
                        Math.floor(Math.random()*255) + ', 0.7)'
                    )
                }]
            }
        });
    </script>

</body>
</html>
