<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// Ambil filters
$tahun_filter       = $_GET['tahun']        ?? '';
$status_filter      = $_GET['status']       ?? '';
$jenisprofil_filter = $_GET['jenisprofil']  ?? '';

// Query lookup jenisprofil & status
$jenisprofil_list = $pdo->query("SELECT id_jenisprofil, jenisprofil FROM lookup_jenisprofil WHERE id_jenisprofil NOT IN (1,2) ORDER BY jenisprofil ASC")->fetchAll(PDO::FETCH_ASSOC);
$status_list      = $pdo->query("SELECT id_status, status FROM lookup_status ORDER BY status ASC")->fetchAll(PDO::FETCH_ASSOC);

// Build WHERE
$where = " WHERE p.id_jenisprofil NOT IN (1,2) ";
$params = [];

if($tahun_filter != '') {
    $where .= " AND YEAR(p.tarikh_mula) = :tahun ";
    $params[':tahun'] = $tahun_filter;
}

if($status_filter != '') {
    $where .= " AND p.id_status = :status ";
    $params[':status'] = $status_filter;
}

if($jenisprofil_filter != '') {
    $where .= " AND p.id_jenisprofil = :jenis ";
    $params[':jenis'] = $jenisprofil_filter;
}

// Query statistik
$sql = "SELECT YEAR(p.tarikh_mula) AS tahun, p.id_jenisprofil, p.id_status FROM profil p $where ORDER BY tahun ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Array statistik
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

// HEADER EXCEL
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=laporan_statistik.xls");

// Buat table
echo "<table border='1'>";

// Header
echo "<tr>";
echo "<th>Tahun</th>";
foreach ($jenisprofil_list as $jp) {
    if($jenisprofil_filter != '' && $jenisprofil_filter != $jp['id_jenisprofil']) continue;
    echo "<th colspan='2'>{$jp['jenisprofil']}</th>";
}
echo "</tr>";

// Sub-header
echo "<tr>";
echo "<th></th>";
foreach ($jenisprofil_list as $jp) {
    if($jenisprofil_filter != '' && $jenisprofil_filter != $jp['id_jenisprofil']) continue;
    echo "<th>Aktif</th>";
    echo "<th>Tidak Aktif</th>";
}
echo "</tr>";

// Data
foreach ($data as $tahun => $jenisRows) {
    echo "<tr>";
    echo "<td>{$tahun}</td>";
    foreach ($jenisprofil_list as $jp) {
        if($jenisprofil_filter != '' && $jenisprofil_filter != $jp['id_jenisprofil']) continue;
        $id = $jp['id_jenisprofil'];
        $aktif = $jenisRows[$id]['aktif'] ?? 0;
        $tidak = $jenisRows[$id]['tidak'] ?? 0;
        echo "<td>{$aktif}</td>";
        echo "<td>{$tidak}</td>";
    }
    echo "</tr>";
}

// TOTAL
echo "<tr>";
echo "<td>Jumlah</td>";
$total = [];
foreach ($jenisprofil_list as $jp) {
    if($jenisprofil_filter != '' && $jenisprofil_filter != $jp['id_jenisprofil']) continue;
    $id = $jp['id_jenisprofil'];
    $sumAktif = 0;
    $sumTidak = 0;
    foreach ($data as $tahun => $jenisRows) {
        $sumAktif += $jenisRows[$id]['aktif'] ?? 0;
        $sumTidak += $jenisRows[$id]['tidak'] ?? 0;
    }
    echo "<td>{$sumAktif}</td>";
    echo "<td>{$sumTidak}</td>";
}
echo "</tr>";

echo "</table>";
exit;
?>
