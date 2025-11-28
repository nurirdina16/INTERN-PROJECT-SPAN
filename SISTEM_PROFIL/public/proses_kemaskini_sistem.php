<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// ======================
// VALIDATE ID
// ======================
if (!isset($_POST['id_profilsistem'])) {
    header("Location: profil_sistem.php");
    exit;
}

$id = intval($_POST['id_profilsistem']);

// ======================
// CHECK NEW SUPPLIER
// ======================
$id_pembekal = $_POST['id_pembekal'] ?? null;

if ($id_pembekal === "NEW_SUPPLIER") {

    // Insert pembekal baru
    $stmt = $pdo->prepare("
        INSERT INTO lookup_pembekal 
        (nama_syarikat, tempoh_kontrak, alamat_syarikat, nama_PIC, jawatan_PIC, 
         emel_PIC, notelefon_PIC, fax_PIC)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['nama_syarikat_baru'] ?? '',
        $_POST['tempoh_kontrak_baru'] ?? '',
        $_POST['alamat_syarikat_baru'] ?? '',
        $_POST['nama_PIC_baru'] ?? '',
        $_POST['jawatan_PIC_baru'] ?? '',
        $_POST['emel_PIC_baru'] ?? '',
        $_POST['notelefon_PIC_baru'] ?? '',
        $_POST['fax_PIC_baru'] ?? ''
    ]);

    $id_pembekal = $pdo->lastInsertId();
}

// ======================
// UPDATE TABLE PROFIL
// ======================
$updateProfil = $pdo->prepare("
    UPDATE PROFIL SET
        id_status = ?, 
        nama_entiti = ?, 
        alamat_pejabat = ?, 
        id_bahagianunit = ?, 
        id_carta = ?, 
        nama_ketua = ?, 
        nama_cio = ?, 
        nama_ictso = ?
    WHERE id_profilsistem = ?
");

$updateProfil->execute([
    $_POST['id_status'],
    $_POST['nama_entiti'],
    $_POST['alamat_pejabat'],
    $_POST['id_bahagianunit'],
    $_POST['id_carta'] ?: null,
    $_POST['nama_ketua'],
    $_POST['nama_cio'],
    $_POST['nama_ictso'],
    $id
]);

// ======================
// UPDATE TABLE SISTEM
// ======================
$updateSistem = $pdo->prepare("
    UPDATE SISTEM SET
        nama_sistem = ?, 
        id_pemilik_sistem = ?, 
        objektif_sistem = ?, 
        tarikh_mula = ?, 
        tarikh_siap = ?, 
        tarikh_guna = ?, 
        id_kategori = ?, 
        bil_pengguna = ?, 
        bil_modul = ?, 
        bahasa_pengaturcaraan = ?, 
        pangkalan_data = ?, 
        rangkaian = ?, 
        integrasi = ?, 
        id_kaedahpembangunan = ?, 
        inhouse = ?, 
        id_pembekal = ?, 
        tarikh_dibeli = ?, 
        tempoh_jaminan_sistem = ?, 
        expired_jaminan_sistem = ?, 
        id_penyelenggaraan = ?, 
        kos_keseluruhan = ?, 
        kos_perkakasan = ?, 
        kos_perisian = ?, 
        kos_lesen_perisian = ?, 
        kos_penyelenggaraan = ?, 
        kos_lain = ?, 
        id_kategoriuser = ?, 
        pengurus_akses = ?
    WHERE id_profilsistem = ?
");

$updateSistem->execute([
    $_POST['nama_sistem'],
    $_POST['id_pemilik_sistem'],
    $_POST['objektif_sistem'],
    $_POST['tarikh_mula'],
    $_POST['tarikh_siap'],
    $_POST['tarikh_guna'],
    $_POST['id_kategori'],
    $_POST['bil_pengguna'],
    $_POST['bil_modul'],
    $_POST['bahasa_pengaturcaraan'],
    $_POST['pangkalan_data'],
    $_POST['rangkaian'],
    $_POST['integrasi'],
    $_POST['id_kaedahpembangunan'],

    // inhouse OR null
    ($_POST['inhouse'] !== '' ? $_POST['inhouse'] : null),

    // id_pembekal OR null
    ($id_pembekal ?: null),

    $_POST['tarikh_dibeli'],
    $_POST['tempoh_jaminan_sistem'],
    $_POST['expired_jaminan_sistem'],
    $_POST['id_penyelenggaraan'],
    $_POST['kos_keseluruhan'],
    $_POST['kos_perkakasan'],
    $_POST['kos_perisian'],
    $_POST['kos_lesen_perisian'],
    $_POST['kos_penyelenggaraan'],
    $_POST['kos_lain'],
    $_POST['id_kategoriuser'],
    $_POST['pengurus_akses'],

    $id
]);

// DONE
header("Location: view_sistem.php?id=$id&updated=1");
exit;

?>
