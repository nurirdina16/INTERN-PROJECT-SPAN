<?php

require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);

$alert_type = '';
$alert_message = '';

// Fetch lookups
$statuses = $pdo->query("SELECT * FROM LOOKUP_STATUS")->fetchAll(PDO::FETCH_ASSOC);
$jenisprofils = $pdo->query("SELECT * FROM LOOKUP_JENISPROFIL")->fetchAll(PDO::FETCH_ASSOC);
$bahagianunits = $pdo->query("SELECT * FROM LOOKUP_BAHAGIANUNIT ORDER BY bahagianunit ASC")->fetchAll(PDO::FETCH_ASSOC);
$kategoris = $pdo->query("SELECT * FROM LOOKUP_KATEGORI")->fetchAll(PDO::FETCH_ASSOC);
$penyelenggaraans = $pdo->query("SELECT * FROM LOOKUP_PENYELENGGARAAN")->fetchAll(PDO::FETCH_ASSOC);
$kaedahPembangunans = $pdo->query("SELECT * FROM LOOKUP_KAEDAHPEMBANGUNAN")->fetchAll(PDO::FETCH_ASSOC);
$userprofiles = $pdo->query("SELECT * FROM LOOKUP_USERPROFILE")->fetchAll(PDO::FETCH_ASSOC);
$cartas = $pdo->query("SELECT * FROM LOOKUP_CARTA ORDER BY carta ASC")->fetchAll(PDO::FETCH_ASSOC);
$pics = $pdo->query("SELECT * FROM LOOKUP_PIC")->fetchAll(PDO::FETCH_ASSOC);
$jenisperalatans = $pdo->query("SELECT * FROM LOOKUP_JENISPERALATAN")->fetchAll(PDO::FETCH_ASSOC);
$pembekals = $pdo->query("SELECT * FROM LOOKUP_PEMBEKAL")->fetchAll(PDO::FETCH_ASSOC);
$kategoriusers = $pdo->query("SELECT * FROM LOOKUP_KATEGORIUSER")->fetchAll(PDO::FETCH_ASSOC);

$id_userlog = $_SESSION['userlog']['id_userlog'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenisprofil_post = $_POST['jenisprofil'] ?? null;

    try {
        $pdo->beginTransaction();

        // 1) PENGGUNA (only lookup_userprofile)
        if ($jenisprofil_post == 4) { // 4 = pengguna
            $nama_user       = trim($_POST['nama_user'] ?? '');
            $jawatan_user    = trim($_POST['jawatan_user'] ?? '');
            $emel_user       = trim($_POST['emel_user'] ?? '');
            $notelefon_user  = trim($_POST['notelefon_user'] ?? '');
            $fax_user        = trim($_POST['fax_user'] ?? '');
            $id_bahagianunit = $_POST['id_bahagianunit'] ?? null;

            if (!$nama_user || !$emel_user) {
                throw new Exception("Nama dan Emel pengguna wajib diisi.");
            }

            // check duplicate email
            $stmtCheck = $pdo->prepare("SELECT id_userprofile FROM lookup_userprofile WHERE emel_user = ?");
            $stmtCheck->execute([$emel_user]);
            if ($stmtCheck->fetch()) {
                throw new Exception("Emel pengguna sudah wujud: $emel_user");
            }

            // insert user only
            $stmt = $pdo->prepare("
                INSERT INTO lookup_userprofile
                (nama_user, jawatan_user, emel_user, notelefon_user, fax_user, id_bahagianunit)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $nama_user,
                $jawatan_user ?: null,
                $emel_user,
                $notelefon_user ?: null,
                $fax_user ?: null,
                $id_bahagianunit ?: null
            ]);

            $pdo->commit();
            $alert_type = "success";
            $alert_message = "Pengguna berjaya direkodkan!";
            goto end_of_post;
        }


        // 2) SISTEM: insert into PROFIL then SISTEM
        if ($jenisprofil_post == 1) {   // 1 = Sistem
            try {
                $pdo->beginTransaction();

                // 1) Insert PROFIL
                $stmtProfil = $pdo->prepare("
                    INSERT INTO PROFIL
                    (id_jenisprofil, id_status, nama_entiti, alamat_pejabat, id_bahagianunit,
                    tarikh_kemaskini, nama_ketua, nama_cio, nama_ictso,
                    id_carta, created_by, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $tarikh_kemaskini = $_POST['tarikh_kemaskini'] ?? date('Y-m-d');

                $stmtProfil->execute([
                    $_POST['jenisprofil'],
                    $_POST['status'] ?? null,
                    $_POST['nama_entiti'] ?? null,
                    $_POST['alamat_pejabat'] ?? null,
                    $_POST['id_bahagianunit_entiti'] ?? null,
                    $tarikh_kemaskini,
                    $_POST['nama_ketua'] ?? null,
                    $_POST['nama_cio'] ?? null,
                    $_POST['nama_ictso'] ?? null,
                    $_POST['id_carta'] ?? null,
                    $id_userlog ?? null,
                    date('Y-m-d')
                ]);

                $id_profilsistem = $pdo->lastInsertId();

                // 2) Handle Kaedah Pembekal 'Other' → insert LOOKUP_PEMBEKAL & LOOKUP_PIC
                $id_pembekal = $_POST['id_pembekal'] ?? null;
                if ($_POST['id_kaedahpembangunan'] == 2 && $id_pembekal == 'other') {
                    // insert pembekal
                    $stmtPB = $pdo->prepare("INSERT INTO LOOKUP_PEMBEKAL (nama_syarikat, alamat_syarikat, tempoh_kontrak, id_PIC) VALUES (?, ?, ?, ?)");
                    // insert PIC
                    $stmtPIC = $pdo->prepare("INSERT INTO LOOKUP_PIC (nama_PIC, emel_PIC, notelefon_PIC, fax_PIC, jawatan_PIC) VALUES (?, ?, ?, ?, ?)");

                    $stmtPIC->execute([
                        $_POST['nama_PIC_manual'] ?? null,
                        $_POST['emel_PIC_manual'] ?? null,
                        $_POST['notelefon_PIC_manual'] ?? null,
                        $_POST['fax_PIC_manual'] ?? null,
                        $_POST['jawatan_PIC_manual'] ?? null
                    ]);
                    $id_pic = $pdo->lastInsertId();

                    $stmtPB->execute([
                        $_POST['nama_syarikat_manual'] ?? null,
                        $_POST['alamat_syarikat_manual'] ?? null,
                        $_POST['tempoh_kontrak_manual'] ?? null,
                        $id_pic
                    ]);
                    $id_pembekal = $pdo->lastInsertId();
                }

                // 3) Resolve id_kategoriuser
                $id_kategoriuser = $_POST['id_kategoriuser'] ?? null;
                $jenis_dalaman = $_POST['jenis_dalaman'] ?? null;
                $jenis_umum    = $_POST['jenis_umum'] ?? null;

                if(!$id_kategoriuser && ($jenis_dalaman !== null || $jenis_umum !== null)){
                    $jd = $jenis_dalaman ? 1 : 0;
                    $ju = $jenis_umum ? 1 : 0;

                    $chk = $pdo->prepare("SELECT id_kategoriuser FROM LOOKUP_KATEGORIUSER WHERE jenis_dalaman=? AND jenis_umum=? LIMIT 1");
                    $chk->execute([$jd,$ju]);
                    $row = $chk->fetch(PDO::FETCH_ASSOC);
                    if($row) $id_kategoriuser = $row['id_kategoriuser'];
                    else{
                        $ins = $pdo->prepare("INSERT INTO LOOKUP_KATEGORIUSER (jenis_dalaman, jenis_umum) VALUES (?,?)");
                        $ins->execute([$jd,$ju]);
                        $id_kategoriuser = $pdo->lastInsertId();
                    }
                }

                // 4) Insert SISTEM
                $stmtSistem = $pdo->prepare("
                    INSERT INTO SISTEM
                    (id_profilsistem, nama_sistem, objektif_sistem, id_pemilik_sistem,
                    tarikh_mula, tarikh_siap, tarikh_guna,
                    bil_pengguna, bil_modul, id_kategori,
                    bahasa_pengaturcaraan, pangkalan_data, rangkaian, integrasi,
                    id_kaedahpembangunan, id_pembekal, inhouse,
                    id_penyelenggaraan, tarikh_dibeli,
                    tempoh_jaminan_sistem, expired_jaminan_sistem,
                    kos_keseluruhan, kos_perkakasan, kos_perisian,
                    kos_lesen_perisian, kos_penyelenggaraan, kos_lain,
                    id_kategoriuser, pengurus_akses, pegawai_rujukan_sistem)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $stmtSistem->execute([
                    $id_profilsistem,
                    $_POST['nama_sistem'] ?? null,
                    $_POST['objektif_sistem'] ?? null,
                    $_POST['id_pemilik_sistem'] ?? null,
                    $_POST['tarikh_mula'] ?? null,
                    $_POST['tarikh_siap'] ?? null,
                    $_POST['tarikh_guna'] ?? null,
                    $_POST['bil_pengguna'] ?? null,
                    $_POST['bil_modul'] ?? null,
                    $_POST['id_kategori'] ?? null,
                    $_POST['bahasa_pengaturcaraan'] ?? null,
                    $_POST['pangkalan_data'] ?? null,
                    $_POST['rangkaian'] ?? null,
                    $_POST['integrasi'] ?? null,
                    $_POST['id_kaedahpembangunan'] ?? null,
                    $id_pembekal,
                    $_POST['inhouse'] ?? null,
                    $_POST['id_penyelenggaraan'] ?? null,
                    $_POST['tarikh_dibeli'] ?? null,
                    $_POST['tempoh_jaminan_sistem'] ?? null,
                    $_POST['expired_jaminan_sistem'] ?? null,
                    $_POST['kos_keseluruhan'] ?? null,
                    $_POST['kos_perkakasan'] ?? null,
                    $_POST['kos_perisian'] ?? null,
                    $_POST['kos_lesen_perisian'] ?? null,
                    $_POST['kos_penyelenggaraan'] ?? null,
                    $_POST['kos_lain'] ?? null,
                    $id_kategoriuser ?? null,
                    $_POST['pengurus_akses'] ?? null,
                    $_POST['pegawai_rujukan_sistem'] ?? null
                ]);

                $pdo->commit();
                $alert_type = "success";
                $alert_message = "Maklumat Sistem berjaya direkodkan!";
            } catch(Exception $e){
                if($pdo->inTransaction()) $pdo->rollBack();
                $alert_type = "danger";
                $alert_message = "Ralat: ".$e->getMessage();
            }
        }




        // 3) PERALATAN
        if ($jenisprofil_post == 2) {
          
        }


        // If reached here no specific branch matched
        $pdo->commit();

    } catch (Exception $e) {
        // rollback and show error
        if ($pdo->inTransaction()) $pdo->rollBack();
        $alert_type = "danger";
        $alert_message = "Ralat: " . $e->getMessage();
    }
}

end_of_post:
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
    
    <link rel="stylesheet" href="css/daftarprofil.css">
    
    <script src="js/sidebar.js" defer></script> 
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <?php include 'header.php'; ?>

        <div class="main-header mt-4 mb-1"><i class="bi bi-pc-display"></i>Daftar Profil</div>

        <!-- Toast -->
        <div class="position-fixed top-0 end-0 p-3" style="z-index:1080;">
            <div id="liveToast" class="toast align-items-center text-bg-<?= $alert_type ?> border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><?= htmlspecialchars($alert_message) ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>

        <form method="POST" class="section-card">
            <!-- PROFIL PILIHAN -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label>Jenis Profil</label>
                    <select name="jenisprofil" id="jenisProfil" class="form-select" required>
                        <option value="">-- Pilih Jenis Profil --</option>
                        <?php foreach ($jenisprofils as $jp): ?>
                            <?php if ($jp['id_jenisprofil'] == 3) continue; // skip Pembekal ?>
                            <option value="<?= $jp['id_jenisprofil'] ?>"><?= $jp['jenisprofil'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- DB PROFIL -->
            <div class="section-title"><i class="bi bi-folder2-open"></i> MAKLUMAT PROFIL</div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label>Status</label>
                    <select name="status" class="form-select" required>
                        <option value="">-- Pilih Status --</option>
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s['id_status'] ?>"><?= $s['status'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Nama Entiti</label>
                    <input type="text" name="nama_entiti" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Alamat Pejabat</label>
                    <input type="text" name="alamat_pejabat" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Bahagian (Entiti)</label>
                    <select name="id_bahagianunit_entiti" class="form-select">
                        <option value="">-- Pilih Bahagian/Unit --</option>
                        <?php foreach ($bahagianunits as $b): ?>
                            <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Tarikh Kemaskini</label>
                    <input type="date" name="tarikh_kemaskini" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                    <label>Nama Ketua Bahagian</label>
                    <select name="nama_ketua" class="form-select">
                        <option value="">-- Pilih Ketua Bahagian --</option>
                        <?php foreach ($userprofiles as $u): ?>
                            <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Nama Chief Information Officer (CIO)</label>
                    <select name="nama_cio" class="form-select">
                        <option value="">-- Pilih CIO --</option>
                        <?php foreach ($userprofiles as $u): ?>
                            <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Nama Chief Security Officer (ICTSO)</label>
                    <select name="nama_ictso" class="form-select">
                        <option value="">-- Pilih ICTSO --</option>
                        <?php foreach ($userprofiles as $u): ?>
                            <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Carta Organisasi Entiti</label>
                    <select name="id_carta" class="form-select">
                        <option value="">-- Pilih Carta --</option>
                        <?php foreach ($cartas as $c): ?>
                            <option value="<?= $c['id_carta'] ?>"><?= $c['carta'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


            <!-- DB SISTEM -->
            <div id="formSistem" style="display:none;">
                <?php include 'forms/form_sistem.php'; ?>
            </div>

            <!-- DB PERALATAN -->
            <div id="formPeralatan" style="display:none;">
                <?php include 'forms/form_peralatan.php'; ?>
            </div>        

            <!-- DB_USERPROFILE -->
            <div id="formPengguna" style="display:none;">
                <?php include 'forms/form_pengguna.php'; ?>
            </div>
            

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Simpan Profil</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const toastEl = document.getElementById('liveToast');
            if(toastEl && toastEl.querySelector('.toast-body').textContent.trim() !== ''){
                new bootstrap.Toast(toastEl, { delay: 5000 }).show();
            }

            const jenisSelect = document.getElementById('jenisProfil');
            const forms = {
                1: document.getElementById('formSistem'),
                2: document.getElementById('formPeralatan'),
                3: document.getElementById('formPembekal'),
                4: document.getElementById('formPengguna')
            };

            jenisSelect.addEventListener('change', function(){
                Object.values(forms).forEach(f=>f.style.display='none');
                if(forms[this.value]) forms[this.value].style.display='block';
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jenisSelect = document.getElementById('jenisProfil');
            const forms = {
                1: document.getElementById('formSistem'),      // Sistem
                2: document.getElementById('formPeralatan'),  // Peralatan
                3: document.getElementById('formPembekal'),   // Pembekal
                4: document.getElementById('formPengguna')    // Pengguna
            };

            const profilSection = document.querySelector('.section-title'); // MAKLUMAT PROFIL title
            const profilFields = profilSection.nextElementSibling;           // MAKLUMAT PROFIL fields

            function toggleForms() {
                const value = jenisSelect.value;

                // Hide all specific forms
                Object.values(forms).forEach(f => {
                    if (f) f.style.display = 'none';
                });

                // Show selected form
                if(forms[value]) forms[value].style.display = 'block';

                // Only show MAKLUMAT PROFIL for Sistem (1) or Peralatan (2)
                if(value === '1' || value === '2'){
                    profilSection.style.display = 'block';
                    profilFields.style.display = 'block';
                    // set required attributes
                    profilFields.querySelectorAll('input, select').forEach(el => el.required = true);
                } else {
                    profilSection.style.display = 'none';
                    profilFields.style.display = 'none';
                    // remove required
                    profilFields.querySelectorAll('input, select').forEach(el => el.required = false);
                }
            }

            jenisSelect.addEventListener('change', toggleForms);

            // Run once on page load
            toggleForms();
        });
    </script>

</body>
</html>
