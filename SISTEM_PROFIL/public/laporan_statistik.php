<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// FILTER
$tahun_filter       = $_GET['tahun']        ?? '';
$status_filter      = $_GET['status']       ?? '';
$jenisprofil_filter = $_GET['jenisprofil']  ?? '';

$where = " WHERE p.id_jenisprofil NOT IN (1,2) ";
$params = [];

// FILTER TAHUN
if($tahun_filter != ''){
    $where .= " AND YEAR(p.tarikh_mula) = :tahun ";
    $params[':tahun'] = $tahun_filter;
}

// FILTER STATUS
if($status_filter != ''){
    $where .= " AND p.id_status = :status ";
    $params[':status'] = $status_filter;
}

// FILTER JENIS PROFIL
if($jenisprofil_filter != ''){
    $where .= " AND p.id_jenisprofil = :jenis ";
    $params[':jenis'] = $jenisprofil_filter;
}

// Dropdown status
$status_list = $pdo->query("
    SELECT id_status, status FROM lookup_status ORDER BY status ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Dropdown jenis profil
$jenisprofil_list = $pdo->query("
    SELECT id_jenisprofil, jenisprofil FROM lookup_jenisprofil WHERE id_jenisprofil NOT IN (1,2)
    ORDER BY jenisprofil ASC
")->fetchAll(PDO::FETCH_ASSOC);

// QUERY STATISTIK
$sql = "
SELECT 
    YEAR(p.tarikh_mula) AS tahun,
    p.id_jenisprofil,
    p.id_status
FROM profil p
$where
ORDER BY tahun ASC
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
                <!-- FILTER TAHUN -->
                <div class="col-md-3">
                    <label class="form-label">Tahun (Tarikh Mula)</label>
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        <?php for($i=2015;$i<=date('Y');$i++): ?>
                            <option value="<?= $i ?>" <?= ($tahun_filter==$i?'selected':'')?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- FILTER STATUS -->
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach($status_list as $s): ?>
                            <option value="<?= $s['id_status'] ?>" <?=($status_filter==$s['id_status']?'selected':'')?>>
                                <?= $s['status'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- FILTER JENIS PROFIL -->
                <div class="col-md-3">
                    <label class="form-label">Jenis Profil</label>
                    <select name="jenisprofil" class="form-select">
                        <option value="">Semua Jenis</option>
                        <?php foreach($jenisprofil_list as $jp): ?>
                            <option value="<?= $jp['id_jenisprofil'] ?>" <?=($jenisprofil_filter==$jp['id_jenisprofil']?'selected':'')?>>
                                <?= $jp['jenisprofil'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tapis
                    </button>
                </div>

                <div class="col-md-1">
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

                        <?php foreach ($jenisprofil_list as $jp): 
                            // ⚡ if jenisprofil filter active → only show selected jenis
                            if($jenisprofil_filter != '' && $jenisprofil_filter != $jp['id_jenisprofil']) continue;
                        ?>
                            <?php if($status_filter == ''): ?>
                                <th colspan="2"><?= $jp['jenisprofil'] ?></th>
                            <?php else: ?>
                                <th><?= $jp['jenisprofil'] ?></th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>

                    <tr>
                        <?php foreach ($jenisprofil_list as $jp): 
                            if($jenisprofil_filter != '' && $jenisprofil_filter != $jp['id_jenisprofil']) continue;
                        ?>
                            <?php if($status_filter == ''): ?>
                                <th>Aktif</th>
                                <th>Tidak Aktif</th>
                            <?php endif; ?>
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

                    foreach ($data as $tahun => $jenisRows):?>
                        <tr>
                            <td class="text-center"><?= $bil++ ?></td>
                            <td class="text-center"><?= $tahun ?></td>

                            <?php foreach ($jenisprofil_list as $jp):
                                if($jenisprofil_filter != '' && $jenisprofil_filter != $jp['id_jenisprofil']) continue;

                                $id = $jp['id_jenisprofil'];
                                $aktif = $jenisRows[$id]['aktif'] ?? 0;
                                $tidak = $jenisRows[$id]['tidak'] ?? 0;

                                $total[$id]['aktif'] += $aktif;
                                $total[$id]['tidak'] += $tidak;
                            ?>

                                <?php if($status_filter == ''): ?>
                                    <td class="text-center text-primary fw-bold">
                                        <a href="javascript:void(0)" 
                                        class="stat-link text-decoration-none" 
                                        data-tahun="<?= $tahun ?>" 
                                        data-jenis="<?= $id ?>" 
                                        data-status="1">
                                        <?= $aktif ?>
                                        </a>
                                    </td>
                                    <td class="text-center text-danger fw-bold">
                                        <a href="javascript:void(0)" 
                                        class="stat-link text-decoration-none" 
                                        data-tahun="<?= $tahun ?>" 
                                        data-jenis="<?= $id ?>" 
                                        data-status="0">
                                        <?= $tidak ?>
                                        </a>
                                    </td>
                                <?php elseif($status_filter == '1'): ?>
                                    <!-- Hanya Aktif -->
                                    <td class="text-center text-primary fw-bold">
                                        <a href="javascript:void(0)" 
                                        class="stat-link text-decoration-none" 
                                        data-tahun="<?= $tahun ?>" 
                                        data-jenis="<?= $id ?>" 
                                        data-status="1">
                                        <?= $aktif ?>
                                        </a>
                                    </td>
                                <?php elseif($status_filter == '0'): ?>
                                    <!-- Hanya Tidak Aktif -->
                                    <td class="text-center text-danger fw-bold">
                                        <a href="javascript:void(0)" 
                                        class="stat-link text-decoration-none" 
                                        data-tahun="<?= $tahun ?>" 
                                        data-jenis="<?= $id ?>" 
                                        data-status="0">
                                        <?= $tidak ?>
                                        </a>
                                    </td>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>

                    <!-- TOTAL ROW -->
                    <tr class="table-secondary text-center fw-bold">
                        <td colspan="2">Jumlah</td>
                        
                        <?php foreach($jenisprofil_list as $jp):
                            if($jenisprofil_filter != '' && $jenisprofil_filter != $jp['id_jenisprofil']) continue;
                            $id = $jp['id_jenisprofil']; 
                        ?>

                            <?php if($status_filter == ''): ?>
                                <td><?= $total[$id]['aktif'] ?></td>
                                <td><?= $total[$id]['tidak'] ?></td>
                            <?php elseif($status_filter == '1'): ?>
                                <td><?= $total[$id]['aktif'] ?></td>
                            <?php elseif($status_filter == '0'): ?>
                                <td><?= $total[$id]['tidak'] ?></td>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>

            <!-- GRAPH STATISTICS -->
            <?php
            $chartYears = array_keys($data); // X-axis: Years
            $chartJenis = [];
            $chartAktif = [];
            $chartTidak = [];

            foreach ($jenisprofil_list as $jp) {
                if ($jenisprofil_filter != '' && $jenisprofil_filter != $jp['id_jenisprofil']) continue;

                $id = $jp['id_jenisprofil'];
                $chartJenis[$id] = $jp['jenisprofil'];

                foreach ($chartYears as $y) {
                    $aktif = $data[$y][$id]['aktif'] ?? 0;
                    $tidak = $data[$y][$id]['tidak'] ?? 0;

                    $chartAktif[$id][] = $aktif;
                    $chartTidak[$id][] = $tidak;
                }
            }
            ?>
            <!-- CONTAINER TO DISPLAY GRAPH -->
            <div class="mt-5 mb-4 p-3 bg-white shadow-sm rounded">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-bar-chart"></i> Graf Statistik</h5>
                <canvas id="statistikChart" height="120"></canvas>
            </div>

        </div>
    </div>

    <!-- MODAL -->
    <div class="modal fade" id="resultModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Senarai Rekod</h5>
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

    <script>
        const years   = <?= json_encode($chartYears) ?>;
        const jenis   = <?= json_encode($chartJenis) ?>;
        const aktif   = <?= json_encode($chartAktif) ?>;
        const tidak   = <?= json_encode($chartTidak) ?>;

        let datasets = [];

        // Case 1 - JENIS FILTERED (Show Line Trend)
        if ("<?= $jenisprofil_filter ?>" !== "") {
            let id = Object.keys(jenis)[0]; // Single jenis only
            datasets = [
                {
                    type: "line",
                    label: jenis[id] + " (Aktif)",
                    data: aktif[id],
                    borderWidth: 3,
                    tension: 0.3
                },
                {
                    type: "line",
                    label: jenis[id] + " (Tidak Aktif)",
                    data: tidak[id],
                    borderWidth: 3,
                    borderDash: [5,5],
                    tension: 0.3
                }
            ];
        }

        // Case 2 - STATUS FILTER ONLY (Bar Compare by Jenis)
        else if ("<?= $status_filter ?>" !== "") {
            let idList = Object.keys(jenis);
            idList.forEach(id=>{
                datasets.push({
                    label: jenis[id],
                    data: ("<?= $status_filter ?>"==="1" ? aktif[id] : tidak[id]),
                    borderWidth: 1
                });
            });
        }

        // Case 3 - NO FILTER → Multi-Bar Chart (ALL DATA)
        else {
            let idList = Object.keys(jenis);
            idList.forEach(id => {
                datasets.push({
                    label: jenis[id] + " (Aktif)",
                    data: aktif[id],
                    borderWidth: 1
                });
                datasets.push({
                    label: jenis[id] + " (Tidak Aktif)",
                    data: tidak[id],
                    borderWidth: 1,
                    borderDash: [5,5]
                });
            });
        }

        new Chart(document.getElementById('statistikChart'), {
            type: "bar",
            data: {
                labels: years,
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend:{ position:"bottom" }
                },
                scales: {
                    y:{ beginAtZero:true }
                }
            }
        });
    </script>

</body>
</html>
