<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// Validate
if (!isset($_POST['id_profilsistem'])) {
    die("Invalid access");
}

$id = intval($_POST['id_profilsistem']);

/* ======================================================
   1. UPDATE MAKLUMAT PROFIL (TABLE: PROFIL)
====================================================== */
$sqlProfil = "
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
";

$stmt = $pdo->prepare($sqlProfil);
$stmt->execute([
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


/* ======================================================
   2. HANDLE KAEDAH PEMBANGUNAN (INHOUSE / PEMBEKAL)
====================================================== */

// DEFAULT VALUE
$inhouse = null;
$id_pembekal = null;

// Pilihan kaedah
$kaedah = $_POST['id_kaedahpembangunan'];

// --- KALAU INHOUSE ---
if ($kaedah == 1) {  // assume 1 = inhouse
    $inhouse = $_POST['inhouse'];
}

// --- KALAU PEMBEKAL ---
if ($kaedah == 2) {  // assume 2 = pembekal

    // 2.1 — KALAU PILIH PEMBEKAL SEDIA ADA
    if (!empty($_POST['id_pembekal']) && $_POST['is_new_supplier'] == 0) {

        $id_pembekal = $_POST['id_pembekal'];

        // UPDATE PEMBEKAL SEDIA ADA
        $sqlUpdatePemb = "
            UPDATE lookup_pembekal SET
                nama_syarikat = ?,
                tempoh_kontrak = ?,
                alamat_syarikat = ?
            WHERE id_pembekal = ?
        ";
        $stmt = $pdo->prepare($sqlUpdatePemb);
        $stmt->execute([
            $_POST['edit_nama_syarikat'],
            $_POST['edit_tempoh_kontrak'],
            $_POST['edit_alamat_syarikat'],
            $id_pembekal
        ]);

        // UPDATE PIC
        if (!empty($_POST['existing_id_PIC'])) {
            $sqlUpdatePIC = "
                UPDATE lookup_PIC SET
                    nama_PIC = ?,
                    emel_PIC = ?,
                    notelefon_PIC = ?,
                    fax_PIC = ?,
                    jawatan_PIC = ?
                WHERE id_PIC = ?
            ";
            $stmt = $pdo->prepare($sqlUpdatePIC);
            $stmt->execute([
                $_POST['edit_nama_PIC'],
                $_POST['edit_emel_PIC'],
                $_POST['edit_notelefon_PIC'],
                $_POST['edit_fax_PIC'],
                $_POST['edit_jawatan_PIC'],
                $_POST['existing_id_PIC']
            ]);
        }
    }

    // 2.2 — DAFTAR PEMBEKAL BARU
    else if ($_POST['is_new_supplier'] == 1) {

        // Insert PIC
        $sqlPIC = "
            INSERT INTO lookup_PIC (nama_PIC, emel_PIC, notelefon_PIC, fax_PIC, jawatan_PIC)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt = $pdo->prepare($sqlPIC);
        $stmt->execute([
            $_POST['nama_PIC_baru'],
            $_POST['emel_PIC_baru'],
            $_POST['notelefon_PIC_baru'],
            $_POST['fax_PIC_baru'],
            $_POST['jawatan_PIC_baru']
        ]);
        $newPicID = $pdo->lastInsertId();

        // Insert Pembekal Baru
        $sqlPemb = "
            INSERT INTO lookup_pembekal (nama_syarikat, tempoh_kontrak, alamat_syarikat, id_PIC)
            VALUES (?, ?, ?, ?)
        ";
        $stmt = $pdo->prepare($sqlPemb);
        $stmt->execute([
            $_POST['nama_syarikat_baru'],
            $_POST['tempoh_kontrak_baru'],
            $_POST['alamat_syarikat_baru'],
            $newPicID
        ]);

        $id_pembekal = $pdo->lastInsertId();
    }
}


/* ======================================================
   3. UPDATE MAKLUMAT SISTEM (TABLE: SISTEM)
====================================================== */

$sqlSistem = "
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
        id_pembekal = ?
    WHERE id_profilsistem = ?
";

$stmt = $pdo->prepare($sqlSistem);
$stmt->execute([
    $_POST['nama_sistem'],
    $_POST['id_pemilik_sistem'],
    $_POST['objektif_sistem'],
    $_POST['tarikh_mula'] ?: null,
    $_POST['tarikh_siap'] ?: null,
    $_POST['tarikh_guna'] ?: null,
    $_POST['id_kategori'],
    $_POST['bil_pengguna'],
    $_POST['bil_modul'],
    $_POST['bahasa_pengaturcaraan'],
    $_POST['pangkalan_data'],
    $_POST['rangkaian'],
    $_POST['integrasi'],
    $kaedah,
    $inhouse,
    $id_pembekal,
    $id
]);

header("Location: profil_sistem.php?kemaskini=berjaya");
exit;

?>
