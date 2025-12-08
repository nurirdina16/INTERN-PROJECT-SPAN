<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);

$alert_type = '';
$alert_message = '';

// Fetch lookup data
$jenisprofil = $pdo->query("SELECT * FROM lookup_jenisprofil")->fetchAll(PDO::FETCH_ASSOC);
$status_list = $pdo->query("SELECT * FROM lookup_status")->fetchAll(PDO::FETCH_ASSOC);
$bahagianunit = $pdo->query("SELECT * FROM lookup_bahagianunit")->fetchAll(PDO::FETCH_ASSOC);
$kategori = $pdo->query("SELECT * FROM lookup_kategori")->fetchAll(PDO::FETCH_ASSOC);
$kategoriuser = $pdo->query("SELECT * FROM lookup_kategoriuser")->fetchAll(PDO::FETCH_ASSOC);
$jenisperalatan = $pdo->query("SELECT * FROM lookup_jenisperalatan")->fetchAll(PDO::FETCH_ASSOC);
$kaedahPembangunan = $pdo->query("SELECT * FROM lookup_kaedahPembangunan")->fetchAll(PDO::FETCH_ASSOC);
$pembekal = $pdo->query("SELECT * FROM lookup_pembekal")->fetchAll(PDO::FETCH_ASSOC);
$penyelenggaraan = $pdo->query("SELECT * FROM lookup_penyelenggaraan")->fetchAll(PDO::FETCH_ASSOC);
$userprofile = $pdo->query("SELECT * FROM lookup_userprofile")->fetchAll(PDO::FETCH_ASSOC);
$carta = $pdo->query("SELECT * FROM lookup_carta")->fetchAll(PDO::FETCH_ASSOC);
$pic = $pdo->query("SELECT * FROM lookup_pic")->fetchAll(PDO::FETCH_ASSOC);

// User login
if (!isset($_SESSION['userlog']['id_userlog'])) {
    throw new Exception("User not logged in.");
}
$id_userlog = $_SESSION['userlog']['id_userlog'];

// TAMBAH JENIS PROFIL BARU
if (isset($_POST['save_jenisprofil'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO lookup_jenisprofil (jenisprofil) VALUES (:jenis)");
        $stmt->execute([
            ':jenis' => $_POST['new_jenisprofil']
        ]);
        // Refresh dropdown
        $jenisprofil = $pdo->query("SELECT * FROM lookup_jenisprofil")->fetchAll(PDO::FETCH_ASSOC);
        $alert_type = 'success';
        $alert_message = 'Jenis Profil berjaya ditambah!';
    } catch (Exception $e) {
        $alert_type = 'danger';
        $alert_message = 'Ralat: ' . $e->getMessage();
    }
    header("Location: daftar_profil.php");
    exit;
}
// TAMBAH KATEGORI BARU
if (isset($_POST['save_kategori'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO lookup_kategori (kategori) VALUES (:kategori)");
        $stmt->execute([
            ':kategori' => $_POST['new_kategori']
        ]);
        // Refresh dropdown
        $kategori = $pdo->query("SELECT * FROM lookup_kategori")->fetchAll(PDO::FETCH_ASSOC);
        $alert_type = 'success';
        $alert_message = 'Kategori berjaya ditambah!';
    } catch (Exception $e) {
        $alert_type = 'danger';
        $alert_message = 'Ralat: ' . $e->getMessage();
    }
    header("Location: daftar_profil.php");
    exit;
}
// TAMBAH JENIS PERALATAN BARU
if (isset($_POST['save_jenisperalatan'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO lookup_jenisperalatan (jenis_peralatan) VALUES (:jenis)");
        $stmt->execute([
            ':jenis' => $_POST['new_jenisperalatan']
        ]);
        // Refresh dropdown
        $jenisperalatan = $pdo->query("SELECT * FROM lookup_jenisperalatan")->fetchAll(PDO::FETCH_ASSOC);
        $alert_type = 'success';
        $alert_message = 'Jenis Peralatan berjaya ditambah!';
    } catch (Exception $e) {
        $alert_type = 'danger';
        $alert_message = 'Ralat: ' . $e->getMessage();
    }
    header("Location: daftar_profil.php");
    exit;
}
// TAMBAH PEMBEKAL BARU
if (isset($_POST['save_pembekal'])) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO lookup_pembekal (
                nama_syarikat, 
                alamat1, alamat2, poskod, bandar, negeri,
                tempoh_kontrak, 
                id_PIC
            ) VALUES (
                :nama_syarikat, 
                :alamat1, :alamat2, :poskod, :bandar, :negeri,
                :tempoh_kontrak,
                :id_PIC
            )
        ");

        $stmt->execute([
            ':nama_syarikat' => $_POST['new_nama_syarikat'],
            ':alamat1'       => $_POST['new_alamat1'] ?? null,
            ':alamat2'       => $_POST['new_alamat2'] ?? null,
            ':poskod'        => $_POST['new_poskod'] ?? null,
            ':bandar'        => $_POST['new_bandar'] ?? null,
            ':negeri'        => $_POST['new_negeri'] ?? null,
            ':tempoh_kontrak'=> $_POST['new_tempoh_kontrak'] ?? null,
            ':id_PIC'        => $_POST['new_id_PIC'] ?: null
        ]);

        // refresh dropdown
        $pembekal = $pdo->query("SELECT * FROM lookup_pembekal")->fetchAll(PDO::FETCH_ASSOC);

        $alert_type = 'success';
        $alert_message = 'Pembekal berjaya ditambah!';
    } catch (Exception $e) {
        $alert_type = 'danger';
        $alert_message = 'Ralat: ' . $e->getMessage();
    }

    header("Location: daftar_profil.php");
    exit;
}
// TAMBAH PIC BARU
if (isset($_POST['save_pic'])) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO lookup_pic (nama_PIC, emel_PIC, notelefon_PIC, fax_PIC, jawatan_PIC)
            VALUES (:nama_PIC, :emel_PIC, :notelefon_PIC, :fax_PIC, :jawatan_PIC)
        ");
        $stmt->execute([
            ':nama_PIC' => $_POST['new_nama_PIC'],
            ':emel_PIC' => $_POST['new_emel_PIC'] ?? null,
            ':notelefon_PIC' => $_POST['new_notelefon_PIC'] ?? null,
            ':fax_PIC' => $_POST['new_fax_PIC'] ?? null,
            ':jawatan_PIC' => $_POST['new_jawatan_PIC'] ?? null
        ]);
        // Refresh dropdown
        $pic = $pdo->query("SELECT * FROM lookup_pic")->fetchAll(PDO::FETCH_ASSOC);
        $alert_type = 'success';
        $alert_message = 'PIC berjaya ditambah!';
    } catch (Exception $e) {
        $alert_type = 'danger';
        $alert_message = 'Ralat: ' . $e->getMessage();
    }
    header("Location: daftar_profil.php");
    exit;
}

//  PROCESS FORM SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $sql = "INSERT INTO profil (
            id_userlog, id_status, id_jenisprofil, nama_profil, objektif_profil,
            id_pemilik_profil, tarikh_mula, tarikh_siap, tarikh_guna,
            tarikh_dibeli, tempoh_warranty, expired_warranty,
            id_kategori, id_jenisperalatan, id_kategoriuser,
            bahasa_pengaturcaraan, pangkalan_data, rangkaian, integrasi,
            id_kaedahpembangunan, id_pembekal, inhouse,
            lokasi, no_siri, jenama_model,
            bil_pengguna, bil_modul, id_penyelenggaraan,
            tarikh_akhir_penyelenggaraan,
            kos_perkakasan, kos_perisian, kos_lesen_perisian,
            kos_penyelenggaraan, kos_lain, description_kos,
            kos_keseluruhan,
            nama_entiti, alamat_pejabat, id_bahagianunit,
            nama_ketua, nama_cio, nama_ictso,
            pengurus_akses, pegawai_rujukan,
            id_carta
        ) VALUES (
            :id_userlog, :id_status, :id_jenisprofil, :nama_profil, :objektif_profil,
            :id_pemilik_profil, :tarikh_mula, :tarikh_siap, :tarikh_guna,
            :tarikh_dibeli, :tempoh_warranty, :expired_warranty,
            :id_kategori, :id_jenisperalatan, :id_kategoriuser,
            :bahasa_pengaturcaraan, :pangkalan_data, :rangkaian, :integrasi,
            :id_kaedahpembangunan, :id_pembekal, :inhouse,
            :lokasi, :no_siri, :jenama_model,
            :bil_pengguna, :bil_modul, :id_penyelenggaraan,
            :tarikh_akhir_penyelenggaraan,
            :kos_perkakasan, :kos_perisian, :kos_lesen_perisian,
            :kos_penyelenggaraan, :kos_lain, :description_kos,
            (:kos_perkakasan + :kos_perisian + :kos_lesen_perisian + :kos_penyelenggaraan + :kos_lain),
            :nama_entiti, :alamat_pejabat, :id_bahagianunit,
            :nama_ketua, :nama_cio, :nama_ictso,
            :pengurus_akses, :pegawai_rujukan,
            :id_carta
        )";

        $stmt = $pdo->prepare($sql);

        // BIND PARAMS
        $params = [
            'id_userlog' => $id_userlog,
            'id_status' => $_POST['id_status'],
            'id_jenisprofil' => $_POST['id_jenisprofil'],
            'nama_profil' => $_POST['nama_profil'],
            'objektif_profil' => $_POST['objektif_profil'] ?? null,
            'id_pemilik_profil' => $_POST['id_pemilik_profil'] ?: null,

            'tarikh_mula' => $_POST['tarikh_mula'] ?: null,
            'tarikh_siap' => $_POST['tarikh_siap'] ?: null,
            'tarikh_guna' => $_POST['tarikh_guna'] ?: null,
            'tarikh_dibeli' => $_POST['tarikh_dibeli'] ?: null,
            'tempoh_warranty' => $_POST['tempoh_warranty'] ?: null,
            'expired_warranty' => $_POST['expired_warranty'] ?: null,

            'id_kategori' => $_POST['id_kategori'] ?: null,
            'id_jenisperalatan' => !empty($_POST['id_jenisperalatan']) ? $_POST['id_jenisperalatan'] : null,
            'id_kategoriuser' => $_POST['id_kategoriuser'] ?: null,

            'bahasa_pengaturcaraan' => $_POST['bahasa_pengaturcaraan'] ?? null,
            'pangkalan_data' => $_POST['pangkalan_data'] ?? null,
            'rangkaian' => $_POST['rangkaian'] ?? null,
            'integrasi' => $_POST['integrasi'] ?? null,

            'id_kaedahpembangunan' => $_POST['id_kaedahpembangunan'] ?: null,
            'id_pembekal' => $_POST['id_pembekal'] ?: null,
            'inhouse' => $_POST['inhouse'] ?: null,

            'lokasi' => $_POST['lokasi'] ?? null,
            'no_siri' => !empty($_POST['no_siri']) ? $_POST['no_siri'] : null,
            'jenama_model' => !empty($_POST['jenama_model']) ? $_POST['jenama_model'] : null,

            'bil_pengguna' => $_POST['bil_pengguna'] ?? null,
            'bil_modul' => $_POST['bil_modul'] ?? null,

            'id_penyelenggaraan' => $_POST['id_penyelenggaraan'] ?: null,
            'tarikh_akhir_penyelenggaraan' => $_POST['tarikh_akhir_penyelenggaraan'] ?: null,

            'kos_perkakasan' => $_POST['kos_perkakasan'] ?: 0,
            'kos_perisian' => $_POST['kos_perisian'] ?: 0,
            'kos_lesen_perisian' => $_POST['kos_lesen_perisian'] ?: 0,
            'kos_penyelenggaraan' => $_POST['kos_penyelenggaraan'] ?: 0,
            'kos_lain' => $_POST['kos_lain'] ?: 0,
            'description_kos' => $_POST['description_kos'] ?? null,

            'nama_entiti' => $_POST['nama_entiti'] ?? null,
            'alamat_pejabat' => $_POST['alamat_pejabat'] ?? null,
            'id_bahagianunit' => $_POST['id_bahagianunit'],

            'nama_ketua' => $_POST['nama_ketua'] ?: null,
            'nama_cio' => $_POST['nama_cio'] ?: null,
            'nama_ictso' => $_POST['nama_ictso'] ?: null,

            'pengurus_akses' => $_POST['pengurus_akses'] ?: null,
            'pegawai_rujukan' => $_POST['pegawai_rujukan'],

            'id_carta' => $_POST['id_carta'] ?: null,
        ];

        $stmt->execute($params);

        $alert_type = 'success';
        $alert_message = 'Profil berjaya disimpan!';

    } catch (Exception $e) {
        $alert_type = 'danger';
        $alert_message = 'Ralat: ' . $e->getMessage();
    }
}

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <!-- FIXED HEADER + HOME -->
        <?php include 'header.php'; ?>

        <div class="main-header mt-3 mb-3"><i class="bi bi-pencil-square"></i>Daftar Profil</div>

        <!-- ALERT POPUP (Sama macam form_pengguna.php) -->
        <div id="alert-container" style="position: fixed; top: 95px; right: 20px; z-index: 1050; min-width: 300px;">
            <?php if (!empty($alert_type) && !empty($alert_message)): ?>
                <div class="alert alert-<?= $alert_type ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($alert_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
        <script>
            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('#alert-container .alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        </script>


        <form method="POST" class="section-card" autocomplete="off">
            <!-- MAKLUMAT ASAS -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="id_status" class="form-select" required>
                        <option value="">-- Pilih Status --</option>
                        <?php foreach ($status_list as $s): ?>
                            <option value="<?= $s['id_status'] ?>"><?= $s['status'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Jenis Profil <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select name="id_jenisprofil" id="jenisProfilSelect" class="form-select" required>
                            <option value="">-- Pilih Jenis Profil --</option>
                            <?php foreach ($jenisprofil as $jp): ?>
                                <?php if (!in_array($jp['id_jenisprofil'], [1, 2])): // hide PEMBEKAL & PENGGUNA ?>
                                    <option value="<?= $jp['id_jenisprofil'] ?>"><?= $jp['jenisprofil'] ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>

                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalJenisProfil">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- MAKLUMAT PROFIL -->
            <div class="section-title"><i class="bi bi-info-circle"></i> MAKLUMAT PROFIL</div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label>Nama Profil <span class="text-danger">*</span></label>
                    <input type="text" name="nama_profil" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Pemilik Profil</label>
                    <select name="id_pemilik_profil" class="form-select">
                        <option value="">-- Pilih Bahagian / Unit --</option>
                        <?php foreach ($bahagianunit as $b): ?>
                            <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Objektif Profil</label>
                    <textarea name="objektif_profil" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tarikh Mula Pembangunan</label>
                    <input type="date" name="tarikh_mula" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tarikh Siap Pembangunan</label>
                    <input type="date" name="tarikh_siap" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tarikh Digunakan</label>
                    <input type="date" name="tarikh_guna" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tarikh Dibeli / Diterima</label>
                    <input type="date" name="tarikh_dibeli" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tempoh Warranty (Tahun)</label>
                    <input type="text" name="tempoh_warranty" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tarikh Luput Warranty</label>
                    <input type="date" name="expired_warranty" class="form-control">
                </div>
            </div>

            <!-- MAKLUMAT SPECIFIC -->
            <hr>
            <p style="font-style: italic;">[sila isi maklumat yang berkenaan sahaja]</p>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Kategori</label>
                    <div class="input-group">
                        <select name="id_kategori" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= $k['id_kategori'] ?>"><?= $k['kategori'] ?></option>
                            <?php endforeach; ?>
                        </select>

                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalKategori">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Jenis Peralatan</label>
                    <div class="input-group">
                        <select name="id_jenisperalatan" class="form-select">
                            <option value="">-- Pilih Jenis --</option>
                            <?php foreach ($jenisperalatan as $j): ?>
                                <option value="<?= $j['id_jenisperalatan'] ?>"><?= $j['jenis_peralatan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalJenisPeralatan">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Kategori Jenis Pengguna:</label>
                    <div class="row">
                        <!-- Jenis Dalaman -->
                        <div class="col-md-6 mb-1">
                            <p style="font-weight: 500; font-size: 14px">Dalaman (Jabatan/Bahagian)</p>
                            <select name="jenis_dalaman" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                        <!-- Jenis Umum -->
                        <div class="col-md-6 mb-1">
                            <p style="font-weight: 500; font-size: 14px">Umum (Orang Awam)</p>
                            <select name="jenis_umum" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="id_kategoriuser" id="id_kategoriuser_hidden">
                </div>
                <script>
                    const kategoriData = <?= json_encode($kategoriuser) ?>;

                    document.querySelector("select[name='jenis_dalaman']").addEventListener('change', updateKategoriUser);
                    document.querySelector("select[name='jenis_umum']").addEventListener('change', updateKategoriUser);

                    function updateKategoriUser() {
                        const d = document.querySelector("select[name='jenis_dalaman']").value;
                        const u = document.querySelector("select[name='jenis_umum']").value;

                        // Cari id_kategoriuser matching pilihan
                        const match = kategoriData.find(k =>
                            k.jenis_dalaman == d && k.jenis_umum == u
                        );

                        document.getElementById("id_kategoriuser_hidden").value =
                            match ? match.id_kategoriuser : "";
                    }
                </script>

                <div class="col-md-6">
                    <label class="form-label">Bahasa Pengaturcaraan</label>
                    <textarea name="bahasa_pengaturcaraan" class="form-control" rows="2"></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Jenis Pangkalan Data</label>
                    <textarea name="pangkalan_data" class="form-control" rows="2"></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Rangkaian Digunakan</label>
                    <textarea name="rangkaian" class="form-control" rows="2"></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Integrasi (Sistem Lain)</label>
                    <textarea name="integrasi" class="form-control" rows="2"></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kaedah Pembangunan</label>
                    <select name="id_kaedahpembangunan" class="form-select">
                        <option value="">-- Pilih Kaedah --</option>
                        <?php foreach ($kaedahPembangunan as $kp): ?>
                            <option value="<?= $kp['id_kaedahPembangunan'] ?>"><?= $kp['kaedahPembangunan'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Pembekal</label>
                    <div class="input-group">
                        <select name="id_pembekal" class="form-select">
                            <option value="">-- Pilih Pembekal --</option>
                            <?php foreach ($pembekal as $p): ?>
                                <option value="<?= $p['id_pembekal'] ?>"><?= $p['nama_syarikat'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPembekal">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Dalaman</label>
                    <select name="inhouse" class="form-select">
                        <option value="">-- Pilih Bahagian / Unit --</option>
                        <?php foreach ($bahagianunit as $b): ?>
                            <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Lokasi Diletakkan (Bangunan, Aras, Unit)</label>
                    <input type="text" name="lokasi" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nombor Siri / ID</label>
                    <input type="text" name="no_siri" class="form-control" value="" autocomplete="off">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jenama / Model</label>
                    <input type="text" name="jenama_model" class="form-control" value="" autocomplete="off">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Angaran Bilangan Pengguna</label>
                    <input type="text" name="bil_pengguna" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Bilangan Modul</label>
                    <input type="text" name="bil_modul" class="form-control">
                </div>

                <div class="col-md-8">
                    <label class="form-label">Kaedah Penyelenggaraan</label>
                    <select name="id_penyelenggaraan" class="form-select">
                        <option value="">-- Pilih Bahagian / Unit --</option>
                        <?php foreach ($penyelenggaraan as $py): ?>
                            <option value="<?= $py['id_penyelenggaraan'] ?>"><?= $py['penyelenggaraan'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tarikh Akhir Penyelenggaraan</label>
                    <input type="date" name="tarikh_akhir_penyelenggaraan" class="form-control">
                </div>
            </div>

            <!-- MAKLUMAT KOS -->
            <div class="section-title"><i class="bi bi-cash-stack"></i> MAKLUMAT KOS</div>

            <div class="row g-3 mb-4">
                <?php
                $fields = [
                    "kos_perkakasan" => "Kos Perkakasan",
                    "kos_perisian" => "Kos Perisian",
                    "kos_lesen_perisian" => "Kos Lesen Perisian",
                    "kos_penyelenggaraan" => "Kos Penyelenggaraan",
                    "kos_lain" => "Kos Lain"
                ];
                foreach ($fields as $name => $label): ?>
                    <div class="col-md-3">
                        <label class="form-label"><?= $label ?></label>
                        <input type="number" step="0.01" name="<?= $name ?>" class="form-control">
                    </div>
                <?php endforeach; ?>

                <div class="col-md-9">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="description_kos" class="form-control">
                </div>
            </div>

            <!-- MAKLUMAT ENTITI -->
            <div class="section-title"><i class="bi bi-person-lines-fill"></i> MAKLUMAT ENTITI</div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Entiti</label>
                    <input type="text" name="nama_entiti" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Bahagian Entiti</label>
                    <select name="id_bahagianunit" class="form-select">
                        <option value="">-- Pilih Bahagian / Unit --</option>
                        <?php foreach ($bahagianunit as $b): ?>
                            <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Alamat Pejabat</label>
                    <input type="text" name="alamat_pejabat" class="form-control">
                </div>

                <?php
                $pegawai_fields = [
                    "nama_ketua" => "Nama Ketua Bahagian",
                    "nama_cio" => "Nama CIO",
                    "nama_ictso" => "Nama ICTSO"
                ];
                foreach ($pegawai_fields as $name => $label): ?>
                    <div class="col-md-4">
                        <label class="form-label"><?= $label ?></label>
                        <select name="<?= $name ?>" class="form-select">
                            <option value="">-- Pilih --</option>
                            <?php foreach ($userprofile as $u): ?>
                                <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>

                <div class="col-md-6">
                    <label class="form-label">Pengurus Akses Pengguna</label>
                    <select name="pengurus_akses" class="form-select">
                        <option value="">-- Pilih Bahagian / Unit --</option>
                        <?php foreach ($bahagianunit as $b): ?>
                            <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label>Pegawai Rujukan <span class="text-danger">*</span></label>
                    <select name="pegawai_rujukan" class="form-select" required>
                        <option value="">-- Pilih --</option>
                            <?php foreach ($userprofile as $u): ?>
                                <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?></option>
                            <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Carta Organisasi</label>
                    <select name="id_carta" class="form-select">
                        <option value="">-- Pilih Carta --</option>
                        <?php foreach ($carta as $c): ?>
                            <option value="<?= $c['id_carta'] ?>"><?= $c['carta'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- SAVE BUTTON -->
            <div class="text-center mt-5">
                <button type="submit" class="btn btn-primary px-4">Simpan Profil</button>
            </div>

        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const toast = document.getElementById('liveToast');
        if ("<?= $alert_type ?>" !== "") {
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toast);
            toastBootstrap.show();
        }
    </script>

    <!-- MODAL TAMBAH JENIS PROFIL -->
    <div class="modal fade" id="modalJenisProfil" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jenis Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Nama Jenis Profil <span class="text-danger">*</span></label>
                    <input type="text" name="new_jenisprofil" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_jenisprofil" class="btn btn-primary">Simpan</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <!-- MODAL: TAMBAH KATEGORI -->
    <div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Nama </label>
                    <label>Nama Kategori Baru <span class="text-danger">*</span></label>
                    <input type="text" name="new_kategori" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_kategori" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <!-- MODAL TAMBAH JENIS PERALATAN -->
    <div class="modal fade" id="modalJenisPeralatan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                <h5 class="modal-title">Tambah Jenis Peralatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                <label>Nama Jenis Peralatan <span class="text-danger">*</span></label>
                <input type="text" name="new_jenisperalatan" class="form-control" required>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="save_jenisperalatan" class="btn btn-primary">Simpan</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <!-- MODAL TAMBAH PEMBEKAL -->
    <div class="modal fade" id="modalPembekal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pembekal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- NAMA SYARIKAT -->
                    <label>Nama Syarikat <span class="text-danger">*</span></label>
                    <input type="text" name="new_nama_syarikat" class="form-control" required>
                    <!-- ALAMAT -->
                    <label class="form-label fw-semibold mt-3">Alamat Syarikat</label>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <input type="text" name="new_alamat1" class="form-control" placeholder="Alamat Baris 1">
                        </div>
                        <div class="col-md-12">
                            <input type="text" name="new_alamat2" class="form-control" placeholder="Alamat Baris 2 (jika ada)">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="new_poskod" class="form-control" placeholder="Poskod">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="new_bandar" class="form-control" placeholder="Bandar">
                        </div>
                        <div class="col-md-3">
                            <select name="new_negeri" class="form-select">
                                <option value="">Negeri</option>
                                <option>Johor</option>
                                <option>Kedah</option>
                                <option>Kelantan</option>
                                <option>Melaka</option>
                                <option>Negeri Sembilan</option>
                                <option>Pahang</option>
                                <option>Perak</option>
                                <option>Perlis</option>
                                <option>Pulau Pinang</option>
                                <option>Sabah</option>
                                <option>Sarawak</option>
                                <option>Selangor</option>
                                <option>Terengganu</option>
                                <option>WP Kuala Lumpur</option>
                                <option>WP Putrajaya</option>
                                <option>WP Labuan</option>
                            </select>
                        </div>
                    </div>
                    <!-- TEMPOH KONTRAK -->
                    <label class="form-label fw-semibold mt-3">Tempoh Kontrak</label>
                    <input type="text" name="new_tempoh_kontrak" class="form-control">
                    <!--PIC-->
                    <label class="form-label fw-semibold mt-3">PIC</label>
                    <div class="input-group">
                        <select name="new_id_PIC" class="form-select">
                            <option value="">-- Pilih PIC --</option>
                            <?php foreach ($pic as $p): ?>
                                <option value="<?= $p['id_PIC'] ?>"><?= $p['nama_PIC'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPIC">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_pembekal" class="btn btn-primary">Simpan</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <!-- MODAL TAMBAH PIC -->
    <div class="modal fade" id="modalPIC" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah PIC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Nama PIC</label>
                    <input type="text" name="new_nama_PIC" class="form-control">
                    
                    <label class="form-label mt-2">Emel</label>
                    <input type="email" name="new_emel_PIC" class="form-control">
                    
                    <label class="form-label mt-2">No Telefon</label>
                    <input type="text" name="new_notelefon_PIC" class="form-control">
                    
                    <label class="form-label mt-2">Fax</label>
                    <input type="text" name="new_fax_PIC" class="form-control">
                    
                    <label class="form-label mt-2">Jawatan</label>
                    <input type="text" name="new_jawatan_PIC" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_pic" class="btn btn-primary">Simpan</button>
                </div>
            </form>
            </div>
        </div>
    </div>

</body>
</html>
