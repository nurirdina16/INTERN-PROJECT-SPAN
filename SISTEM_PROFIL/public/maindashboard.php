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

?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Daftar Profil | Sistem Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    
    <link rel="stylesheet" href="css/maindashboard.css">
    
    <script src="js/sidebar.js" defer></script> 
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <?php include 'header.php'; ?>

        <div class="main-header mt-4 mb-1"><i class="bi bi-speedometer2"></i>Dashboard</div>

        <div class="dashboard-container">

            <div class="row mt-4">
                <!-- JUMLAH EACH JENIS PROFIL -->
                <div class="col-md-6">
                    <div class="row g-4">
                        <?php foreach ($data_kiraan as $item): ?>
                            <div class="col-md-12">
                                <div class="dash-card shadow-sm">

                                    <div class="dash-card-icon">
                                        <i class="bi bi-grid-fill"></i>
                                    </div>

                                    <div class="dash-card-title">
                                        <?= htmlspecialchars($item['jenisprofil']); ?>
                                    </div>

                                    <div class="dash-card-number">
                                        <?= $item['jumlah']; ?>
                                    </div>

                                    <div class="dash-card-footer">
                                        Jumlah Rekod
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- TABLE TAHUN PEMBANGUNAN -->
                <div class="col-md-6">
                    <div class="card shadow-sm p-3">
                        <h5 class="mb-3"><i class="bi bi-calendar-range"></i> Rekod Mengikut Tahun Pembangunan</h5>

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

                <!-- LINE GRAPH TAHUN PEMBANGUNAN -->

            </div>
      
        </div>
    </div>

</body>
</html>
