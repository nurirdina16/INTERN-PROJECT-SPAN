<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// FILTER DATE
$tarikh_mula = $_GET['tarikh_mula'] ?? '';
$tarikh_akhir = $_GET['tarikh_akhir'] ?? '';

$where = "";
$params = [];

if (!empty($tarikh_mula)) {
    $where .= " AND p.tarikh_mula >= :tmula";
    $params[':tmula'] = $tarikh_mula;
}

if (!empty($tarikh_akhir)) {
    $where .= " AND p.tarikh_siap <= :takhir";
    $params[':takhir'] = $tarikh_akhir;
}

// LIST JENIS PROFIL
$jenisprofil_list = $pdo->query("
    SELECT id_jenisprofil, jenisprofil 
    FROM lookup_jenisprofil
    WHERE id_jenisprofil NOT IN (1, 2)
    ORDER BY jenisprofil ASC
")->fetchAll(PDO::FETCH_ASSOC);

// QUERY STATISTIK
$sql = "
SELECT 
    YEAR(p.tarikh_mula) AS tahun,
    p.id_jenisprofil,
    p.id_status
FROM profil p
WHERE p.id_jenisprofil NOT IN (1, 2) $where
ORDER BY tahun
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ARRAY TABLE
$data = [];

foreach ($records as $row) {
    $tahun = $row['tahun'] ?? 'Tiada Tahun';
    $jenis = $row['id_jenisprofil'];
    $status = $row['id_status'];

    if (!isset($data[$tahun][$jenis])) {
        $data[$tahun][$jenis] = ['aktif' => 0, 'tidak' => 0];
    }

    if ($status == 1) {
        $data[$tahun][$jenis]['aktif']++;
    } else {
        $data[$tahun][$jenis]['tidak']++;
    }
}

?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statistik Profil | Sistem Profil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/laporan.css">

    <script src="js/sidebar.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <!-- FIXED HEADER -->
        <?php include 'header.php'; ?>
            
        <div class="main-header mt-3 mb-3"><i class="bi bi-file-earmark-bar-graph"></i> Statistik Profil</div>

        <div class="profil-card shadow-sm p-4">
            <!--FILTER-->
            <form method="GET" class="filter-form row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label class="form-label">Tarikh Mula</label>
                    <input type="date" name="tarikh_mula" class="form-control" value="<?= $tarikh_mula ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tarikh Akhir</label>
                    <input type="date" name="tarikh_akhir" class="form-control" value="<?= $tarikh_akhir ?>">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tapis
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="laporan_statistik.php" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
            
            <!--TABLE-->
            <table class="table table-bordered table-striped">
                <thead class="table-primary text-center align-middle">
                    <tr>
                        <th rowspan="2">BIL</th>
                        <th rowspan="2">TAHUN</th>

                        <?php foreach ($jenisprofil_list as $jp): ?>
                            <th colspan="2"><?= $jp['jenisprofil'] ?></th>
                        <?php endforeach; ?>
                    </tr>

                    <tr>
                        <?php foreach ($jenisprofil_list as $jp): ?>
                            <th>Aktif</th>
                            <th>Tidak Aktif</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $bil = 1;
                    $total = []; // initialize total array

                    // initialize total per jenis
                    foreach ($jenisprofil_list as $jp) {
                        $id = $jp['id_jenisprofil'];
                        $total[$id] = ['aktif' => 0, 'tidak' => 0];
                    }

                    foreach ($data as $tahun => $jenisRows):
                    ?>

                    <tr>
                        <td class="text-center align-middle"><?= $bil++ ?></td>
                        <td class="text-center align-middle"><?= $tahun ?></td>

                        <?php foreach ($jenisprofil_list as $jp): 
                            $id = $jp['id_jenisprofil'];
                            $aktif = $jenisRows[$id]['aktif'] ?? 0;
                            $tidak = $jenisRows[$id]['tidak'] ?? 0;

                            // add to total
                            $total[$id]['aktif'] += $aktif;
                            $total[$id]['tidak'] += $tidak;
                        ?>
                            <!-- Aktif -->
                            <td class="text-center">
                                <span class="stat-link text-primary fw-bold" 
                                    data-tahun="<?= $tahun ?>" 
                                    data-jenis="<?= $id ?>" 
                                    data-status="1">
                                    <?= $aktif ?>
                                </span>
                            </td>
                            <!-- Tidak Aktif -->
                            <td class="text-center">
                                <span class="stat-link text-danger fw-bold"
                                    data-tahun="<?= $tahun ?>" 
                                    data-jenis="<?= $id ?>" 
                                    data-status="0">
                                    <?= $tidak ?>
                                </span>
                            </td>
                        <?php endforeach; ?>
                        
                    </tr>
                    <?php endforeach; ?>

                    <!-- TOTAL ROW -->
                    <tr class="table-secondary text-center fw-bold">
                        <td colspan="2">Jumlah</td>
                        <?php foreach ($jenisprofil_list as $jp):
                            $id = $jp['id_jenisprofil'];
                        ?>
                            <td><?= $total[$id]['aktif'] ?></td>
                            <td><?= $total[$id]['tidak'] ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    <!-- MODAL -->
    <div class="modal fade" id="resultModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Senarai Rekod Terlibat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBodyResult">
                    <p class="text-center text-secondary">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll(".stat-link").forEach(function(el){
            el.addEventListener("click", function(){
                
                let tahun  = this.dataset.tahun;
                let jenis  = this.dataset.jenis;
                let status = this.dataset.status;

                // Load modal
                new bootstrap.Modal(document.getElementById('resultModal')).show();
                document.getElementById("modalBodyResult").innerHTML = "<p class='text-center'>Loading...</p>";

                // AJAX fetch
                fetch("statistik_detail.php?tahun="+tahun+"&jenis="+jenis+"&status="+status)
                .then(res=>res.text())
                .then(data=>{
                    document.getElementById("modalBodyResult").innerHTML = data;
                });
            });
        });
    </script>

</body>
</html>
