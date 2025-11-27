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
$userprofiles = $pdo->query("SELECT * FROM LOOKUP_USERPROFILE ORDER BY nama_user ASC")->fetchAll(PDO::FETCH_ASSOC);
$cartas = $pdo->query("SELECT * FROM LOOKUP_CARTA ORDER BY carta ASC")->fetchAll(PDO::FETCH_ASSOC);
$pics = $pdo->query("SELECT * FROM LOOKUP_PIC")->fetchAll(PDO::FETCH_ASSOC);
$jenisperalatans = $pdo->query("SELECT * FROM LOOKUP_JENISPERALATAN")->fetchAll(PDO::FETCH_ASSOC);
$pembekals = $pdo->query("SELECT * FROM LOOKUP_PEMBEKAL")->fetchAll(PDO::FETCH_ASSOC);
$kategoriusers = $pdo->query("SELECT * FROM LOOKUP_KATEGORIUSER")->fetchAll(PDO::FETCH_ASSOC);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['userlog']['id_userlog'])) {
    throw new Exception("User not logged in.");
}
$id_userlog = $_SESSION['userlog']['id_userlog'];

// ID Jenis Profil untuk SISTEM
$id_jenisprofil_sistem = 1;
$id_jenisprofil_peralatan = 2;
$id_jenisprofil_pengguna = 4;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenisprofil_post = $_POST['jenisprofil'] ?? null;

    try {
        $pdo->beginTransaction();

        // ------------------------------------
        // LOGIK UNTUK SISTEM (ID 1)
        // ------------------------------------
        if ($jenisprofil_post == $id_jenisprofil_sistem) {   

            $id_pembekal = $_POST['id_pembekal'] ?? null;
            $id_kaedahpembangunan = $_POST['id_kaedahpembangunan'] ?? null;
            $inhouse = $_POST['inhouse'] ?? null; // Dari borang kaedah Dalaman
            $inhouse_luaran = $_POST['inhouse_luaran'] ?? null; // Dari borang kaedah Luaran

            // **********************************************
            // 0. LOGIK UNTUK PEMBEKAL BARU (Jika dipilih)
            // **********************************************
            if ($id_pembekal === 'NEW_SUPPLIER') {
                $nama_syarikat_baru = trim($_POST['nama_syarikat_baru'] ?? '');
                $emel_PIC_baru = trim($_POST['emel_PIC_baru'] ?? '');
                $nama_PIC_baru = trim($_POST['nama_PIC_baru'] ?? '');
                
                if (!$nama_syarikat_baru || !$emel_PIC_baru || !$nama_PIC_baru) {
                    throw new Exception("Sila lengkapkan maklumat Nama Syarikat, Nama PIC, dan Emel PIC untuk Pembekal Baharu.");
                }

                // Cek duplikasi Emel PIC
                $stmtCheckPic = $pdo->prepare("SELECT id_PIC FROM LOOKUP_PIC WHERE emel_PIC = ?");
                $stmtCheckPic->execute([$emel_PIC_baru]);
                if ($stmtCheckPic->fetch()) {
                    throw new Exception("Emel PIC Pembekal sudah wujud: $emel_PIC_baru");
                }
                
                // 0a. Insert ke LOOKUP_PIC
                $stmt_pic = $pdo->prepare("
                    INSERT INTO LOOKUP_PIC (nama_PIC, emel_PIC, notelefon_PIC, fax_PIC, jawatan_PIC)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt_pic->execute([
                    $nama_PIC_baru,
                    $emel_PIC_baru,
                    $_POST['notelefon_PIC_baru'] ?: null,
                    $_POST['fax_PIC_baru'] ?: null,
                    $_POST['jawatan_PIC_baru'] ?: null
                ]);
                $id_PIC_baru = $pdo->lastInsertId();

                // 0b. Insert ke LOOKUP_PEMBEKAL
                $stmt_pembekal = $pdo->prepare("
                    INSERT INTO LOOKUP_PEMBEKAL (nama_syarikat, alamat_syarikat, tempoh_kontrak, id_PIC)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt_pembekal->execute([
                    $nama_syarikat_baru,
                    $_POST['alamat_syarikat_baru'] ?: null,
                    $_POST['tempoh_kontrak_baru'] ?: null,
                    $id_PIC_baru
                ]);
                
                // Gantikan 'NEW_SUPPLIER' dengan id_pembekal yang baru
                $id_pembekal = $pdo->lastInsertId();
            }
            

            // **********************************************
            // 1. DATA PROFIL & SISTEM SELEPAS PRE-PROCESSING
            // **********************************************
            
            // Tentukan nilai inhouse yang betul berdasarkan Kaedah Pembangunan
            // Jika Dalaman (id_kaedahpembangunan=1), guna $inhouse. Jika Luaran (id_kaedahpembangunan!=1), nilai $inhouse=null
            $inhouse_value = ($id_kaedahpembangunan == 1) ? $inhouse : null;
            
            // Jika Luaran (id_kaedahpembangunan!=1), set $id_pembekal. Jika Dalaman, $id_pembekal=null
            $pembekal_value = ($id_kaedahpembangunan != 1) ? $id_pembekal : null;

            // Data PROFIL (Ambil dari form_sistem.php)
            $id_status = $_POST['id_status'] ?? null;
            $nama_entiti = trim($_POST['nama_entiti'] ?? '');
            $alamat_pejabat = trim($_POST['alamat_pejabat'] ?? '');
            $id_bahagianunit = $_POST['id_bahagianunit'] ?? null;
            $tarikh_kemaskini = date('Y-m-d'); 
            $nama_ketua = $_POST['nama_ketua'] ?? null;
            $nama_cio = $_POST['nama_cio'] ?? null;
            $nama_ictso = $_POST['nama_ictso'] ?? null;
            $id_carta = $_POST['id_carta'] ?? null;
            
            // Validation PROFIL
            if (!$id_status || !$nama_entiti || !$id_bahagianunit || !$nama_ketua || !$nama_cio || !$nama_ictso) {
                throw new Exception("Sila pastikan semua medan 'Maklumat Profil' yang wajib diisi (Status, Nama Entiti, Bahagian/Unit, Ketua, CIO, ICTSO) untuk Profil Sistem.");
            }

            // 1. Insert ke PROFIL
            $stmt_profil = $pdo->prepare("
                INSERT INTO PROFIL
                (id_userlog, id_jenisprofil, id_status, nama_entiti, alamat_pejabat, id_bahagianunit, tarikh_kemaskini, nama_ketua, nama_cio, nama_ictso, id_carta, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt_profil->execute([
                $id_userlog,
                $id_jenisprofil_sistem,
                $id_status,
                $nama_entiti,
                $alamat_pejabat ?: null,
                $id_bahagianunit,
                $tarikh_kemaskini,
                $nama_ketua,
                $nama_cio,
                $nama_ictso,
                $id_carta ?: null
            ]);
            $id_profilsistem = $pdo->lastInsertId();

            // Data SISTEM (Ambil dari form_sistem.php)
            $nama_sistem = trim($_POST['nama_sistem'] ?? '');
            $objektif_sistem = trim($_POST['objektif_sistem'] ?? '');
            $id_pemilik_sistem = $_POST['id_pemilik_sistem'] ?? null;
            $tarikh_mula = $_POST['tarikh_mula'] ?: null;
            $tarikh_siap = $_POST['tarikh_siap'] ?: null;
            $tarikh_guna = $_POST['tarikh_guna'] ?: null;
            $bil_pengguna = trim($_POST['bil_pengguna'] ?? '');
            $bil_modul = trim($_POST['bil_modul'] ?? '');
            $id_kategori = $_POST['id_kategori'] ?? null;
            $bahasa_pengaturcaraan = trim($_POST['bahasa_pengaturcaraan'] ?? '');
            $pangkalan_data = trim($_POST['pangkalan_data'] ?? '');
            $rangkaian = trim($_POST['rangkaian'] ?? '');
            $integrasi = trim($_POST['integrasi'] ?? '');
            // $id_kaedahpembangunan sudah diambil di atas
            $id_penyelenggaraan = $_POST['id_penyelenggaraan'] ?? null;
            $tarikh_dibeli = $_POST['tarikh_dibeli'] ?: null;
            $tempoh_jaminan_sistem = trim($_POST['tempoh_jaminan_sistem'] ?? '');
            $expired_jaminan_sistem = $_POST['expired_jaminan_sistem'] ?: null;
            $kos_keseluruhan = $_POST['kos_keseluruhan'] ?: null;
            $kos_perkakasan = $_POST['kos_perkakasan'] ?: null;
            $kos_perisian = $_POST['kos_perisian'] ?: null;
            $kos_lesen_perisian = $_POST['kos_lesen_perisian'] ?: null;
            $kos_penyelenggaraan = $_POST['kos_penyelenggaraan'] ?: null;
            $kos_lain = $_POST['kos_lain'] ?: null;
            $id_kategoriuser = $_POST['id_kategoriuser'] ?? null;
            $pengurus_akses = $_POST['pengurus_akses'] ?? null;
            $pegawai_rujukan_sistem = $_POST['pegawai_rujukan_sistem'] ?? null;
            
            // Validation SISTEM
            // Validation id_penyelenggaraan hanya jika bukan dalaman (ID 1)
            if (!$nama_sistem || !$id_pemilik_sistem || !$id_kategori || !$id_kaedahpembangunan || !$id_kategoriuser || !$pengurus_akses || !$pegawai_rujukan_sistem) {
                throw new Exception("Sila pastikan semua medan 'Maklumat Sistem' yang wajib diisi (bahagian I dan II).");
            }
            if ($id_kaedahpembangunan != 1 && !$id_penyelenggaraan) {
                throw new Exception("Sila pilih Jenis Penyelenggaraan untuk Kaedah Pembangunan Luaran.");
            }
            if ($id_kaedahpembangunan == 1 && !$inhouse_value) {
                 throw new Exception("Sila pilih Bahagian/Unit Inhouse untuk Kaedah Pembangunan Dalaman.");
            }

            // 2. Insert ke SISTEM (Guna $pembekal_value dan $inhouse_value yang telah diproses)
            $stmt_sistem = $pdo->prepare("
                INSERT INTO SISTEM
                (id_profilsistem, nama_sistem, objektif_sistem, id_pemilik_sistem, tarikh_mula, tarikh_siap, tarikh_guna, bil_pengguna, bil_modul, id_kategori, bahasa_pengaturcaraan, pangkalan_data, rangkaian, integrasi, id_kaedahpembangunan, id_pembekal, inhouse, id_penyelenggaraan, tarikh_dibeli, tempoh_jaminan_sistem, expired_jaminan_sistem, kos_keseluruhan, kos_perkakasan, kos_perisian, kos_lesen_perisian, kos_penyelenggaraan, kos_lain, id_kategoriuser, pengurus_akses, pegawai_rujukan_sistem)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt_sistem->execute([
                $id_profilsistem,
                $nama_sistem,
                $objektif_sistem ?: null,
                $id_pemilik_sistem,
                $tarikh_mula,
                $tarikh_siap,
                $tarikh_guna,
                $bil_pengguna ?: null,
                $bil_modul ?: null,
                $id_kategori,
                $bahasa_pengaturcaraan ?: null,
                $pangkalan_data ?: null,
                $rangkaian ?: null,
                $integrasi ?: null,
                $id_kaedahpembangunan,
                $pembekal_value, // Guna nilai yang telah diproses
                $inhouse_value, // Guna nilai yang telah diproses
                $id_penyelenggaraan,
                $tarikh_dibeli,
                $tempoh_jaminan_sistem ?: null,
                $expired_jaminan_sistem,
                $kos_keseluruhan ?: null,
                $kos_perkakasan ?: null,
                $kos_perisian ?: null,
                $kos_lesen_perisian ?: null,
                $kos_penyelenggaraan ?: null,
                $kos_lain ?: null,
                $id_kategoriuser,
                $pengurus_akses,
                $pegawai_rujukan_sistem
            ]);

            $pdo->commit();
            $alert_type = "success";
            $alert_message = "Profil Sistem **$nama_sistem** berjaya direkodkan! (ID Profil: $id_profilsistem)";
            goto end_of_post;
        }


        // ------------------------------------
        // LOGIK UNTUK PENGGUNA (ID 4)
        // ------------------------------------
        if ($jenisprofil_post == $id_jenisprofil_pengguna) { // 4 = pengguna
            // ... (Logik penuh PENGGUNA kekal SAMA) ...

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


        // Jika Jenis Profil tidak dipilih
        if (!$jenisprofil_post) {
            $alert_type = "warning";
            $alert_message = "Sila pilih Jenis Profil yang ingin didaftarkan.";
        }


    } catch(PDOException $e) {
        $pdo->rollBack();
        $alert_type = "danger";
        $alert_message = "Ralat Pangkalan Data: " . $e->getMessage();
    } catch(Exception $e) {
        $pdo->rollBack();
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
            
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label>Jenis Profil <span class="text-danger">*</span></label>
                    <select name="jenisprofil" id="jenisProfil" class="form-select" required>
                        <option value="">-- Pilih Jenis Profil --</option>
                        <?php foreach ($jenisprofils as $jp): ?>
                            <?php if ($jp['id_jenisprofil'] == 3) continue; // skip Pembekal (ID 3) buat masa ini ?>
                            <option value="<?= $jp['id_jenisprofil'] ?>"
                                <?= (isset($jenisprofil_post) && $jenisprofil_post == $jp['id_jenisprofil']) ? 'selected' : '' ?>>
                                <?= $jp['jenisprofil'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="maklumatProfilContainer" style="display:none;">
                <?php 
                    // Kita perlu include fail berasingan yang mengandungi hanya Maklumat Profil, 
                    // tetapi buat masa ini kita ambil sahaja dari form_sistem.php dan uruskan paparan
                    // melalui JS.
                    // Untuk form_sistem.php yang anda berikan, ia sudah mengandungi Maklumat Profil.
                    // Kita akan ubahsuai form_sistem.php dan form_pengguna.php untuk memudahkan.
                ?>
            </div>

            <div id="formSistem" style="display:none;">
                <?php include 'forms/form_sistem.php'; ?>
            </div>  

            <div id="formPeralatan" style="display:none;">
                <?php // Anda perlu cipta fail 'forms/form_peralatan.php' ?>
                <?php // include 'forms/form_peralatan.php'; ?>
                <div class="alert alert-warning">Borang Peralatan belum tersedia.</div>
            </div>        

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
                // ID Jenis Profil: Element Borang
                '1': document.getElementById('formSistem'),      // Sistem
                '2': document.getElementById('formPeralatan'),  // Peralatan
                '4': document.getElementById('formPengguna')    // Pengguna
            };

            // Dapatkan semua elemen input/select dalam borang SISTEM dan PENGGUNA
            const sistemInputs = forms['1'] ? forms['1'].querySelectorAll('input, select, textarea') : [];
            const penggunaInputs = forms['4'] ? forms['4'].querySelectorAll('input, select, textarea') : [];

            function toggleForms() {
                const selectedId = jenisSelect.value;
                
                // Sembunyikan semua borang dan tetapkan 'disabled'
                Object.values(forms).forEach(f => {
                    if (f) {
                        f.style.display = 'none';
                        f.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
                    }
                });

                // Tunjukkan borang yang dipilih dan tetapkan 'enabled'
                if(forms[selectedId]) {
                    forms[selectedId].style.display = 'block';
                    forms[selectedId].querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
                    
                    // Trigger logik kaedah pembangunan untuk borang sistem jika ia dipilih (ID 1)
                    if (selectedId === '1') {
                        // Semak status awal Kaedah Pembangunan dan Pembekal Baharu
                        checkKaedahPembangunan(true); 
                    }
                }
            }

            jenisSelect.addEventListener('change', toggleForms);

            // Jalankan sekali pada permulaan (untuk paparan selepas POST)
            // Simpan nilai yang dipilih jika ada
            const initialSelectedId = jenisSelect.value;
            if (initialSelectedId) {
                // Gunakan logik toggleForms untuk memaparkan borang yang dipilih
                toggleForms();
            }

            // --- Logik Sistem Khusus (Kawalan Kaedah Pembangunan & Pembekal) ---
            const kaedahSelect = document.getElementById('id_kaedahpembangunan');
            const inhouseContainer = document.getElementById('pembangunanInhouseContainer'); // Kaedah Dalaman (ID 1)
            const luarContainer = document.getElementById('pembangunanLuarContainer');   // Kaedah Pembekal (ID > 1)
            const inhouseDalamanSelect = document.getElementById('inhouse_dalaman'); 
            const pembekalSelect = document.getElementById('id_pembekal');
            const pembekalRequiredSpan = document.getElementById('pembekal_required'); 
            
            // Input lain yang wajib diisi dalam Maklumat Pembangunan, Kos, dan Pengurusan Pengguna
            const requiredPenyelenggaraan = document.getElementById('id_penyelenggaraan'); 
            const requiredKategoriUser = document.getElementById('id_kategoriuser');
            const requiredPengurusAkses = document.getElementById('pengurus_akses');
            const requiredPegawaiRujukan = document.getElementById('pegawai_rujukan_sistem');
            const allRequiredInputs = [
                requiredPenyelenggaraan, 
                requiredKategoriUser,
                requiredPengurusAkses,
                requiredPegawaiRujukan
            ];

            function checkKaedahPembangunan(isInitialLoad = false) {
                const selectedKaedah = kaedahSelect.value;
                const isDalaman = selectedKaedah === '1';
                
                // 1. Kawal Required Inputs
                allRequiredInputs.forEach(el => {
                    // Semua wajib hanya jika Kaedah Pembangunan (dan keseluruhan borang sistem) dipilih
                    if (selectedKaedah && jenisSelect.value === '1') {
                        el.setAttribute('required', 'required');
                        el.disabled = false;
                    } else {
                        el.removeAttribute('required');
                        // Jika Kaedah tidak dipilih, biarkan required Inputs di-disabled (dikawal oleh toggleForms jika Jenis Profil bertukar)
                    }
                });

                // 2. Kawal Paparan Inhouse vs Luaran
                if (!selectedKaedah) {
                    inhouseContainer.style.display = 'none';
                    luarContainer.style.display = 'none';
                    inhouseDalamanSelect.disabled = true;
                    pembekalSelect.disabled = true;
                    pembekalSelect.removeAttribute('required');
                    pembekalRequiredSpan.style.display = 'none';
                    checkNewPembekal(true); // Sembunyikan borang pembekal baharu
                    return;
                }

                // 3. Logik Khusus Kaedah Pembangunan
                if (isDalaman) {
                    // Dalaman (ID 1): Aktifkan Inhouse Bahagian/Unit
                    inhouseContainer.style.display = 'block';
                    luarContainer.style.display = 'none';
                    
                    inhouseDalamanSelect.disabled = false; 

                    pembekalSelect.disabled = true;
                    pembekalSelect.removeAttribute('required');
                    pembekalRequiredSpan.style.display = 'none';

                    checkNewPembekal(true); // Pastikan borang pembekal baharu disembunyikan/disabled

                } else {
                    // Luaran (ID > 1): Aktifkan Pembekal
                    inhouseContainer.style.display = 'none';
                    luarContainer.style.display = 'block';
                    
                    inhouseDalamanSelect.disabled = true;

                    pembekalSelect.disabled = false;
                    pembekalSelect.setAttribute('required', 'required'); // Pembekal wajib untuk Kaedah Luaran
                    pembekalRequiredSpan.style.display = 'inline';

                    checkNewPembekal(); // Semak jika "Pembekal Baharu" dipilih
                }
            }

            kaedahSelect.addEventListener('change', checkKaedahPembangunan);
        
            // --- Logik Pembekal Baharu/PIC ---
            const newPembekalFormDiv = document.getElementById('newPembekalForm');
            const newPembekalInputs = newPembekalFormDiv ? newPembekalFormDiv.querySelectorAll('input, select') : [];
            const isNewSupplierHiddenInput = document.getElementById('is_new_supplier');

            function hideNewPembekalForm(isDalaman = false) {
                newPembekalFormDiv.style.display = 'none';
                isNewSupplierHiddenInput.value = '0';
                
                newPembekalInputs.forEach(el => {
                    el.disabled = true;
                    el.removeAttribute('required');
                });
                
                // Jika bukan Dalaman, pastikan #id_pembekal diaktifkan dan required
                if (!isDalaman && kaedahSelect.value && kaedahSelect.value !== '1') {
                    pembekalSelect.disabled = false;
                    pembekalSelect.setAttribute('required', 'required');
                    pembekalRequiredSpan.style.display = 'inline';
                }
            }

            function showNewPembekalForm() {

                newPembekalFormDiv.style.display = 'flex';

                // Tambah console log untuk debugging
                console.log("showNewPembekalForm dipanggil. newPembekalFormDiv dipaparkan.");

                isNewSupplierHiddenInput.value = '1';

                // Aktifkan dan jadikan required untuk field Pembekal Baharu
                newPembekalInputs.forEach(el => {
                    el.disabled = false;
                    
                    // Tetapkan required hanya pada field wajib (Nama Syarikat, Nama PIC, Emel PIC)
                    const isRequired = ['nama_syarikat_baru', 'nama_PIC_baru', 'emel_PIC_baru'].includes(el.name);
                    
                    if (isRequired) {
                        el.setAttribute('required', 'required');
                    } else {
                        el.removeAttribute('required');
                    }
                });
                
                // Disable field pembekal sedia ada (dropdown) untuk elak konflik input
                pembekalSelect.disabled = true;
                pembekalSelect.removeAttribute('required');
                pembekalRequiredSpan.style.display = 'none'; // Required berpindah ke field baru
            }

            function checkNewPembekal(isDalaman = false) {
                if (isDalaman) {
                    hideNewPembekalForm(true);
                    return;
                }

                const selectedPembekal = pembekalSelect.value;
                const isNew = selectedPembekal === 'NEW_SUPPLIER';
                
                console.log("Pembekal selected: " + selectedPembekal + " (isNew: " + isNew + ")"); // DEBUG

                if (isNew) {
                    showNewPembekalForm();
                } else {
                    hideNewPembekalForm(false);
                }
            }

            pembekalSelect.addEventListener('change', checkNewPembekal);

            // Semak status Kaedah Pembangunan/Pembekal Baharu pada permulaan jika ada nilai tersimpan
            if (kaedahSelect && jenisSelect.value === '1') {
                checkKaedahPembangunan(true); 
            }

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const dropdownPembekal = document.getElementById("id_pembekal");
            const newPembekalForm = document.getElementById("newPembekalForm");
            const isNewSupplier = document.getElementById("is_new_supplier");

            // Semua input dalam new supplier form
            const newInputs = [
                "nama_syarikat_baru",
                "tempoh_kontrak_baru",
                "alamat_syarikat_baru",
                "nama_PIC_baru",
                "jawatan_PIC_baru",
                "emel_PIC_baru",
                "notelefon_PIC_baru",
                "fax_PIC_baru"
            ];

            function toggleNewSupplierForm() {
                if (dropdownPembekal.value === "NEW_SUPPLIER") {
                    newPembekalForm.style.display = "flex";
                    isNewSupplier.value = "1";

                    newInputs.forEach(id => {
                        document.getElementById(id).disabled = false;
                    });

                } else {
                    newPembekalForm.style.display = "none";
                    isNewSupplier.value = "0";

                    newInputs.forEach(id => {
                        document.getElementById(id).disabled = true;
                        document.getElementById(id).value = "";
                    });
                }
            }

            dropdownPembekal.addEventListener("change", toggleNewSupplierForm);
        });
    </script>

</body>
</html>
