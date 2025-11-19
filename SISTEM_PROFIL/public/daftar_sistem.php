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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // contoh ambil data
    $id_status = $_POST['id_status'];
    $id_jenisprofil = $_POST['id_jenisprofil'];
    $nama_sistem = $_POST['nama_sistem'];
    $objektif = $_POST['objektif'];
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
    $kos_keseluruhan = $_POST['kos_keseluruhan'];
    $kos_perkakasan = $_POST['kos_perkakasan'];
    $kos_perisian = $_POST['kos_perisian'];
    $kos_lesen_perisian = $_POST['kos_lesen_perisian'];
    $kos_penyelenggaraan = $_POST['kos_penyelenggaraan'];
    $kos_lain = $_POST['kos_lain'];

    // dapatkan current user
    $id_user = $_SESSION['id_user'];

    try {
        $pdo->beginTransaction();

        // 1. insert ke PROFIL_SISTEM
        $stmt = $pdo->prepare("INSERT INTO PROFIL_SISTEM (id_user, id_jenisprofil, id_status) VALUES (?, ?, ?)");
        $stmt->execute([$id_user, $id_jenisprofil, $id_status]);
        $id_profilsistem = $pdo->lastInsertId();

        // 2. insert ke SISTEM
        $stmt2 = $pdo->prepare("INSERT INTO SISTEM (id_profilsistem, nama_sistem, objektif, id_bahagianunit, tarikh_mula, tarikh_siap, tarikh_guna, bil_pengguna, bil_modul, id_kategori, bahasa_pengaturcaraan, pangkalan_data, rangkaian, integrasi, id_penyelenggaraan, id_kaedahPembangunan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->execute([$id_profilsistem, $nama_sistem, $objektif, $id_bahagianunit, $tarikh_mula, $tarikh_siap, $tarikh_guna, $bil_pengguna, $bil_modul, $id_kategori, $bahasa_pengaturcaraan, $pangkalan_data, $rangkaian, $integrasi, $id_penyelenggaraan, $id_kaedahPembangunan]);

        // 3. insert ke KOS
        $stmt3 = $pdo->prepare("INSERT INTO KOS (id_profilsistem, kos_keseluruhan, kos_perkakasan, kos_perisian, kos_lesen_perisian, kos_penyelenggaraan, kos_lain) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt3->execute([$id_profilsistem, $kos_keseluruhan, $kos_perkakasan, $kos_perisian, $kos_lesen_perisian, $kos_penyelenggaraan, $kos_lain]);

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

        <!-- ========================================
            SECTION: STATUS & JENIS PROFIL
        ========================================== -->
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

        <!-- ========================================
            SECTION A — MAKLUMAT SISTEM
        ========================================== -->
        <div class="section-title">A. Maklumat Sistem</div>

        <div class="mb-3">
            <label class="form-label">Nama Sistem</label>
            <input type="text" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Objektif</label>
            <textarea class="form-control" rows="4"></textarea>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Kaedah Pembangunan</label>
                <select class="form-control">
                    <option value="">-- Pilih Kaedah --</option>
                    <option>In-house</option>
                    <option>Outsource</option>
                </select>
            </div>

            <div class="col mb-3">
                <label class="form-label">Bahagian/Unit</label>
                <select class="form-control">
                    <option value="">-- Pilih Bahagian/Unit --</option>
                    <option>Bahagian Khidmat Sokongan</option>
                    <option>Bahagian Khidmat Sokongan (Unit IT)</option>
                </select>
            </div>

            <div class="col mb-3">
                <label class="form-label">Kategori Sistem</label>
                <select class="form-control">
                    <option value="">-- Pilih Kategori --</option>
                    <option>Pentadbiran</option>
                    <option>Operasi</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Tarikh Mula</label>
                <input type="date" class="form-control">
            </div>
            <div class="col mb-3">
                <label class="form-label">Tarikh Siap</label>
                <input type="date" class="form-control">
            </div>
            <div class="col mb-3">
                <label class="form-label">Tarikh Guna</label>
                <input type="date" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Anggaran Bilangan Pengguna</label>
                <input type="text" class="form-control">
            </div>

            <div class="col mb-3">
                <label class="form-label">Bilangan Modul</label>
                <input type="text" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Bahasa Pengaturcaraan</label>
                <input type="text" class="form-control">
            </div>

            <div class="col mb-3">
                <label class="form-label">Jenis Pangkalan Data</label>
                <input type="text" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Rangkaian Digunakan</label>
                <input type="text" class="form-control">
            </div>

            <div class="col mb-3">
                <label class="form-label">Integrasi Sistem Lain</label>
                <input type="text" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Penyelenggara Sistem</label>
            <select class="form-control">
                <option value="">-- Pilih Penyelenggara --</option>
                <option>Dalaman</option>
                <option>Pembekal</option>
            </select>
        </div>

        <!-- ========================================
            SECTION B — KOS SISTEM
        ========================================== -->
        <div class="section-title">B. Maklumat Kos Sistem</div>

        <div class="row">
            <div class="col mb-3">
                <label>Kos Keseluruhan (RM)</label>
                <input type="number" class="form-control" step="0.01">
            </div>
            <div class="col mb-3">
                <label>Kos Perkakasan (RM)</label>
                <input type="number" class="form-control" step="0.01">
            </div>
            <div class="col mb-3">
                <label>Kos Perisian (RM)</label>
                <input type="number" class="form-control" step="0.01">
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label>Kos Lesen Perisian (RM)</label>
                <input type="number" class="form-control" step="0.01">
            </div>
            <div class="col mb-3">
                <label>Kos Penyelenggaraan (RM)</label>
                <input type="number" class="form-control" step="0.01">
            </div>
            <div class="col mb-3">
                <label>Kos Lain (RM)</label>
                <input type="number" class="form-control" step="0.01">
            </div>
        </div>

        <!-- ========================================
            SECTION C — AKSES SISTEM
        ========================================== -->
        <div class="section-title">C. Maklumat Akses Sistem</div>

        <div class="mb-3">
            <label>Pegawai Mengurus Akses</label>
            <select class="form-control">
                <option>Encik Ali</option>
                <option>Puan Rohana</option>
            </select>
        </div>

        <div class="row">
            <p>Kategori Jenis pengguna:</p>

            <div class="col mb-3">
                <label>Kategori Dalaman</label>
                <select class="form-control">
                    <option>Ya</option>
                    <option>Tidak</option>
                </select>
            </div>

            <div class="col mb-3">
                <label>Kategori Umum</label>
                <select class="form-control">
                    <option>Ya</option>
                    <option>Tidak</option>
                </select>
            </div>
        </div>

        <!-- ========================================
            SECTION D — ENTITI
        ========================================== -->
        <div class="section-title">D. Maklumat Am Entiti</div>

        <div class="mb-3">
            <label>Nama Entiti</label>
            <input type="text" class="form-control">
        </div>

        <div class="row">
            <div class="mb-3">
                <label>Tarikh Kemaskini</label>
                <input type="date" class="form-control">
            </div>

            <div class="col mb-3">
                <label class="form-label">Bahagian</label>
                <select class="form-control">
                    <option value="">-- Pilih Bahagian --</option>
                    <option>Bahagian Khidmat Sokongan</option>
                    <option>Bahagian Khidmat Sokongan (Unit IT)</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>Nama Ketua</label>
            <select class="form-control">
                <option>Encik Azman</option>
                <option>Puan Laila</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Nama CIO</label>
            <select class="form-control">
                <option>Encik Hafiz</option>
                <option>Puan Nurul</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Nama ICTSO</label>
            <select class="form-control">
                <option>Encik Rizal</option>
                <option>Puan Ira</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Carta Organisasi</label>
            <select class="form-control">
                <option>Telah Dikemukakan</option>
                <option>Belum Dikemukakan</option>
            </select>
        </div>

        <!-- ========================================
            SECTION E — PEGAWAI RUJUKAN
        ========================================== -->
        <div class="section-title">E. Maklumat Pegawai Rujukan</div>

        <div class="mb-3">
            <label>Nama Pegawai</label>
            <select class="form-control">
                <option>Encik Ali</option>
                <option>Puan Farah</option>
            </select>
        </div>

        <!-- SUBMIT BUTTON -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Simpan Profil</button>
        </div>

    </form>

    </div>
</div>

</body>
</html>
