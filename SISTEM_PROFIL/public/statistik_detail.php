<?php
require_once '../app/config.php';

$tahun  = $_GET['tahun'];
$jenis  = $_GET['jenis'];
$status = $_GET['status'];

// Ubah dari 0/1 ke id_status sebenar
if ($status == '0') $status = 2;
elseif ($status == '1') $status = 1;

$where = " WHERE p.id_jenisprofil = :jenis AND p.id_status = :status ";
$params = ['jenis'=>$jenis,'status'=>$status];

if ($tahun === 'Tiada Tahun') {
    $where .= " AND (p.tarikh_mula IS NULL OR YEAR(p.tarikh_mula) = 0) ";
}
elseif ($tahun !== 'all' && $tahun !== '') {
    $where .= " AND YEAR(p.tarikh_mula) = :tahun ";
    $params['tahun'] = $tahun;
}

$query = $pdo->prepare("
    SELECT 
        p.nama_profil,
        lp.jenisprofil,
        p.tarikh_mula,
        p.tarikh_siap,
        s.status
    FROM profil p
    JOIN lookup_status s ON p.id_status = s.id_status
    JOIN lookup_jenisprofil lp ON lp.id_jenisprofil = p.id_jenisprofil
    $where
");
$query->execute($params);

$data = $query->fetchAll(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<p class='text-center text-danger'> Tiada Rekod Dijumpai </p>";
    exit;
}
?>


<table class="table table-bordered table-striped">
    <thead class="table-primary">
        <tr>
            <th>Nama Profil</th>
            <th>Jenis Profil</th>
            <th>Tarikh Mula</th>
            <th>Tarikh Siap</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($data as $row): ?>
        <tr>
            <td><?= $row['nama_profil'] ?></td>
            <td><?= $row['jenisprofil'] ?></td>
            <td><?= $row['tarikh_mula'] ?></td>
            <td><?= $row['tarikh_siap'] ?></td>
            <td><?= $row['status'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

