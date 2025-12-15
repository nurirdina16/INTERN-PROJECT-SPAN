<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// Build filters sama macam laporan_maklumat.php
$where = [];
$params = [];

if (!empty($_GET['tarikh_mula'])) {
    $where[] = "p.tarikh_mula = :tarikh_mula";
    $params[':tarikh_mula'] = $_GET['tarikh_mula'];
}
if (!empty($_GET['tarikh_siap'])) {
    $where[] = "p.tarikh_siap = :tarikh_siap";
    $params[':tarikh_siap'] = $_GET['tarikh_siap'];
}
if (!empty($_GET['tarikh_guna'])) {
    $where[] = "p.tarikh_guna = :tarikh_guna";
    $params[':tarikh_guna'] = $_GET['tarikh_guna'];
}
if (!empty($_GET['tarikh_dibeli'])) {
    $where[] = "p.tarikh_dibeli = :tarikh_dibeli";
    $params[':tarikh_dibeli'] = $_GET['tarikh_dibeli'];
}
if (!empty($_GET['id_kaedahPembangunan'])) {
    $where[] = "p.id_kaedahPembangunan = :id_kaedah";
    $params[':id_kaedah'] = $_GET['id_kaedahPembangunan'];
}
if (!empty($_GET['id_pemilik_profil'])) {
    $where[] = "p.id_pemilik_profil = :pemilik";
    $params[':pemilik'] = $_GET['id_pemilik_profil'];
}
if (!empty($_GET['id_jenisprofil'])) {
    $where[] = "p.id_jenisprofil = :jenisprofil";
    $params[':jenisprofil'] = $_GET['id_jenisprofil'];
}
if (!empty($_GET['q'])) {
    $where[] = "(p.nama_profil LIKE :search OR p.nama_entiti LIKE :search)";
    $params[':search'] = "%" . $_GET['q'] . "%";
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Query ikut table display sama
$sql = "
SELECT p.*, 
    k.kaedahPembangunan,
    b1.bahagianunit AS pemilik_unit,
    b2.bahagianunit AS pengurus_akses_unit,
    b3.bahagianunit AS inhouse_unit,
    b4.bahagianunit AS bahagian_unit,
    j.jenisprofil,
    s.status,
    kat.kategori,
    peny.penyelenggaraan,
    ku.jenis_dalaman,
    ku.jenis_umum,
    per.jenis_peralatan,
    c.carta,
    up1.nama_user AS pegawai_rujukan_nama,
    up2.nama_user AS ketua_nama,
    up3.nama_user AS cio_nama,
    up4.nama_user AS ictso_nama,
    pb.nama_syarikat AS nama_pembekal
FROM profil p
LEFT JOIN lookup_kaedahpembangunan k ON p.id_kaedahPembangunan = k.id_kaedahPembangunan
LEFT JOIN lookup_bahagianunit b1 ON p.id_pemilik_profil = b1.id_bahagianunit
LEFT JOIN lookup_bahagianunit b2 ON p.pengurus_akses = b2.id_bahagianunit
LEFT JOIN lookup_bahagianunit b3 ON p.inhouse = b3.id_bahagianunit
LEFT JOIN lookup_bahagianunit b4 ON p.id_bahagianunit = b4.id_bahagianunit
LEFT JOIN lookup_jenisprofil j ON p.id_jenisprofil = j.id_jenisprofil
LEFT JOIN lookup_status s ON p.id_status = s.id_status
LEFT JOIN lookup_kategori kat ON p.id_kategori = kat.id_kategori
LEFT JOIN lookup_penyelenggaraan peny ON p.id_penyelenggaraan = peny.id_penyelenggaraan
LEFT JOIN lookup_kategoriuser ku ON p.id_kategoriuser = ku.id_kategoriuser
LEFT JOIN lookup_jenisperalatan per ON p.id_jenisperalatan = per.id_jenisperalatan
LEFT JOIN lookup_carta c ON p.id_carta = c.id_carta
LEFT JOIN lookup_pembekal pb ON p.id_pembekal = pb.id_pembekal
LEFT JOIN lookup_userprofile up1 ON p.pegawai_rujukan = up1.id_userprofile
LEFT JOIN lookup_userprofile up2 ON p.nama_ketua = up2.id_userprofile
LEFT JOIN lookup_userprofile up3 ON p.nama_cio = up3.id_userprofile
LEFT JOIN lookup_userprofile up4 ON p.nama_ictso = up4.id_userprofile
$where_sql
ORDER BY p.id_profil DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// HEADER EXCEL
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=laporan_maklumat.xls");

// Susunan column ikut table HTML
$columns = [
    'status', 'jenisprofil', 'nama_profil', 'nama_entiti', 'objektif_profil',
    'pemilik_unit', 'tarikh_mula', 'tarikh_siap', 'tarikh_guna', 'bil_pengguna',
    'bil_modul', 'kategori', 'bahasa_pengaturcaraan', 'pangkalan_data', 'rangkaian',
    'integrasi', 'tarikh_dibeli', 'tempoh_warranty', 'expired_warranty',
    'kos_perkakasan', 'kos_perisian', 'kos_lesen_perisian', 'kos_penyelenggaraan',
    'kos_lain', 'description_kos', 'kos_keseluruhan', 'jenis_peralatan', 'lokasi',
    'no_siri', 'jenama_model', 'jenis_dalaman', 'jenis_umum', 'pengurus_akses_unit',
    'kaedahPembangunan', 'nama_pembekal', 'inhouse_unit', 'penyelenggaraan',
    'tarikh_akhir_penyelenggaraan', 'pegawai_rujukan_nama', 'alamat_pejabat',
    'bahagian_unit', 'ketua_nama', 'cio_nama', 'ictso_nama', 'carta'
];

// Buat table
echo "<table border='1'>";

// Header
echo "<tr>";
foreach ($columns as $col) {
    echo "<th>" . htmlspecialchars($col) . "</th>";
}
echo "</tr>";

// Data
foreach ($results as $r) {
    echo "<tr>";
    foreach ($columns as $col) {
        $val = $r[$col] ?? '';
        // Convert boolean fields ke YA / TIDAK
        if ($col == 'jenis_dalaman' || $col == 'jenis_umum') {
            $val = $val ? 'YA' : 'TIDAK';
        }
        echo "<td>" . htmlspecialchars($val) . "</td>";
    }
    echo "</tr>";
}

echo "</table>";
exit;
