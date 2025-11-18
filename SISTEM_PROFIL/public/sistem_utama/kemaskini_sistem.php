<?php
require_once '../../app/config.php';
require_once '../../app/auth.php';
require_login();

// Get ID
$id = $_POST['id_sistemutama'] ?? null;
if (!$id) {
    die("Invalid ID");
}

/* ============================
   A. UPDATE SISTEM_UTAMA 
   ============================ */
$sql1 = "UPDATE sistem_utama SET 
    nama_entiti = ?, 
    tarikh_kemaskini = ?, 
    bahagian = ?, 
    alamat = ?, 
    nama_ketua = ?, 
    no_telefon = ?, 
    no_faks = ?, 
    emel_ketua = ?, 
    cio = ?, 
    ictso = ?, 
    carta_organisasi = ?
    WHERE id_sistemutama = ?";

$stmt1 = $pdo->prepare($sql1);
$stmt1->execute([
    $_POST['nama_entiti'],
    $_POST['tarikh_kemaskini'],
    $_POST['bahagian'],
    $_POST['alamat'],
    $_POST['nama_ketua'],
    $_POST['no_telefon'],
    $_POST['no_faks'],
    $_POST['emel_ketua'],
    $_POST['cio'],
    $_POST['ictso'],
    $_POST['carta_organisasi'],
    $id
]);


/* ============================
   B. UPDATE SISTEM_APLIKASI 
   ============================ */

// Check if record exists — if not, insert
$check = $pdo->prepare("SELECT id_sistemutama FROM sistem_aplikasi WHERE id_sistemutama = ?");
$check->execute([$id]);

if ($check->rowCount() == 0) {
    $pdo->prepare("INSERT INTO sistem_aplikasi (id_sistemutama) VALUES (?)")->execute([$id]);
}

$sql2 = "UPDATE sistem_aplikasi SET
    nama_sistem = ?,
    objektif = ?,
    pemilik = ?,
    tarikh_mula = ?,
    tarikh_siap = ?,
    tarikh_guna = ?,
    bil_pengguna = ?,
    kaedah_pembangunan = ?,
    inhouse = ?,
    outsource = ?,
    bil_modul = ?,
    kategori = ?,
    bahasa_pengaturcaraan = ?,
    pangkalan_data = ?,
    rangkaian = ?,
    integrasi = ?,
    penyelenggaraan = ?
    WHERE id_sistemutama = ?";

$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([
    $_POST['nama_sistem'],
    $_POST['objektif'],
    $_POST['pemilik'],
    $_POST['tarikh_mula'],
    $_POST['tarikh_siap'],
    $_POST['tarikh_guna'],
    $_POST['bil_pengguna'],
    $_POST['kaedah_pembangunan'],
    $_POST['inhouse'],
    $_POST['outsource'],
    $_POST['bil_modul'],
    $_POST['kategori'],
    $_POST['bahasa_pengaturcaraan'],
    $_POST['pangkalan_data'],
    $_POST['rangkaian'],
    $_POST['integrasi'],
    $_POST['penyelenggaraan'],
    $id
]);


/* ============================
   C. UPDATE KOS_SISTEM 
   ============================ */

$check = $pdo->prepare("SELECT id_sistemutama FROM kos_sistem WHERE id_sistemutama = ?");
$check->execute([$id]);

if ($check->rowCount() == 0) {
    $pdo->prepare("INSERT INTO kos_sistem (id_sistemutama) VALUES (?)")->execute([$id]);
}

$sql3 = "UPDATE kos_sistem SET
    keseluruhan = ?,
    perkakasan = ?,
    perisian = ?,
    lesen_perisian = ?,
    penyelenggaraan_kos = ?,
    kos_lain = ?
    WHERE id_sistemutama = ?";

$stmt3 = $pdo->prepare($sql3);
$stmt3->execute([
    $_POST['keseluruhan'],
    $_POST['perkakasan'],
    $_POST['perisian'],
    $_POST['lesen_perisian'],
    $_POST['penyelenggaraan_kos'],
    $_POST['kos_lain'],
    $id
]);


/* ============================
   D. UPDATE AKSES_SISTEM 
   ============================ */

$check = $pdo->prepare("SELECT id_sistemutama FROM akses_sistem WHERE id_sistemutama = ?");
$check->execute([$id]);

if ($check->rowCount() == 0) {
    $pdo->prepare("INSERT INTO akses_sistem (id_sistemutama) VALUES (?)")->execute([$id]);
}

$sql4 = "UPDATE akses_sistem SET
    kategori_dalaman = ?,
    kategori_umum = ?,
    pegawai_urus_akses = ?
    WHERE id_sistemutama = ?";

$stmt4 = $pdo->prepare($sql4);
$stmt4->execute([
    $_POST['kategori_dalaman'],
    $_POST['kategori_umum'],
    $_POST['pegawai_urus_akses'] ?? '',
    $id
]);


/* ============================
   E. UPDATE PEGAWAI_RUJUKAN_SISTEM 
   ============================ */

$check = $pdo->prepare("SELECT id_sistemutama FROM pegawai_rujukan_sistem WHERE id_sistemutama = ?");
$check->execute([$id]);

if ($check->rowCount() == 0) {
    $pdo->prepare("INSERT INTO pegawai_rujukan_sistem (id_sistemutama) VALUES (?)")->execute([$id]);
}

// Update data
$sql5 = "UPDATE pegawai_rujukan_sistem SET
    nama_pegawai = ?,
    jawatan_gred = ?,
    bahagian = ?,
    emel_pegawai = ?,
    no_telefon = ?
    WHERE id_sistemutama = ?";

$stmt5 = $pdo->prepare($sql5);
$stmt5->execute([
    $_POST['nama_pegawai'],
    $_POST['jawatan_gred'],
    $_POST['bahagian_pegawai'],
    $_POST['emel_pegawai'],
    $_POST['no_telefon_pegawai'],
    $id     // ← Correct variable
]);


// Redirect
header("Location: view_sistem.php?id=$id&updated=1");
exit;
?>
