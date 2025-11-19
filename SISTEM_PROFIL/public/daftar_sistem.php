<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);
$message = '';

// ambil data lookup dari DB
$statuses = $pdo->query("SELECT * FROM LOOKUP_STATUS")->fetchAll(PDO::FETCH_ASSOC);
$jenisProfil = $pdo->query("SELECT * FROM LOOKUP_JENISPROFIL")->fetchAll(PDO::FETCH_ASSOC);
$bahagianUnit = $pdo->query("SELECT * FROM LOOKUP_BAHAGIANUNIT")->fetchAll(PDO::FETCH_ASSOC);
$kategori = $pdo->query("SELECT * FROM LOOKUP_KATEGORI")->fetchAll(PDO::FETCH_ASSOC);
$penyelenggaraan = $pdo->query("SELECT * FROM LOOKUP_PENYELENGGARAAN")->fetchAll(PDO::FETCH_ASSOC);
$kaedahPembangunan = $pdo->query("SELECT * FROM LOOKUP_KAEDAHPEMBANGUNAN")->fetchAll(PDO::FETCH_ASSOC);
$userProfile = $pdo->query("SELECT * FROM LOOKUP_USERPROFILE")->fetchAll(PDO::FETCH_ASSOC);
$carta = $pdo->query("SELECT * FROM LOOKUP_CARTA")->fetchAll(PDO::FETCH_ASSOC);
$outsource = $pdo->query("SELECT * FROM LOOKUP_OUTSOURCE")->fetchAll(PDO::FETCH_ASSOC);
$kategoriUser = $pdo->query("SELECT * FROM LOOKUP_KATEGORIUSER")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// Dapatkan data dari form
$id_status = $_POST['id_status'] ?? null;
$id_jenisprofil = $_POST['id_jenisprofil'] ?? null;
$nama_sistem = $_POST['nama_sistem'] ?? '';
$objektif = $_POST['objektif'] ?? '';
$id_bahagianunit = $_POST['id_bahagianunit'] ?? null;
$tarikh_mula = $_POST['tarikh_mula'] ?? null;
$tarikh_siap = $_POST['tarikh_siap'] ?? null;
$tarikh_guna = $_POST['tarikh_guna'] ?? null;
$bil_pengguna = $_POST['bil_pengguna'] ?? null;
$bil_modul = $_POST['bil_modul'] ?? null;
$id_kategori = $_POST['id_kategori'] ?? null;
$bahasa_pengaturcaraan = $_POST['bahasa_pengaturcaraan'] ?? '';
$pangkalan_data = $_POST['pangkalan_data'] ?? '';
$rangkaian = $_POST['rangkaian'] ?? '';
$integrasi = $_POST['integrasi'] ?? '';
$id_penyelenggaraan = $_POST['id_penyelenggaraan'] ?? null;
$id_kaedahPembangunan = $_POST['id_kaedahPembangunan'] ?? null;
$id_outsource = $_POST['id_outsource'] ?? null;

$kos_keseluruhan = $_POST['kos_keseluruhan'] ?? 0;
$kos_perkakasan = $_POST['kos_perkakasan'] ?? 0;
$kos_perisian = $_POST['kos_perisian'] ?? 0;
$kos_lesen_perisian = $_POST['kos_lesen_perisian'] ?? 0;
$kos_penyelenggaraan = $_POST['kos_penyelenggaraan'] ?? 0;
$kos_lain = $_POST['kos_lain'] ?? 0;

$id_userakses = $_POST['id_bahagianunit_akses'] ?? null;
$id_kategoriuser_dalaman = $_POST['id_kategoriuser_dalaman'] ?? null;
$id_kategoriuser_umum = $_POST['id_kategoriuser_umum'] ?? null;

$nama_entiti = $_POST['nama_entiti'] ?? '';
$tarikh_kemaskini = $_POST['tarikh_kemaskini'] ?? null;
$id_bahagianunit_entiti = $_POST['id_bahagianunit_entiti'] ?? null;
$id_userprofile_ketua = $_POST['id_userprofile_ketua'] ?? null;
$id_userprofile_cio = $_POST['id_userprofile_cio'] ?? null;
$id_userprofile_ictso = $_POST['id_userprofile_ictso'] ?? null;
$id_carta = $_POST['id_carta'] ?? null;

$id_user = $_SESSION['id_user'];

try {
    $pdo->beginTransaction();

    // PROFIL SISTEM
    $stmt = $pdo->prepare("INSERT INTO PROFIL_SISTEM (id_user, id_jenisprofil, id_status, id_userprofile) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_user, $id_jenisprofil, $id_status, $id_userprofile_ketua]);
    $id_profilsistem = $pdo->lastInsertId();

    // SISTEM
    $stmt2 = $pdo->prepare("INSERT INTO SISTEM (id_profilsistem, nama_sistem, objektif, id_bahagianunit, tarikh_mula, tarikh_siap, tarikh_guna, bil_pengguna, bil_modul, id_kategori, bahasa_pengaturcaraan, pangkalan_data, rangkaian, integrasi, id_penyelenggaraan, id_kaedahPembangunan, id_outsource) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt2->execute([$id_profilsistem, $nama_sistem, $objektif, $id_bahagianunit, $tarikh_mula, $tarikh_siap, $tarikh_guna, $bil_pengguna, $bil_modul, $id_kategori, $bahasa_pengaturcaraan, $pangkalan_data, $rangkaian, $integrasi, $id_penyelenggaraan, $id_kaedahPembangunan, $id_outsource]);

    // KOS
    $stmt3 = $pdo->prepare("INSERT INTO KOS (id_profilsistem, kos_keseluruhan, kos_perkakasan, kos_perisian, kos_lesen_perisian, kos_penyelenggaraan, kos_lain) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt3->execute([$id_profilsistem, $kos_keseluruhan, $kos_perkakasan, $kos_perisian, $kos_lesen_perisian, $kos_penyelenggaraan, $kos_lain]);

    // AKSES
    $stmt4 = $pdo->prepare("INSERT INTO AKSES (id_profilsistem, id_bahagianunit, id_kategoriuser) VALUES (?, ?, ?)");
    $stmt4->execute([$id_profilsistem, $id_userakses, $id_kategoriuser_dalaman]); // boleh adjust ikut logic kategori

    // ENTITI
    $stmt5 = $pdo->prepare("INSERT INTO ENTITI (id_profilsistem, nama_entiti, tarikh_kemaskini, id_bahagianunit, id_userprofile, id_carta) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt5->execute([$id_profilsistem, $nama_entiti, $tarikh_kemaskini, $id_bahagianunit_entiti, $id_userprofile_ketua, $id_carta]);

    $pdo->commit();
    $message = '<div class="alert alert-success">Profil sistem berjaya disimpan!</div>';
} catch (Exception $e) {
    $pdo->rollBack();
    $message = '<div class="alert alert-danger">Ralat: '.$e->getMessage().'</div>';
}

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
        <!-- STATUS & JENIS PROFIL -->
        <div class="section-title">Maklumat Profil</div>
        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Status</label>
                <select class="form-control" name="id_status" required>
                    <option value="">-- Pilih Status --</option>
                    <?php foreach($statuses as $s): ?>
                        <option value="<?= $s['id_status'] ?>"><?= $s['status'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col mb-3">
                <label class="form-label">Jenis Profil</label>
                <select class="form-control" name="id_jenisprofil" required>
                    <option value="">-- Pilih Jenis Profil --</option>
                    <?php foreach($jenisProfil as $j): ?>
                        <option value="<?= $j['id_jenisprofil'] ?>"><?= $j['jenisprofil'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- SISTEM -->
        <div class="section-title">A. Maklumat Sistem</div>
        <div class="mb-3">
            <label class="form-label">Nama Sistem</label>
            <input type="text" class="form-control" name="nama_sistem" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Objektif</label>
            <textarea class="form-control" rows="4" name="objektif"></textarea>
        </div>
        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Kaedah Pembangunan</label>
                <select class="form-control" name="id_kaedahPembangunan">
                    <option value="">-- Pilih Kaedah --</option>
                    <?php foreach($kaedahPembangunan as $k): ?>
                        <option value="<?= $k['id_kaedahPembangunan'] ?>"><?= $k['kaedahPembangunan'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col mb-3">
                <label class="form-label">Bahagian/Unit</label>
                <select class="form-control" name="id_bahagianunit">
                    <option value="">-- Pilih Bahagian/Unit --</option>
                    <?php foreach($bahagianUnit as $b): ?>
                        <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col mb-3">
                <label class="form-label">Kategori Sistem</label>
                <select class="form-control" name="id_kategori">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach($kategori as $c): ?>
                        <option value="<?= $c['id_kategori'] ?>"><?= $c['kategori'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Tarikh Mula</label>
                <input type="date" class="form-control" name="tarikh_mula">
            </div>
            <div class="col mb-3">
                <label class="form-label">Tarikh Siap</label>
                <input type="date" class="form-control" name="tarikh_siap">
            </div>
            <div class="col mb-3">
                <label class="form-label">Tarikh Guna</label>
                <input type="date" class="form-control" name="tarikh_guna">
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Anggaran Bilangan Pengguna</label>
                <input type="text" class="form-control" name="bil_pengguna">
            </div>
            <div class="col mb-3">
                <label class="form-label">Bilangan Modul</label>
                <input type="text" class="form-control" name="bil_modul">
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Bahasa Pengaturcaraan</label>
                <input type="text" class="form-control" name="bahasa_pengaturcaraan">
            </div>
            <div class="col mb-3">
                <label class="form-label">Jenis Pangkalan Data</label>
                <input type="text" class="form-control" name="pangkalan_data">
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Rangkaian Digunakan</label>
                <input type="text" class="form-control" name="rangkaian">
            </div>
            <div class="col mb-3">
                <label class="form-label">Integrasi Sistem Lain</label>
                <input type="text" class="form-control" name="integrasi">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Penyelenggara Sistem</label>
            <select class="form-control" name="id_penyelenggaraan">
                <option value="">-- Pilih Penyelenggara --</option>
                <?php foreach($penyelenggaraan as $p): ?>
                    <option value="<?= $p['id_penyelenggaraan'] ?>"><?= $p['penyelenggaraan'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- KOS -->
        <div class="section-title">B. Maklumat Kos Sistem</div>
        <div class="row">
            <div class="col mb-3"><label>Kos Keseluruhan (RM)</label><input type="number" class="form-control" step="0.01" name="kos_keseluruhan"></div>
            <div class="col mb-3"><label>Kos Perkakasan (RM)</label><input type="number" class="form-control" step="0.01" name="kos_perkakasan"></div>
            <div class="col mb-3"><label>Kos Perisian (RM)</label><input type="number" class="form-control" step="0.01" name="kos_perisian"></div>
        </div>
        <div class="row">
            <div class="col mb-3"><label>Kos Lesen Perisian (RM)</label><input type="number" class="form-control" step="0.01" name="kos_lesen_perisian"></div>
            <div class="col mb-3"><label>Kos Penyelenggaraan (RM)</label><input type="number" class="form-control" step="0.01" name="kos_penyelenggaraan"></div>
            <div class="col mb-3"><label>Kos Lain (RM)</label><input type="number" class="form-control" step="0.01" name="kos_lain"></div>
        </div>

        <!-- ENTITI -->
        <div class="section-title">D. Maklumat Am Entiti</div>
        <div class="mb-3">
            <label>Nama Entiti</label>
            <input type="text" class="form-control" name="nama_entiti">
        </div>
        <div class="row">
            <div class="col mb-3">
                <label>Tarikh Kemaskini</label>
                <input type="date" class="form-control" name="tarikh_kemaskini">
            </div>
            <div class="col mb-3">
                <label class="form-label">Bahagian</label>
                <select class="form-control" name="id_bahagianunit_entiti">
                    <option value="">-- Pilih Bahagian --</option>
                    <?php foreach($bahagianUnit as $b): ?>
                        <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label>Ketua</label>
            <select class="form-control" name="id_userprofile_ketua">
                <option value="">-- Pilih Ketua --</option>
                <?php foreach($userProfile as $u): ?>
                    <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>CIO</label>
            <select class="form-control" name="id_userprofile_cio">
                <option value="">-- Pilih CIO --</option>
                <?php foreach($userProfile as $u): ?>
                    <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>ICTSO</label>
            <select class="form-control" name="id_userprofile_ictso">
                <option value="">-- Pilih ICTSO --</option>
                <?php foreach($userProfile as $u): ?>
                    <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Carta Organisasi</label>
            <select class="form-control" name="id_carta">
                <option value="">-- Pilih Carta --</option>
                <?php foreach($carta as $c): ?>
                    <option value="<?= $c['id_carta'] ?>"><?= $c['carta'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Simpan Profil</button>
        </div>
    </form>

    </div>
</div>

</body>
</html>
