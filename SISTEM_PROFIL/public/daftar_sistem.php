<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);
$message = '';

// Ambil data lookup dari DB
$statuses = $pdo->query("SELECT * FROM LOOKUP_STATUS")->fetchAll(PDO::FETCH_ASSOC);
$jenisprofils = $pdo->query("SELECT * FROM LOOKUP_JENISPROFIL")->fetchAll(PDO::FETCH_ASSOC);
$bahagianunits = $pdo->query("SELECT * FROM LOOKUP_BAHAGIANUNIT ORDER BY bahagianunit ASC")->fetchAll(PDO::FETCH_ASSOC);
$kategoris = $pdo->query("SELECT * FROM LOOKUP_KATEGORI")->fetchAll(PDO::FETCH_ASSOC);
$penyelenggaraans = $pdo->query("SELECT * FROM LOOKUP_PENYELENGGARAAN")->fetchAll(PDO::FETCH_ASSOC);
$kaedahPembangunans = $pdo->query("SELECT * FROM LOOKUP_KAEDAHPEMBANGUNAN")->fetchAll(PDO::FETCH_ASSOC);
$outsources = $pdo->query("SELECT * FROM LOOKUP_OUTSOURCE")->fetchAll(PDO::FETCH_ASSOC);
$userprofiles = $pdo->query("SELECT * FROM LOOKUP_USERPROFILE")->fetchAll(PDO::FETCH_ASSOC);
$kategoriusers = $pdo->query("SELECT * FROM LOOKUP_KATEGORIUSER")->fetchAll(PDO::FETCH_ASSOC);
$cartas = $pdo->query("SELECT * FROM LOOKUP_CARTA ORDER BY carta ASC")->fetchAll(PDO::FETCH_ASSOC);
$akses_dalaman = $_POST['akses_dalaman'] ?? null;
$akses_umum = $_POST['akses_umum'] ?? null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $pdo->beginTransaction();

        $outsource_id = null;
        // HANDLE OUTSOURCE / INHOUSE
        if ($_POST['kaedahPembangunan'] == 2) { // OUTSOURCE

            // 1. Jika syarikat sedia ada dipilih
            if (!empty($_POST['outsource_id']) && $_POST['outsource_id'] != 'other') {
                $outsource_id = $_POST['outsource_id'];
            } 
            else {
                // 2. Insert syarikat baru
                $stmt = $pdo->prepare("INSERT INTO LOOKUP_OUTSOURCE 
                    (nama_syarikat, alamat_syarikat) VALUES (?, ?)");
                $stmt->execute([
                    $_POST['manual_nama_syarikat'],
                    $_POST['manual_alamat_syarikat']
                ]);
                $outsource_id = $pdo->lastInsertId();
            }
        } 
        else {
            // INHOUSE
            $outsource_id = null;
        }

        // Insert PROFIL_SISTEM
        $stmt = $pdo->prepare("
            INSERT INTO PROFIL_SISTEM (id_user, id_jenisprofil, id_userprofile, id_status)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['userlog']['id'],   // user login
            $_POST['jenisprofil'],
            $_POST['userprofile'],
            $_POST['status']
        ]);

        $last_profilsistem_id = $pdo->lastInsertId();


        // Tentukan id_bahagianunit untuk table SISTEM
        $id_bahagianunit_final = null;

        if ($_POST['kaedahPembangunan'] == 1) { 
            // INHOUSE
            $id_bahagianunit_final = $_POST['inhouse_bahagianunit'];
        } else { 
            // OUTSOURCE — TIDAK ADA BAHAGIANUNIT
            $id_bahagianunit_final = null;
        }


        // Insert SISTEM
        $stmt2 = $pdo->prepare("
            INSERT INTO SISTEM 
            (id_profilsistem, nama_sistem, objektif, pemilik_sistem, tarikh_mula, tarikh_siap, tarikh_guna, 
            bil_pengguna, bil_modul, id_kategori, bahasa_pengaturcaraan, pangkalan_data, rangkaian, integrasi,
            id_penyelenggaraan, id_kaedahPembangunan, id_outsource, id_bahagianunit)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt2->execute([
            $last_profilsistem_id,
            $_POST['nama_sistem'],
            $_POST['objektif'],
            $_POST['pemilik_sistem'],
            $_POST['tarikh_mula'],
            $_POST['tarikh_siap'],
            $_POST['tarikh_guna'],
            $_POST['bil_pengguna'],
            $_POST['bil_modul'],
            $_POST['kategori'],
            $_POST['bahasa_pengaturcaraan'],
            $_POST['pangkalan_data'],
            $_POST['rangkaian'],
            $_POST['integrasi'],
            $_POST['penyelenggaraan'],
            $_POST['kaedahPembangunan'],
            $outsource_id,
            $id_bahagianunit_final   // FIX PENTING
        ]);


        // Insert to KOS
        $stmtKos = $pdo->prepare("
            INSERT INTO KOS 
            (id_profilsistem, kos_keseluruhan, kos_perkakasan, kos_perisian, kos_lesen_perisian, kos_penyelenggaraan, kos_lain)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmtKos->execute([
            $last_profilsistem_id,
            $_POST['kos_keseluruhan'] ?: 0,
            $_POST['kos_perkakasan'] ?: 0,
            $_POST['kos_perisian'] ?: 0,
            $_POST['kos_lesen_perisian'] ?: 0,
            $_POST['kos_penyelenggaraan'] ?: 0,
            $_POST['kos_lain'] ?: 0
        ]);

        // Query lookup_kategoriuser untuk dapatkan id
        $stmtKategori = $pdo->prepare("
            SELECT id_kategoriuser FROM lookup_kategoriuser 
            WHERE jenis_dalaman = ? AND jenis_umum = ?
            LIMIT 1
        ");
        $stmtKategori->execute([$akses_dalaman, $akses_umum]);
        $id_kategoriuser = $stmtKategori->fetchColumn();

        if (!$id_kategoriuser) {
            // fallback jika kombinasi tak wujud
            $id_kategoriuser = 3; // contoh: 3 = Dalaman & Umum = 0
        }

        // Insert AKSES
        $stmtAkses = $pdo->prepare("
            INSERT INTO AKSES 
            (id_profilsistem, id_bahagianunit, id_kategoriuser)
            VALUES (?, ?, ?)
        ");
        $stmtAkses->execute([
            $last_profilsistem_id,
            $_POST['akses_bahagianunit'],
            $id_kategoriuser
        ]);

        // Insert ENTITI
        $stmtEntiti = $pdo->prepare("
            INSERT INTO ENTITI 
            (id_profilsistem, nama_entiti, tarikh_kemaskini, id_bahagianunit, id_userprofile, cio, ictso, id_carta)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtEntiti->execute([
            $last_profilsistem_id,
            $_POST['nama_entiti'],
            $_POST['tarikh_kemaskini'],
            $_POST['entiti_bahagianunit'],
            $_POST['ketua_userprofile'],
            $_POST['cio_userprofile'],
            $_POST['ictso_userprofile'],
            $_POST['entiti_carta']
        ]);

        // Insert PEGAWAI RUJUKAN SISTEM
        $stmtPegawai = $pdo->prepare("
            INSERT INTO PEGAWAI_RUJUKAN_SISTEM 
            (id_profilsistem, id_userprofile)
            VALUES (?, ?)
        ");
        $stmtPegawai->execute([
            $last_profilsistem_id,
            $_POST['pegawai_rujukan_sistem']
        ]);


        $pdo->commit();
        $message = "<div class='alert alert-success'>Profil Sistem Berjaya Disimpan!</div>";

    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert alert-danger'>Ralat: " . $e->getMessage() . "</div>";
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
                <div class="col-md-6">
                    <label>Pegawai Profil</label>
                    <select name="userprofile" class="form-select" required>
                        <option value="">-- Pilih Pegawai --</option>
                        <?php foreach ($userprofiles as $u): ?>
                            <option value="<?= $u['id_userprofile'] ?>"><?= $u['nama_user'] ?> (<?= $u['jawatan_user'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


            <!-- SISTEM -->
            <div class="section-title">A. MAKLUMAT SISTEM</div>
            <div class="row g-4 mb-3">
                <div class="col-md-6">
                    <label>Nama Sistem</label>
                    <input type="text" name="nama_sistem" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Pemilik Sistem</label>
                    <select name="pemilik_sistem" class="form-select" required>
                        <option value="">-- Pilih Bahagian / Unit --</option>
                        <?php foreach ($bahagianunits as $b): ?>
                            <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12">
                    <label>Objektif</label>
                    <textarea name="objektif" rows="3" class="form-control"></textarea>
                </div>
                <div class="col-md-4">
                    <label>Tarikh Mula</label>
                    <input type="date" name="tarikh_mula" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Tarikh Siap</label>
                    <input type="date" name="tarikh_siap" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Tarikh Guna</label>
                    <input type="date" name="tarikh_guna" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Bilangan Pengguna</label>
                    <input type="text" name="bil_pengguna" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Bilangan Modul</label>
                    <input type="text" name="bil_modul" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Kategori Sistem</label>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategoris as $k): ?>
                            <option value="<?= $k['id_kategori'] ?>"><?= $k['kategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Bahasa Pengaturcaraan</label>
                    <input type="text" name="bahasa_pengaturcaraan" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Pangkalan Data</label>
                    <input type="text" name="pangkalan_data" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Rangkaian</label>
                    <textarea name="rangkaian" rows="2" class="form-control"></textarea>
                </div>
                <div class="col-md-6">
                    <label>Integrasi</label>
                    <textarea name="integrasi" rows="2" class="form-control"></textarea>
                </div>
                <div class="col-md-4">
                    <label>Penyelenggaraan</label>
                    <select name="penyelenggaraan" class="form-select">
                        <option value="">-- Pilih Penyelenggaraan --</option>
                        <?php foreach ($penyelenggaraans as $p): ?>
                            <option value="<?= $p['id_penyelenggaraan'] ?>"><?= $p['penyelenggaraan'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                <label>Kaedah Pembangunan</label>
                <select name="kaedahPembangunan" id="kaedahPembangunan" class="form-select" required>
                    <option value="">-- Pilih Kaedah Pembangunan --</option>
                    <?php foreach ($kaedahPembangunans as $kp): ?>
                        <option value="<?= $kp['id_kaedahPembangunan'] ?>"><?= $kp['kaedahPembangunan'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- OUTSOURCE / INHOUSE SECTION -->
            <div id="outsourceBox" class="conditional-box" style="display:none;">
                <div class="sub-section-header">Maklumat Syarikat (Outsource)</div>
                <!-- Pilih syarikat outsource -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Syarikat</label>
                        <select name="outsource_id" id="outsourceSelect" class="form-select">
                            <option value="">-- Pilih Syarikat --</option>
                            <?php foreach ($outsources as $o): ?>
                                <option value="<?= $o['id_outsource'] ?>"><?= $o['nama_syarikat'] ?></option>
                            <?php endforeach; ?>
                            <option value="other">Tambah Baru...</option>
                        </select>
                    </div>
                </div>
                <!-- Jika syarikat tiada dalam DB -->
                <div id="manualOutsource" style="display:none;">
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label>Nama Syarikat</label>
                            <input type="text" name="manual_nama_syarikat" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Alamat Syarikat</label>
                            <textarea name="manual_alamat_syarikat" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <!-- Maklumat PIC -->
                <div id="picBox" style="display:none; margin-top:15px;">
                    <div class="sub-section-header">Maklumat PIC</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>PIC</label>
                            <select name="pic_id" id="picSelect" class="form-select">
                                <option value="">-- Pilih PIC --</option>
                                <option value="other">Tambah Baru...</option>
                            </select>
                        </div>
                    </div>
                    <div id="manualPIC" style="display:none;">
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label>Nama PIC</label>
                                <input type="text" name="manual_pic_nama" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Emel PIC</label>
                                <input type="text" name="manual_pic_emel" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>No Telefon PIC</label>
                                <input type="text" name="manual_pic_telefon" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Fax PIC</label>
                                <input type="text" name="manual_pic_fax" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Jawatan PIC</label>
                                <input type="text" name="manual_pic_jawatan" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- INHOUSE -->
            <div id="inhouseBox" class="conditional-box" style="display:none;">
                <div class="sub-section-header">Maklumat Inhouse</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Bahagian Bertanggungjawab</label>
                        <select name="inhouse_bahagianunit" class="form-select">
                            <option value="">-- Pilih Bahagian --</option>
                            <?php foreach ($bahagianunits as $b): ?>
                                <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>


            <!-- KOS -->
            <div class="section-title">B. MAKLUMAT KOS SISTEM</div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label>Jumlah Kos Keseluruhan (RM)</label>
                    <input type="number" step="0.01" min="0" name="kos_keseluruhan" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Kos Perkakasan (RM)</label>
                    <input type="number" step="0.01" min="0" name="kos_perkakasan" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Kos Perisian (RM)</label>
                    <input type="number" step="0.01" min="0" name="kos_perisian" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Kos Lesen Perisian (RM)</label>
                    <input type="number" step="0.01" min="0" name="kos_lesen_perisian" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Kos Penyelenggaraan (RM)</label>
                    <input type="number" step="0.01" min="0" name="kos_penyelenggaraan" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Kos Lain-lain (RM)</label>
                    <input type="number" step="0.01" min="0" name="kos_lain" class="form-control">
                </div>
            </div>


            <!-- AKSES -->
            <div class="section-title">C. MAKLUMAT AKSES SISTEM</div>
            <div class="akses-card p-3 mb-3">
                <div class="row g-4">
                    <!-- Pegawai Urus Akses -->
                    <div class="col-md-6">
                        <label class="form-label akses-subtitle">
                            <i class="bi bi-shield-lock"></i> Pegawai Mengurus Akses Sistem
                        </label>
                        <select name="akses_bahagianunit" class="form-select akses-input" required>
                            <option value="">-- Pilih Bahagian / Unit --</option>
                            <?php foreach ($bahagianunits as $b): ?>
                                <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Divider line -->
                    <div class="akses-divider my-3"></div>
                    <!-- Kategori Pengguna -->
                    <div class="col-12">
                        <label class="form-label akses-subtitle">
                            <i class="bi bi-people"></i> Kategori Pengguna Sistem
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Dalaman (Jabatan / Bahagian)</label>
                        <select name="akses_dalaman" class="form-select akses-input" required>
                            <option value="">-- Pilih --</option>
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Umum (Orang Awam)</label>
                        <select name="akses_umum" class="form-select akses-input" required>
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
                <div class="col-md-6">
                    <label>Nama Entiti</label>
                    <input type="text" name="nama_entiti" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label>Tarikh Kemaskini</label>
                    <input type="date" name="tarikh_kemaskini" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Bahagian (Entiti)</label>
                    <select name="entiti_bahagianunit" class="form-select" required>
                        <option value="">-- Pilih Bahagian --</option>
                        <?php foreach ($bahagianunits as $b): ?>
                            <option value="<?= $b['id_bahagianunit'] ?>"><?= $b['bahagianunit'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Nama Ketua Bahagian</label>
                    <select name="ketua_userprofile" class="form-select" required>
                        <option value="">-- Pilih Pegawai --</option>
                        <?php foreach ($userprofiles as $u): ?>
                            <option value="<?= $u['id_userprofile'] ?>">
                                <?= $u['nama_user'] ?> (<?= $u['jawatan_user'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Nama CIO</label>
                    <select name="cio_userprofile" class="form-select" required>
                        <option value="">-- Pilih Pegawai --</option>
                        <?php foreach ($userprofiles as $u): ?>
                            <option value="<?= $u['id_userprofile'] ?>">
                                <?= $u['nama_user'] ?> (<?= $u['jawatan_user'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Nama ICTSO</label>
                    <select name="ictso_userprofile" class="form-select" required>
                        <option value="">-- Pilih Pegawai --</option>
                        <?php foreach ($userprofiles as $u): ?>
                            <option value="<?= $u['id_userprofile'] ?>">
                                <?= $u['nama_user'] ?> (<?= $u['jawatan_user'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Carta Organisasi</label>
                    <select name="entiti_carta" class="form-select" required>
                        <option value="">-- Pilih Carta --</option>
                        <?php foreach ($cartas as $c): ?>
                            <option value="<?= $c['id_carta'] ?>"><?= $c['carta'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            
            <!-- PEGAWAI RUJUKAN -->
            <div class="section-title">E. MAKLUMAT PEGAWAI RUJUKAN SISTEM</div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label>Pegawai Rujukan Sistem</label>
                    <select name="pegawai_rujukan_sistem" class="form-select" required>
                        <option value="">-- Pilih Pegawai --</option>
                        <?php foreach ($userprofiles as $u): ?>
                            <option value="<?= $u['id_userprofile'] ?>">
                                <?= $u['nama_user'] ?> (<?= $u['jawatan_user'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Simpan Profil</button>
            </div>
        </form>

        </div>
    </div>

    <script src="../public/js/sistem.js"></script>

</body>
</html>
