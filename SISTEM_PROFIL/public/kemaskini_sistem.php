<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id'])) {
    header("Location: profil_sistem.php");
    exit;
}

$id = intval($_GET['id']);

// --- FETCH SYSTEM DATA BY ID ---
$stmt = $pdo->prepare("
    SELECT P.*, S.*
    FROM PROFIL P
    INNER JOIN SISTEM S ON P.id_profilsistem = S.id_profilsistem
    WHERE P.id_profilsistem = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Rekod tidak dijumpai!");
}

// --- Load lookups ---
$statuses           = $pdo->query("SELECT * FROM lookup_status")->fetchAll();
$bahagianunits      = $pdo->query("SELECT * FROM lookup_bahagianunit")->fetchAll();
$cartas             = $pdo->query("SELECT * FROM lookup_carta")->fetchAll();
$userprofiles       = $pdo->query("SELECT * FROM lookup_userprofile")->fetchAll();
$kategoris          = $pdo->query("SELECT * FROM lookup_kategori")->fetchAll();
$penyelenggaraans   = $pdo->query("SELECT * FROM lookup_penyelenggaraan")->fetchAll();
$kategoriusers      = $pdo->query("SELECT * FROM lookup_kategoriuser")->fetchAll();
$kaedahPembangunans = $pdo->query("SELECT * FROM lookup_kaedahpembangunan")->fetchAll();
$pembekals          = $pdo->query("SELECT * FROM lookup_pembekal")->fetchAll();
?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kemaskini Sistem | Sistem Profil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>

    <link rel="stylesheet" href="css/profil.css">
</head>

<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="content edit-sistem-page">
        <!-- HEADER -->
        <?php include 'header.php'; ?>

        <div class="page-header-box d-flex align-items-center gap-2 mt-3 mb-4">
            <i class="bi bi-pencil-square page-header-icon"></i>
            <h4 class="page-title">Kemaskini Profil Sistem</h4>
        </div>
        
        <div class="edit-sistem-card shadow-sm rounded-4 p-4">
            <form action="proses_kemaskini_sistem.php" method="POST">
                <input type="hidden" name="id_profilsistem" value="<?= $id ?>">

                <!-- ========== SECTION 1: MAKLUMAT ENTITI ========== -->
                <div class="form-section">
                    <div class="section-heading">
                        <i class="bi bi-building"></i>
                        <span>MAKLUMAT ENTITI</span>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="id_status" class="form-select" required>
                                <option value="">-- Pilih Status --</option>
                                <?php foreach ($statuses as $s): ?>
                                    <option value="<?= $s['id_status'] ?>"
                                        <?= $data['id_status'] == $s['id_status'] ? 'selected' : '' ?>>
                                        <?= $s['status'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Nama Entiti</label>
                            <input type="text" name="nama_entiti" class="form-control"
                                value="<?= htmlspecialchars($data['nama_entiti']) ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Alamat Pejabat</label>
                            <textarea name="alamat_pejabat" class="form-control" rows="2"><?= htmlspecialchars($data['alamat_pejabat']) ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bahagian/Unit</label>
                            <select name="id_bahagianunit" class="form-select" required>
                                <?php foreach ($bahagianunits as $bu): ?>
                                    <option value="<?= $bu['id_bahagianunit'] ?>"
                                        <?= $data['id_bahagianunit'] == $bu['id_bahagianunit'] ? 'selected' : '' ?>>
                                        <?= $bu['bahagianunit'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Carta Organisasi</label>
                            <select name="id_carta" class="form-select">
                                <option value="">-- Pilih Carta --</option>
                                <?php foreach ($cartas as $c): ?>
                                    <option value="<?= $c['id_carta'] ?>"
                                        <?= $data['id_carta'] == $c['id_carta'] ? 'selected' : '' ?>>
                                        <?= $c['carta'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- NAMA KETUA / CIO / ICTSO -->
                        <div class="col-md-4">
                            <label class="form-label">Nama Ketua</label>
                            <select name="nama_ketua" class="form-select" required>
                                <?php foreach ($userprofiles as $up): ?>
                                    <option value="<?= $up['id_userprofile'] ?>"
                                        <?= $data['nama_ketua'] == $up['id_userprofile'] ? 'selected' : '' ?>>
                                        <?= $up['nama_user'] ?> (<?= $up['jawatan_user'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nama CIO</label>
                            <select name="nama_cio" class="form-select" required>
                                <?php foreach ($userprofiles as $up): ?>
                                    <option value="<?= $up['id_userprofile'] ?>"
                                        <?= $data['nama_cio'] == $up['id_userprofile'] ? 'selected' : '' ?>>
                                        <?= $up['nama_user'] ?> (<?= $up['jawatan_user'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nama ICTSO</label>
                            <select name="nama_ictso" class="form-select" required>
                                <?php foreach ($userprofiles as $up): ?>
                                    <option value="<?= $up['id_userprofile'] ?>"
                                        <?= $data['nama_ictso'] == $up['id_userprofile'] ? 'selected' : '' ?>>
                                        <?= $up['nama_user'] ?> (<?= $up['jawatan_user'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                </div>                

                <!-- ========== SECTION 2: MAKLUMAT SISTEM ========== -->
                <div class="form-section">
                    <div class="section-heading">
                        <i class="bi bi-gear"></i>
                        <span>MAKLUMAT SISTEM</span>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Nama Sistem</label>
                            <input type="text" name="nama_sistem" class="form-control"
                                value="<?= htmlspecialchars($data['nama_sistem']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pemilik Sistem</label>
                            <select name="id_pemilik_sistem" class="form-select" required>
                                <?php foreach ($bahagianunits as $bu): ?>
                                    <option value="<?= $bu['id_bahagianunit'] ?>"
                                        <?= $data['id_pemilik_sistem'] == $bu['id_bahagianunit'] ? 'selected' : '' ?>>
                                        <?= $bu['bahagianunit'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Objektif Sistem</label>
                            <textarea name="objektif_sistem" class="form-control" rows="3"><?= htmlspecialchars($data['objektif_sistem']) ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tarikh Mula</label>
                            <input type="date" name="tarikh_mula" class="form-control" value="<?= $data['tarikh_mula'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tarikh Siap</label>
                            <input type="date" name="tarikh_siap" class="form-control" value="<?= $data['tarikh_siap'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tarikh Guna</label>
                            <input type="date" name="tarikh_guna" class="form-control" value="<?= $data['tarikh_guna'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kategori Sistem</label>
                            <select name="id_kategori" class="form-select" required>
                                <?php foreach ($kategoris as $k): ?>
                                    <option value="<?= $k['id_kategori'] ?>"
                                        <?= $data['id_kategori'] == $k['id_kategori'] ? 'selected' : '' ?>>
                                        <?= $k['kategori'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Numbers -->
                        <div class="col-md-4">
                            <label class="form-label">Bilangan Pengguna</label>
                            <input type="text" name="bil_pengguna" class="form-control"
                                value="<?= $data['bil_pengguna'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Bilangan Modul</label>
                            <input type="text" name="bil_modul" class="form-control"
                                value="<?= $data['bil_modul'] ?>">
                        </div>

                        <!-- Dev Info -->
                        <div class="col-md-4">
                            <label class="form-label">Bahasa Pengaturcaraan</label>
                            <input type="text" name="bahasa_pengaturcaraan" class="form-control"
                                value="<?= $data['bahasa_pengaturcaraan'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Pangkalan Data</label>
                            <input type="text" name="pangkalan_data" class="form-control"
                                value="<?= $data['pangkalan_data'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Rangkaian</label>
                            <input type="text" name="rangkaian" class="form-control"
                                value="<?= $data['rangkaian'] ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Integrasi</label>
                            <input type="text" name="integrasi" class="form-control"
                                value="<?= $data['integrasi'] ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kaedah Pembangunan</label>
                            <select name="id_kaedahpembangunan" id="id_kaedahpembangunan" class="form-select" required>
                                <?php foreach ($kaedahPembangunans as $kp): ?>
                                    <option value="<?= $kp['id_kaedahPembangunan'] ?>"
                                        <?= $data['id_kaedahpembangunan'] == $kp['id_kaedahPembangunan'] ? 'selected' : '' ?>>
                                        <?= $kp['kaedahPembangunan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                </div>                

                <!-- SUBSECTION (INHOUSE / PEMBEKAL) -->
                <div class="form-section">
                    <div class="section-heading">
                        <i class="bi bi-diagram-3"></i> 
                        <span>KAEDAH PEMBANGUNAN</span>
                    </div>

                    <!-- === INHOUSE / PEMBEKAL (Dynamic) === -->
                    <div id="kaedahPembangunanSpecificContainer" class="row g-3 mb-3">
                        <!-- INHOUSE -->
                        <div id="pembangunanInhouseContainer"
                            class="col-md-6" style="<?= $data['inhouse'] ? '' : 'display:none' ?>">
                            <label class="form-label">Pembangunan Inhouse</label>
                            <select name="inhouse" class="form-select">
                                <option value="">-- Pilih Bahagian --</option>
                                <?php foreach ($bahagianunits as $bu): ?>
                                    <option value="<?= $bu['id_bahagianunit'] ?>"
                                        <?= $data['inhouse'] == $bu['id_bahagianunit'] ? 'selected' : '' ?>>
                                        <?= $bu['bahagianunit'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- PEMBEKAL -->
                        <div id="pembangunanLuarContainer"
                            class="col-md-6" style="<?= $data['id_pembekal'] ? '' : 'display:none' ?>">
                            <label class="form-label">Pembekal Utama</label>
                            <select name="id_pembekal" id="id_pembekal" class="form-select">
                                <option value="">-- Pilih Pembekal --</option>
                                <?php foreach ($pembekals as $p): ?>
                                    <option value="<?= $p['id_pembekal'] ?>"
                                        <?= $data['id_pembekal'] == $p['id_pembekal'] ? 'selected' : '' ?>>
                                        <?= $p['nama_syarikat'] ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="NEW_SUPPLIER">Tambah Pembekal Baru..</option>
                            </select>
                        </div>

                    </div>

                    <div class="new-supplier-box p-3 rounded-3" id="newPembekalForm" style="display:none;">
                        <h6 class="new-supplier-title">Daftar Pembekal Baharu</h6>
                        <div class="row g-3">
                            <!-- NEW SUPPLIER FORM -->
                            <div id="newPembekalForm" class="row g-3 mb-4" style="display:none;">
                                <hr>
                                <div class="section-subtitle">Daftar Pembekal Baharu</div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama Syarikat</label>
                                    <input type="text" name="nama_syarikat_baru" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tempoh Kontrak</label>
                                    <input type="text" name="tempoh_kontrak_baru" class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Alamat Syarikat</label>
                                    <input type="text" name="alamat_syarikat_baru" class="form-control">
                                </div>

                                <div class="col-md-12"><div class="section-subtitle mt-3">Maklumat PIC</div></div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama PIC</label>
                                    <input type="text" name="nama_PIC_baru" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Jawatan PIC</label>
                                    <input type="text" name="jawatan_PIC_baru" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Emel PIC</label>
                                    <input type="email" name="emel_PIC_baru" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">No. Telefon</label>
                                    <input type="text" name="notelefon_PIC_baru" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Fax</label>
                                    <input type="text" name="fax_PIC_baru" class="form-control">
                                </div>

                                <input type="hidden" name="is_new_supplier" id="is_new_supplier" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: KOS & PENYELENGGARAAN -->
                <div class="form-section">
                    <div class="section-heading">
                        <i class="bi bi-wallet2"></i>
                        <span>MAKLUMAT PEMBANGUNAN & KOS</span>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label>Tarikh Dibeli</label>
                            <input type="date" name="tarikh_dibeli" class="form-control" value="<?= $data['tarikh_dibeli'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label>Tempoh Jaminan</label>
                            <input type="text" name="tempoh_jaminan_sistem" class="form-control"
                                value="<?= $data['tempoh_jaminan_sistem'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label>Tarikh Tamat Jaminan</label>
                            <input type="date" name="expired_jaminan_sistem" class="form-control"
                                value="<?= $data['expired_jaminan_sistem'] ?>">
                        </div>

                        <div class="col-md-6">
                            <label>Jenis Penyelenggaraan</label>
                            <select name="id_penyelenggaraan" class="form-select" required>
                                <?php foreach ($penyelenggaraans as $py): ?>
                                    <option value="<?= $py['id_penyelenggaraan'] ?>"
                                        <?= $data['id_penyelenggaraan'] == $py['id_penyelenggaraan'] ? 'selected' : '' ?>>
                                        <?= $py['penyelenggaraan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- KOS SISTEM -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label>Kos Keseluruhan (RM)</label>
                            <input type="number" step="0.01" name="kos_keseluruhan" class="form-control"
                                value="<?= $data['kos_keseluruhan'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label>Kos Perkakasan (RM)</label>
                            <input type="number" step="0.01" name="kos_perkakasan" class="form-control"
                                value="<?= $data['kos_perkakasan'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label>Kos Perisian (RM)</label>
                            <input type="number" step="0.01" name="kos_perisian" class="form-control"
                                value="<?= $data['kos_perisian'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label>Kos Lesen Perisian (RM)</label>
                            <input type="number" step="0.01" name="kos_lesen_perisian" class="form-control"
                                value="<?= $data['kos_lesen_perisian'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label>Kos Penyelenggaraan (RM)</label>
                            <input type="number" step="0.01" name="kos_penyelenggaraan" class="form-control"
                                value="<?= $data['kos_penyelenggaraan'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label>Kos Lain-Lain (RM)</label>
                            <input type="number" step="0.01" name="kos_lain" class="form-control"
                                value="<?= $data['kos_lain'] ?>">
                        </div>
                    </div>
                </div>                

                <!-- SECTION PENGURUSAN PENGGUNA -->
                <div class="form-section">
                    <div class="section-heading">
                        <i class="bi bi-people"></i>
                        <span>PENGURUSAN PENGGUNA</span>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label>Kategori Pengguna</label>
                            <select name="id_kategoriuser" class="form-select" required>
                                <?php foreach ($kategoriusers as $ku): ?>
                                    <option value="<?= $ku['id_kategoriuser'] ?>"
                                        <?= $data['id_kategoriuser'] == $ku['id_kategoriuser'] ? 'selected' : '' ?>>
                                        <?= ($ku['jenis_dalaman'] ? "Dalaman" : "") ?>
                                        <?= ($ku['jenis_dalaman'] && $ku['jenis_umum']) ? " & " : "" ?>
                                        <?= ($ku['jenis_umum'] ? "Umum" : "") ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Pengurus Akses Sistem</label>
                            <select name="pengurus_akses" class="form-select" required>
                                <?php foreach ($bahagianunits as $bu): ?>
                                    <option value="<?= $bu['id_bahagianunit'] ?>"
                                        <?= $data['pengurus_akses'] == $bu['id_bahagianunit'] ? 'selected' : '' ?>>
                                        <?= $bu['bahagianunit'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Pegawai Rujukan Sistem</label>
                            <select name="pegawai_rujukan_sistem" class="form-select" required>
                                <?php foreach ($userprofiles as $up): ?>
                                    <option value="<?= $up['id_userprofile'] ?>"
                                        <?= $data['pegawai_rujukan_sistem'] == $up['id_userprofile'] ? 'selected' : '' ?>>
                                        <?= $up['nama_user'] ?> (<?= $up['jawatan_user'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                </div>                

                <div class="form-footer mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-save">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                    <a href="profil_sistem.php" class="btn btn-outline-secondary">Kembali</a>
                </div>

            </form>

        </div>

    </div>

</body>
</html>




