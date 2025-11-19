<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_profilsistem = $_POST['id_profilsistem'];
    $nama_sistem = trim($_POST['nama_sistem']);
    $objektif = trim($_POST['objektif']);
    $id_bahagianunit = $_POST['id_bahagianunit'];
    $tarikh_mula = $_POST['tarikh_mula'];
    $tarikh_siap = $_POST['tarikh_siap'];
    $tarikh_guna = $_POST['tarikh_guna'];
    $bil_pengguna = $_POST['bil_pengguna'];
    $bil_modul = $_POST['bil_modul'];
    $id_kategori = $_POST['id_kategori'];
    $bahasa_pengaturcaraan = $_POST['bahasa_pengaturcaraan'];
    $pangkalan_data = $_POST['pangkalan_data'];
    $rangkaian = $_POST['rangkaian'];
    $integrasi = $_POST['integrasi'];
    $id_penyelenggaraan = $_POST['id_penyelenggaraan'];
    $id_kaedahPembangunan = $_POST['id_kaedahPembangunan'];
    $id_outsource = $_POST['id_outsource'];

    $stmt = $pdo->prepare("INSERT INTO SISTEM (
        id_profilsistem, nama_sistem, objektif, id_bahagianunit, tarikh_mula, tarikh_siap, tarikh_guna, 
        bil_pengguna, bil_modul, id_kategori, bahasa_pengaturcaraan, pangkalan_data, rangkaian, integrasi, 
        id_penyelenggaraan, id_kaedahPembangunan, id_outsource
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt->execute([
        $id_profilsistem, $nama_sistem, $objektif, $id_bahagianunit, $tarikh_mula, $tarikh_siap, $tarikh_guna,
        $bil_pengguna, $bil_modul, $id_kategori, $bahasa_pengaturcaraan, $pangkalan_data, $rangkaian, $integrasi,
        $id_penyelenggaraan, $id_kaedahPembangunan, $id_outsource
    ])) {
        $message = '<div class="alert alert-success text-center">✅ Sistem berjaya didaftarkan!</div>';
    } else {
        $message = '<div class="alert alert-danger text-center">❌ Ralat semasa pendaftaran sistem.</div>';
    }
}

// Fetch lookup options
$profilsistem_list = $pdo->query("SELECT id_profilsistem FROM PROFIL_SISTEM")->fetchAll(PDO::FETCH_ASSOC);
$bahagianunit_list = $pdo->query("SELECT id_bahagianunit, bahagianunit FROM LOOKUP_BAHAGIANUNIT")->fetchAll(PDO::FETCH_ASSOC);
$kategori_list = $pdo->query("SELECT id_kategori, kategori FROM LOOKUP_KATEGORI")->fetchAll(PDO::FETCH_ASSOC);
$penyelenggaraan_list = $pdo->query("SELECT id_penyelenggaraan, penyelenggaraan FROM LOOKUP_PENYELENGGARAAN")->fetchAll(PDO::FETCH_ASSOC);
$kaedah_list = $pdo->query("SELECT id_kaedahPembangunan, kaedahPembangunan FROM LOOKUP_KAEDAHPEMBANGUNAN")->fetchAll(PDO::FETCH_ASSOC);
$outsource_list = $pdo->query("SELECT id_outsource, nama_syarikat FROM LOOKUP_OUTSOURCE")->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Daftar Sistem Profil | Sistem Profil SPAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
    
    <link href="../public/css/sistemUtama.css" rel="stylesheet">
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
        <div class="mb-3">
            <label class="form-label">Pilih Profil Sistem</label>
            <select name="id_profilsistem" class="form-control" required>
                <option value="">-- Pilih Profil Sistem --</option>
                <?php foreach($profilsistem_list as $p): ?>
                    <option value="<?= $p['id_profilsistem'] ?>"><?= $p['id_profilsistem'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Sistem</label>
            <input type="text" name="nama_sistem" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Objektif</label>
            <textarea name="objektif" class="form-control" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Bahagian/Unit</label>
            <select name="id_bahagianunit" class="form-control">
                <option value="">-- Pilih Bahagian/Unit --</option>
                <?php foreach($bahagianunit_list as $b): ?>
                    <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Tarikh Mula</label>
                <input type="date" name="tarikh_mula" class="form-control">
            </div>
            <div class="col mb-3">
                <label class="form-label">Tarikh Siap</label>
                <input type="date" name="tarikh_siap" class="form-control">
            </div>
            <div class="col mb-3">
                <label class="form-label">Tarikh Guna</label>
                <input type="date" name="tarikh_guna" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Bilangan Pengguna</label>
                <input type="text" name="bil_pengguna" class="form-control">
            </div>
            <div class="col mb-3">
                <label class="form-label">Bilangan Modul</label>
                <input type="text" name="bil_modul" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori Sistem</label>
            <select name="id_kategori" class="form-control">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach($kategori_list as $k): ?>
                    <option value="<?= $k['id_kategori'] ?>"><?= $k['kategori'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Bahasa Pengaturcaraan</label>
            <input type="text" name="bahasa_pengaturcaraan" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Pangkalan Data</label>
            <input type="text" name="pangkalan_data" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Rangkaian</label>
            <textarea name="rangkaian" class="form-control" rows="2"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Integrasi</label>
            <textarea name="integrasi" class="form-control" rows="2"></textarea>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Penyelenggaraan</label>
                <select name="id_penyelenggaraan" class="form-control">
                    <option value="">-- Pilih Penyelenggaraan --</option>
                    <?php foreach($penyelenggaraan_list as $p): ?>
                        <option value="<?= $p['id_penyelenggaraan'] ?>"><?= $p['penyelenggaraan'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col mb-3">
                <label class="form-label">Kaedah Pembangunan</label>
                <select name="id_kaedahPembangunan" class="form-control">
                    <option value="">-- Pilih Kaedah --</option>
                    <?php foreach($kaedah_list as $k): ?>
                        <option value="<?= $k['id_kaedahPembangunan'] ?>"><?= $k['kaedahPembangunan'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col mb-3">
                <label class="form-label">Outsource</label>
                <select name="id_outsource" class="form-control">
                    <option value="">-- Pilih Outsource --</option>
                    <?php foreach($outsource_list as $o): ?>
                        <option value="<?= $o['id_outsource'] ?>"><?= $o['nama_syarikat'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Simpan Sistem</button>
        </div>
    </form>
    ```

    </div>
</div>

</body>
</html>
