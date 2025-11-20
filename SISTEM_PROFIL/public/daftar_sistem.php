<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);
$message = '';

// Ambil data lookup dari DB
$statuses = $pdo->query("SELECT * FROM LOOKUP_STATUS")->fetchAll(PDO::FETCH_ASSOC);
$jenisprofils = $pdo->query("SELECT * FROM LOOKUP_JENISPROFIL")->fetchAll(PDO::FETCH_ASSOC);
$kaedahPembangunan = $pdo->query("SELECT * FROM LOOKUP_KAEDAHPEMBANGUNAN")->fetchAll(PDO::FETCH_ASSOC);
$bahagianUnits = $pdo->query("SELECT * FROM LOOKUP_BAHAGIANUNIT")->fetchAll(PDO::FETCH_ASSOC);
$kategoriSistem = $pdo->query("SELECT * FROM LOOKUP_KATEGORI")->fetchAll(PDO::FETCH_ASSOC);
$penyelenggara = $pdo->query("SELECT * FROM LOOKUP_PENYELENGGARAAN")->fetchAll(PDO::FETCH_ASSOC);
$outsources = $pdo->query("SELECT * FROM LOOKUP_OUTSOURCE")->fetchAll(PDO::FETCH_ASSOC);
$pics = $pdo->query("SELECT * FROM LOOKUP_PIC")->fetchAll(PDO::FETCH_ASSOC);
$kategoriUsers = $pdo->query("SELECT * FROM LOOKUP_KATEGORIUSER")->fetchAll(PDO::FETCH_ASSOC);
$userLogs = $pdo->query("SELECT id_user, nama_penuh FROM USERLOG")->fetchAll(PDO::FETCH_ASSOC);
$kategoriUser = $pdo->query("SELECT * FROM LOOKUP_KATEGORIUSER")->fetchAll(PDO::FETCH_ASSOC);
$userProfiles = $pdo->query("SELECT * FROM LOOKUP_USERPROFILE")->fetchAll(PDO::FETCH_ASSOC);
$cartas = $pdo->query("SELECT * FROM LOOKUP_CARTA")->fetchAll(PDO::FETCH_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ambil semua POST data
    $id_user = $_SESSION['id_user'];
    $id_status = $_POST['status'] ?? null;
    $id_jenisprofil = $_POST['jenisprofil'] ?? null;
    
    $nama_sistem = $_POST['nama_sistem'] ?? null;
    $id_kaedahPembangunan = $_POST['kaedahPembangunan'] ?? null;
    $objektif = $_POST['objektif'] ?? null;
    $id_bahagianunit = $_POST['bahagianunit'] ?? null;
    $tarikh_mula = $_POST['tarikh_mula'] ?? null;
    $tarikh_siap = $_POST['tarikh_siap'] ?? null;
    $tarikh_guna = $_POST['tarikh_guna'] ?? null;
    $bil_pengguna = $_POST['bil_pengguna'] ?? null;
    $bil_modul = $_POST['bil_modul'] ?? null;
    $id_kategori = $_POST['kategori'] ?? null;
    $bahasa_pengaturcaraan = $_POST['bahasa_pengaturcaraan'] ?? null;
    $pangkalan_data = $_POST['pangkalan_data'] ?? null;
    $rangkaian = $_POST['rangkaian'] ?? null;
    $integrasi = $_POST['integrasi'] ?? null;
    $id_penyelenggaraan = $_POST['penyelenggara'] ?? null;
    $id_outsource = $_POST['outsource'] ?? null;
    $id_bahagianInhouse = $_POST['bahagian_inhouse'] ?? null;

    $kos_keseluruhan = $_POST['kos_keseluruhan'] ?? 0;
    $kos_perkakasan = $_POST['kos_perkakasan'] ?? 0;
    $kos_perisian = $_POST['kos_perisian'] ?? 0;
    $kos_lesen_perisian = $_POST['kos_lesen_perisian'] ?? 0;
    $kos_penyelenggaraan = $_POST['kos_penyelenggaraan'] ?? 0;
    $kos_lain = $_POST['kos_lain'] ?? 0;

    $id_pegawaiakses = $_POST['pegawai_akses'] ?? null;
    $id_kategoriuser = $_POST['kategoriuser'] ?? null;
    $jenis_dalaman = $_POST['jenis_dalaman'] ?? null;
    $jenis_umum = $_POST['jenis_umum'] ?? null;

    // Insert to PROFIL_SISTEM
    $stmt = $pdo->prepare("INSERT INTO PROFIL_SISTEM (id_user, id_jenisprofil, id_status) VALUES (?, ?, ?)");
    $stmt->execute([$id_user, $id_jenisprofil, $id_status]);
    $id_profilsistem = $pdo->lastInsertId();

    // Insert to SISTEM
    $stmt2 = $pdo->prepare("INSERT INTO SISTEM (id_profilsistem, nama_sistem, objektif, id_bahagianunit, tarikh_mula, tarikh_siap, tarikh_guna, bil_pengguna, bil_modul, id_kategori, bahasa_pengaturcaraan, pangkalan_data, rangkaian, integrasi, id_penyelenggaraan, id_kaedahPembangunan, id_outsource)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt2->execute([
        $id_profilsistem,
        $nama_sistem,
        $objektif,
        $id_kaedahPembangunan == 1 ? $id_bahagianInhouse : $id_bahagianunit, // example logic
        $tarikh_mula,
        $tarikh_siap,
        $tarikh_guna,
        $bil_pengguna,
        $bil_modul,
        $id_kategori,
        $bahasa_pengaturcaraan,
        $pangkalan_data,
        $rangkaian,
        $integrasi,
        $id_penyelenggaraan,
        $id_kaedahPembangunan,
        $id_kaedahPembangunan == 2 ? $id_outsource : null
    ]);

    // Insert to KOS
    $stmt3 = $pdo->prepare("INSERT INTO KOS 
        (id_profilsistem, kos_keseluruhan, kos_perkakasan, kos_perisian, kos_lesen_perisian, kos_penyelenggaraan, kos_lain)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt3->execute([
        $id_profilsistem,
        $kos_keseluruhan,
        $kos_perkakasan,
        $kos_perisian,
        $kos_lesen_perisian,
        $kos_penyelenggaraan,
        $kos_lain
    ]);

    // Insert to AKSES
    $stmt4 = $pdo->prepare("INSERT INTO AKSES 
        (id_profilsistem, id_bahagianunit, id_kategoriuser)
        VALUES (?, ?, ?)");
    $stmt4->execute([
        $id_profilsistem,
        $id_pegawaiakses,
        $id_kategoriuser
    ]);


    $message = '<div class="alert alert-success">Profil Sistem Berjaya Disimpan!</div>';
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Daftar Sistem | Sistem Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
    
    <link href="../public/css/sistem.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<div class="content">
    <!-- HEADER -->
    <?php include 'header.php'; ?>

    <div class="main-header mt-4 mb-3"><i class="bi bi-pc-display"></i>Daftar Profil Sistem Utama</div>

    <?= $message ?>

    <form method="POST" class="section-card">
        <!-- PROFIL -->
        <div class="section-title">MAKLUMAT PROFIL</div>
        <div class="row g-3 mb-3">
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
                <label>Jenis Profil</label>
                <select name="jenisprofil" class="form-select" required>
                    <option value="">-- Pilih Jenis Profil --</option>
                    <?php foreach ($jenisprofils as $jp): ?>
                        <option value="<?= $jp['id_jenisprofil'] ?>"><?= $jp['jenisprofil'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- SISTEM -->
        <div class="section-title">A. MAKLUMAT SISTEM</div>       
        <div class="row g-4 mb-4">
            <!-- NAMA SISTEM -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Sistem</label>
                <input type="text" name="nama_sistem" class="form-control shadow-sm" required>
            </div>
            <!-- OBJEKTIF -->
            <div class="col-12">
                <label class="form-label fw-semibold">Objektif</label>
                <textarea name="objektif" class="form-control shadow-sm" rows="3" placeholder="Ringkasan objektif sistem..."></textarea>
            </div>
            <!-- ADDITIONAL FIELDS -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bahagian/Unit</label>
                <select name="bahagianunit" class="form-select shadow-sm">
                    <option value="">-- Pilih Bahagian --</option>
                    <?php foreach ($bahagianUnits as $b): ?>
                        <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Tarikh Mula Pembangunan Sistem</label>
                <input type="date" name="tarikh_mula" class="form-control shadow-sm">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Tarikh Siap Pembangunan Sistem</label>
                <input type="date" name="tarikh_siap" class="form-control shadow-sm">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Tarikh Guna Pembangunan Sistem</label>
                <input type="date" name="tarikh_guna" class="form-control shadow-sm">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Anggaran Bilangan Pengguna</label>
                <input type="number" name="bil_pengguna" class="form-control shadow-sm" placeholder="cth: 150">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Bilangan Modul</label>
                <input type="number" name="bil_modul" class="form-control shadow-sm" placeholder="cth: 5">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Kategori Sistem</label>
                <select name="kategori" class="form-select shadow-sm">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategoriSistem as $k): ?>
                        <option value="<?= $k['id_kategori'] ?>"><?= $k['kategori'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Bahasa Pengaturcaraan Yang Digunakan</label>
                <input type="text" name="bahasa_pengaturcaraan" class="form-control shadow-sm" placeholder="cth: PHP, JavaScript">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Jenis Pangkalan Data</label>
                <input type="text" name="pangkalan_data" class="form-control shadow-sm" placeholder="cth: MySQL, MariaDB">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Rangkaian Yang Digunakan</label>
                <textarea name="rangkaian" class="form-control shadow-sm" rows="2" placeholder="cth: LAN, VPN, Cloud"></textarea>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Integrasi Sistem</label>
                <textarea name="integrasi" class="form-control shadow-sm" rows="2" placeholder="cth: Integrasi dengan Sistem HR / LDAP / API lain"></textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Penyelenggara Sistem</label>
                <select name="penyelenggara" class="form-select shadow-sm">
                    <option value="">-- Pilih Penyelenggara --</option>
                    <?php foreach ($penyelenggara as $p): ?>
                        <option value="<?= $p['id_penyelenggaraan'] ?>"><?= $p['penyelenggaraan'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- KAEDAH PEMBANGUNAN -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Kaedah Pembangunan</label>
                <select name="kaedahPembangunan" id="kaedahPembangunan" class="form-select shadow-sm" required>
                    <option value="">-- Pilih Kaedah --</option>
                    <?php foreach ($kaedahPembangunan as $k): ?>
                        <option value="<?= $k['id_kaedahPembangunan'] ?>"><?= $k['kaedahPembangunan'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <!-- OUTSOURCE FIELDS -->
        <div id="outsourceFields" class="conditional-box" style="display:none;">
            <div class="sub-section-header">Maklumat Pembekal</div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Syarikat</label>
                    <select name="outsource" id="outsource" class="form-select shadow-sm">
                        <option value="">-- Pilih Syarikat --</option>
                        <?php foreach ($outsources as $o): ?>
                            <option value="<?= $o['id_outsource'] ?>"><?= $o['nama_syarikat'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Alamat Syarikat</label>
                    <input type="text" name="alamat_syarikat" id="alamat_syarikat" class="form-control shadow-sm" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">PIC</label>
                    <select name="pic" id="pic" class="form-select shadow-sm">
                        <option value="">-- Pilih PIC --</option>
                        <?php foreach ($pics as $p): ?>
                            <option value="<?= $p['id_PIC'] ?>"><?= $p['nama_PIC'] ?></option>
                        <?php endforeach; ?>
                        <option value="new">-- PIC Baru --</option>
                    </select>
                </div>

                <div class="col-md-6" id="picDetails" style="display:none;">
                    <label class="form-label fw-semibold">Maklumat PIC Baru</label>
                    <input type="text" name="nama_pic" class="form-control shadow-sm mb-2" placeholder="Nama PIC">
                    <input type="email" name="emel_pic" class="form-control shadow-sm mb-2" placeholder="Emel PIC">
                    <input type="text" name="tel_pic" class="form-control shadow-sm mb-2" placeholder="No Telefon">
                    <input type="text" name="fax_pic" class="form-control shadow-sm mb-2" placeholder="Fax">
                    <input type="text" name="jawatan_pic" class="form-control shadow-sm" placeholder="Jawatan">
                </div>
            </div>
        </div>
        <!-- INHOUSE FIELDS -->
        <div id="inhouseFields" class="conditional-box" style="display:none;">
            <div class="sub-section-header">Maklumat Bahagian</div>

            <div class="row g-4 mb-3">
                <div class="col-md-10">
                    <label class="form-label fw-semibold">Bahagian Yang Bertanggungjawab</label>
                    <select name="bahagian_inhouse" class="form-select shadow-sm">
                        <option value="">-- Pilih Bahagian --</option>
                        <?php foreach ($bahagianUnits as $b): ?>
                            <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <script>
            const kaedah = document.getElementById('kaedahPembangunan');
            const outsourceFields = document.getElementById('outsourceFields');
            const inhouseFields = document.getElementById('inhouseFields');
            const outsourceSelect = document.getElementById('outsource');
            const alamatSyarikat = document.getElementById('alamat_syarikat');
            const picSelect = document.getElementById('pic');
            const picDetails = document.getElementById('picDetails');

            kaedah.addEventListener('change', function() {
                const selectedValue = this.value; // ID kaedah pembangunan

                if (selectedValue == "2") { // Outsource
                    outsourceFields.style.display = 'flex';
                    inhouseFields.style.display = 'none';
                }
                else if (selectedValue == "1") { // Inhouse
                    outsourceFields.style.display = 'none';
                    inhouseFields.style.display = 'flex';
                }
                else {
                    outsourceFields.style.display = 'none';
                    inhouseFields.style.display = 'none';
                }
            });

            // Auto-fill alamat syarikat
            outsourceSelect.addEventListener('change', function() {
                const selectedId = this.value;
                const outsources = <?php echo json_encode($outsources); ?>;
                const selectedOut = outsources.find(o => o.id_outsource == selectedId);
                alamatSyarikat.value = selectedOut ? selectedOut.alamat_syarikat : '';
            });

            // PIC details toggle
            picSelect.addEventListener('change', function() {
                if(this.value === 'new') {
                    picDetails.style.display = 'block';
                } else {
                    picDetails.style.display = 'none';
                }
            });
        </script>

        <!-- KOS -->
        <div class="section-title">B. MAKLUMAT KOS SISTEM</div>
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Kos Keseluruhan (RM)</label>
                <input type="number" step="0.01" name="kos_keseluruhan" class="form-control shadow-sm" placeholder="0.00">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Kos Perkakasan (RM)</label>
                <input type="number" step="0.01" name="kos_perkakasan" class="form-control shadow-sm" placeholder="0.00">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Kos Perisian (RM)</label>
                <input type="number" step="0.01" name="kos_perisian" class="form-control shadow-sm" placeholder="0.00">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Kos Lesen Perisian (RM)</label>
                <input type="number" step="0.01" name="kos_lesen_perisian" class="form-control shadow-sm" placeholder="0.00">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Kos Penyelenggaraan (RM)</label>
                <input type="number" step="0.01" name="kos_penyelenggaraan" class="form-control shadow-sm" placeholder="0.00">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Kos Lain (RM)</label>
                <input type="number" step="0.01" name="kos_lain" class="form-control shadow-sm" placeholder="0.00">
            </div>
        </div>

        <!-- AKSES -->
        <div class="section-title">C. MAKLUMAT AKSES SISTEM</div>
        <div class="row g-4 mb-4">
            <!-- Pegawai Urus Akses -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Pegawai Urus Akses</label>
                <select name="pegawai_akses" class="form-select shadow-sm" required>
                    <option value="">-- Pilih Pegawai --</option>
                    <?php foreach ($bahagianUnits as $b): ?>
                        <option value="<?= $b['id_bahagianunit'] ?>">
                            <?= $b['bahagianunit'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Kategori Jenis Pengguna -->
            <div class="row g-4 mb-4">
                <label class="form-label fw-bold">Kategori Jenis Pengguna:</label>
                <!-- Dalaman -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Dalaman (Jabatan/Bahagian)</label>
                    <select name="jenis_dalaman" class="form-select shadow-sm" required>
                        <option value="">-- Pilih --</option>
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
                <!-- Umum -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Umum (Orang Awam)</label>
                    <select name="jenis_umum" class="form-select shadow-sm" required>
                        <option value="">-- Pilih --</option>
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ENTITI -->
        <div class="section-title">D. MAKLUMAT AM ENTITI</div>
        <div class="row g-3 mb-3">
            <!-- Nama Entiti -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Entiti</label>
                <input type="text" name="nama_entiti" class="form-control shadow-sm" required>
            </div>
            <!-- Tarikh Kemaskini -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tarikh Kemaskini</label>
                <input type="date" name="tarikh_kemaskini" class="form-control shadow-sm" value="<?= date('Y-m-d') ?>" required>
            </div>
            <!-- Bahagian -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bahagian</label>
                <select name="id_bahagianunit_entiti" class="form-select shadow-sm">
                    <option value="">-- Pilih Bahagian --</option>
                    <?php foreach ($bahagianUnits as $b): ?>
                        <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>  
            <!-- Nama Ketua -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Ketua</label>
                <select name="id_userprofile_ketua" id="id_userprofile_ketua" class="form-select shadow-sm">
                    <option value="">-- Pilih Ketua --</option>
                    <?php foreach ($userProfiles as $u): ?>
                        <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Auto-fill Ketua Details -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">No Telefon Ketua</label>
                <input type="text" name="notelefon_ketua" id="notelefon_ketua" class="form-control shadow-sm" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">No Faks Ketua</label>
                <input type="text" name="fax_ketua" id="fax_ketua" class="form-control shadow-sm" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Emel Ketua</label>
                <input type="email" name="emel_ketua" id="emel_ketua" class="form-control shadow-sm" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Alamat Pejabat Ketua</label>
                <input type="text" name="alamat_pejabat_ketua" id="alamat_pejabat_ketua" class="form-control shadow-sm" readonly>
            </div>
            <!-- Nama CIO -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama CIO</label>
                <select name="id_userprofile_cio" class="form-select shadow-sm">
                    <option value="">-- Pilih CIO --</option>
                    <?php foreach ($userProfiles as $u): ?>
                        <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Nama ICTSO -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama ICTSO</label>
                <select name="id_userprofile_ictso" class="form-select shadow-sm">
                    <option value="">-- Pilih ICTSO --</option>
                    <?php foreach ($userProfiles as $u): ?>
                        <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Carta Organisasi -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Carta Organisasi Entiti</label>
                <select name="id_carta" class="form-select shadow-sm">
                    <option value="">-- Pilih Carta --</option>
                    <?php foreach ($cartas as $c): ?>
                        <option value="<?= $c['id_carta'] ?>"><?= $c['carta'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <script>
            const userProfiles = <?= json_encode($userProfiles); ?>;
            const ketuaSelect = document.getElementById('id_userprofile_ketua');

            ketuaSelect.addEventListener('change', function() {
                const selected = userProfiles.find(u => u.id_userprofile == this.value);
                document.getElementById('notelefon_ketua').value = selected ? selected.notelefon_user : '';
                document.getElementById('fax_ketua').value = selected ? selected.fax_user : '';
                document.getElementById('emel_ketua').value = selected ? selected.emel_user : '';
                document.getElementById('alamat_pejabat_ketua').value = selected ? selected.alamat_pejabat : '';
            });
        </script>

        <!-- PEGAWAI RUJUKAN -->
        <div class="section-title">E. MAKLUMAT PEGAWAI RUJUKAN</div>
        <div class="row g-3 mb-3">
            <!-- Nama Pegawai -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Pegawai</label>
                <select name="id_userprofile_rujukan" id="id_userprofile_rujukan" class="form-select shadow-sm">
                    <option value="">-- Pilih Pegawai --</option>
                    <?php foreach ($userProfiles as $u): ?>
                        <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Jawatan & Gred -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Jawatan & Gred</label>
                <input type="text" name="jawatan_rujukan" id="jawatan_rujukan" class="form-control shadow-sm" readonly>
            </div>
            <!-- Email -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Emel Pegawai</label>
                <input type="email" name="emel_rujukan" id="emel_rujukan" class="form-control shadow-sm" readonly>
            </div>
            <!-- No Telefon -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">No Telefon</label>
                <input type="text" name="notelefon_rujukan" id="notelefon_rujukan" class="form-control shadow-sm" readonly>
            </div>
            <!-- No Faks -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">No Faks</label>
                <input type="text" name="fax_rujukan" id="fax_rujukan" class="form-control shadow-sm" readonly>
            </div>
            <!-- Bahagian/Unit -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bahagian/Seksyen/Unit</label>
                <select name="id_bahagianunit_rujukan" class="form-select shadow-sm">
                    <option value="">-- Pilih Bahagian --</option>
                    <?php foreach ($bahagianUnits as $b): ?>
                        <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <script>
            const rujukanSelect = document.getElementById('id_userprofile_rujukan');
            rujukanSelect.addEventListener('change', function() {
                const selected = userProfiles.find(u => u.id_userprofile == this.value);
                document.getElementById('jawatan_rujukan').value = selected ? selected.jawatan_user : '';
                document.getElementById('emel_rujukan').value = selected ? selected.emel_user : '';
                document.getElementById('notelefon_rujukan').value = selected ? selected.notelefon_user : '';
                document.getElementById('fax_rujukan').value = selected ? selected.fax_user : '';
            });
        </script>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Simpan Profil</button>
        </div>
    </form>

    </div>
</div>

</body>
</html>
