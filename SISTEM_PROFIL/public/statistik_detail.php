<?php
require_once '../app/config.php';

$tahun  = $_GET['tahun'];
$jenis  = $_GET['jenis'];
$status = $_GET['status'];

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
    WHERE YEAR(p.tarikh_mula) = :tahun
      AND p.id_jenisprofil = :jenis
      AND p.id_status = :status
");

$query->execute(['tahun'=>$tahun,'jenis'=>$jenis,'status'=>$status]);
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

