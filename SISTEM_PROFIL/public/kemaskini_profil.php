<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// =============================
// VALIDATE ID PROFIL
// =============================
if (!isset($_GET['id'])) {
    die("Ralat: ID Profil tidak diberikan.");
}
$id = intval($_GET['id']);

$alert_type = '';
$alert_message = '';

// =============================
// FETCH EXISTING PROFIL RECORD
// =============================
$stmt = $pdo->prepare("SELECT * FROM profil WHERE id_profil = :id");
$stmt->execute(['id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Ralat: Rekod profil tidak dijumpai!");
}

// =============================
// FETCH ALL LOOKUP TABLES
// =============================
function fetchLookup($pdo, $table, $order = "ASC") {
    return $pdo->query("SELECT * FROM $table ORDER BY 1 $order")->fetchAll(PDO::FETCH_ASSOC);
}

$lookup_status          = fetchLookup($pdo, "lookup_status");
$lookup_jenisprofil     = fetchLookup($pdo, "lookup_jenisprofil");
$lookup_bahagianunit    = fetchLookup($pdo, "lookup_bahagianunit");
$lookup_kategori        = fetchLookup($pdo, "lookup_kategori");
$lookup_jenisperalatan  = fetchLookup($pdo, "lookup_jenisperalatan");
$lookup_kategoriuser    = fetchLookup($pdo, "lookup_kategoriuser");
$lookup_kaedah          = fetchLookup($pdo, "lookup_kaedahpembangunan");
$lookup_pembekal        = fetchLookup($pdo, "lookup_pembekal");
$lookup_penyelenggaraan = fetchLookup($pdo, "lookup_penyelenggaraan");
$lookup_userprofile     = fetchLookup($pdo, "lookup_userprofile");
$lookup_carta           = fetchLookup($pdo, "lookup_carta");

// =============================
// PROCESS UPDATE FORM SUBMISSION
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // AUTO CALCULATE TOTAL COST
    $kos_perkakasan       = floatval($_POST['kos_perkakasan'] ?? 0);
    $kos_perisian         = floatval($_POST['kos_perisian'] ?? 0);
    $kos_lesen_perisian   = floatval($_POST['kos_lesen_perisian'] ?? 0);
    $kos_penyelenggaraan  = floatval($_POST['kos_penyelenggaraan'] ?? 0);
    $kos_lain             = floatval($_POST['kos_lain'] ?? 0);

    $kos_keseluruhan = 
        $kos_perkakasan 
        + $kos_perisian 
        + $kos_lesen_perisian 
        + $kos_penyelenggaraan 
        + $kos_lain;

    $sql = "
        UPDATE profil SET
            id_status = :id_status,
            id_jenisprofil = :id_jenisprofil,
            nama_profil = :nama_profil,
            objektif_profil = :objektif_profil,
            id_pemilik_profil = :id_pemilik_profil,
            tarikh_mula = :tarikh_mula,
            tarikh_siap = :tarikh_siap,
            tarikh_guna = :tarikh_guna,
            id_kategori = :id_kategori,
            bil_pengguna = :bil_pengguna,
            bil_modul = :bil_modul,
            bahasa_pengaturcaraan = :bahasa_pengaturcaraan,
            pangkalan_data = :pangkalan_data,
            rangkaian = :rangkaian,
            integrasi = :integrasi,
            id_jenisperalatan = :id_jenisperalatan,
            lokasi = :lokasi,
            no_siri = :no_siri,
            jenama_model = :jenama_model,
            tarikh_dibeli = :tarikh_dibeli,
            tempoh_warranty = :tempoh_warranty,
            expired_warranty = :expired_warranty,
            kos_perkakasan = :kos_perkakasan,
            kos_perisian = :kos_perisian,
            kos_lesen_perisian = :kos_lesen_perisian,
            kos_penyelenggaraan = :kos_penyelenggaraan,
            kos_lain = :kos_lain,
            description_kos = :description_kos,
            kos_keseluruhan = :kos_keseluruhan,
            id_penyelenggaraan = :id_penyelenggaraan,
            tarikh_akhir_penyelenggaraan = :tarikh_akhir_penyelenggaraan,
            id_kaedahpembangunan = :id_kaedahpembangunan,
            inhouse = :inhouse,
            id_pembekal = :id_pembekal,
            pengurus_akses = :pengurus_akses,
            pegawai_rujukan = :pegawai_rujukan,
            nama_entiti = :nama_entiti,
            alamat_pejabat = :alamat_pejabat,
            id_bahagianunit = :id_bahagianunit,
            nama_ketua = :nama_ketua,
            nama_cio = :nama_cio,
            nama_ictso = :nama_ictso,
            id_carta = :id_carta,
            id_kategoriuser = :id_kategoriuser,
            tarikh_kemaskini = CURRENT_DATE

        WHERE id_profil = :id";

    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([
        'id_status' => $_POST['id_status'],
        'id_jenisprofil' => $_POST['id_jenisprofil'],
        'nama_profil' => $_POST['nama_profil'],
        'objektif_profil' => $_POST['objektif_profil'],
        'id_pemilik_profil' => $_POST['id_pemilik_profil'],
        'tarikh_mula' => $_POST['tarikh_mula'],
        'tarikh_siap' => $_POST['tarikh_siap'],
        'tarikh_guna' => $_POST['tarikh_guna'],
        'id_kategori' => $_POST['id_kategori'],
        'bil_pengguna' => $_POST['bil_pengguna'],
        'bil_modul' => $_POST['bil_modul'],
        'bahasa_pengaturcaraan' => $_POST['bahasa_pengaturcaraan'],
        'pangkalan_data' => $_POST['pangkalan_data'],
        'rangkaian' => $_POST['rangkaian'],
        'integrasi' => $_POST['integrasi'],
        'id_jenisperalatan' => $_POST['id_jenisperalatan'],
        'lokasi' => $_POST['lokasi'],
        'no_siri' => $_POST['no_siri'],
        'jenama_model' => $_POST['jenama_model'],
        'tarikh_dibeli' => $_POST['tarikh_dibeli'],
        'tempoh_warranty' => $_POST['tempoh_warranty'],
        'expired_warranty' => $_POST['expired_warranty'],
        
        'kos_perkakasan' => $kos_perkakasan,
        'kos_perisian' => $kos_perisian,
        'kos_lesen_perisian' => $kos_lesen_perisian,
        'kos_penyelenggaraan' => $kos_penyelenggaraan,
        'kos_lain' => $kos_lain,
        'description_kos' => $_POST['description_kos'],
        // *** IMPORTANT: AUTO TOTAL ***
        'kos_keseluruhan' => $kos_keseluruhan,

        'id_penyelenggaraan' => $_POST['id_penyelenggaraan'],
        'tarikh_akhir_penyelenggaraan' => $_POST['tarikh_akhir_penyelenggaraan'],
        'id_kaedahpembangunan' => $_POST['id_kaedahpembangunan'],
        'inhouse' => $_POST['inhouse'],
        'id_pembekal' => $_POST['id_pembekal'],
        'pengurus_akses' => $_POST['pengurus_akses'],
        'pegawai_rujukan' => $_POST['pegawai_rujukan'],
        'nama_entiti' => $_POST['nama_entiti'],
        'alamat_pejabat' => $_POST['alamat_pejabat'],
        'id_bahagianunit' => $_POST['id_bahagianunit'],
        'nama_ketua' => $_POST['nama_ketua'],
        'nama_cio' => $_POST['nama_cio'],
        'nama_ictso' => $_POST['nama_ictso'],
        'id_carta' => $_POST['id_carta'],
        'id_kategoriuser' => $_POST['id_kategoriuser'],
        'id' => $id
    ])) {

        header("Location: view_profil.php?id=$id&success=1");
        exit;

    } else {
        echo "<div class='alert alert-danger'>Ralat: Gagal mengemaskini profil.</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kemaskini Profil | Sistem Profil</title>
    
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

    <div class="content">
        <!-- FIXED HEADER -->
        <div class="sticky-top bg-white py-2 mb-3 d-flex align-items-center justify-content-between shadow-sm px-3" style="z-index: 1050;">
            <div style="flex: 1;"><?php include 'header.php'; ?></div>
        </div>

        <div class="profil-card shadow-sm p-4">

            <div class="view-main-header">
                <div class="header-wrapper">
                    <i class="bi bi-pencil-square"></i>
                    <span>Kemaskini Profil</span>
                </div>
            </div>

            <?php if ($alert_message): ?>
                <div class="alert alert-<?= $alert_type; ?>"><?= $alert_message; ?></div>
            <?php endif; ?>


            <form method="POST">

                <!-- SECTION 1 — MAKLUMAT PROFIL -->
                <div class="card p-3 mt-3">
                    <h5>Maklumat Profil</h5>

                    <label class="form-label">Nama Profil</label>
                    <input type="text" name="nama_profil" class="form-control" value="<?= $data['nama_profil']; ?>">

                    <label class="form-label mt-2">Status</label>
                    <select name="id_status" class="form-select">
                        <?php foreach ($lookup_status as $s): ?>
                        <option value="<?= $s['id_status']; ?>" <?= $data['id_status']==$s['id_status']?'selected':''; ?>>
                            <?= $s['status']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Jenis Profil</label>
                    <select name="id_jenisprofil" class="form-select">
                        <?php foreach ($lookup_jenisprofil as $jp): ?>
                        <option value="<?= $jp['id_jenisprofil']; ?>" <?= $data['id_jenisprofil']==$jp['id_jenisprofil']?'selected':''; ?>>
                            <?= $jp['jenisprofil']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Objektif Profil</label>
                    <textarea name="objektif_profil" class="form-control" rows="3"><?= $data['objektif_profil']; ?></textarea>

                    <label class="form-label mt-2">Pemilik Profil</label>
                    <select name="id_pemilik_profil" class="form-select">
                        <?php foreach ($lookup_bahagianunit as $bu): ?>
                        <option value="<?= $bu['id_bahagianunit']; ?>" <?= $data['id_pemilik_profil']==$bu['id_bahagianunit']?'selected':''; ?>>
                            <?= $bu['bahagianunit']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SECTION 1 — MAKLUMAT PROFIL -->
                <div class="card p-3 mt-3">
                    <h5>Maklumat Profil</h5>

                    <label class="form-label">Nama Profil</label>
                    <input type="text" name="nama_profil" class="form-control" value="<?= $data['nama_profil']; ?>">

                    <label class="form-label mt-2">Status</label>
                    <select name="id_status" class="form-select">
                        <?php foreach ($lookup_status as $s): ?>
                        <option value="<?= $s['id_status']; ?>" <?= $data['id_status']==$s['id_status']?'selected':''; ?>>
                            <?= $s['status']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Jenis Profil</label>
                    <select name="id_jenisprofil" class="form-select">
                        <?php foreach ($lookup_jenisprofil as $jp): ?>
                        <option value="<?= $jp['id_jenisprofil']; ?>" <?= $data['id_jenisprofil']==$jp['id_jenisprofil']?'selected':''; ?>>
                            <?= $jp['jenisprofil']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Pemilik Profil</label>
                    <select name="id_pemilik_profil" class="form-select">
                        <?php foreach ($lookup_bahagianunit as $bu): ?>
                        <option value="<?= $bu['id_bahagianunit']; ?>" <?= $data['id_pemilik_profil']==$bu['id_bahagianunit']?'selected':''; ?>>
                            <?= $bu['bahagianunit']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Objektif Profil</label>
                    <textarea name="objektif_profil" class="form-control" rows="3"><?= $data['objektif_profil']; ?></textarea>

                    
                </div>

                <!-- SECTION 2 — Tarikh -->
                <div class="card p-3 mt-3">
                    <h5>Maklumat Tarikh</h5>

                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Tarikh Mula</label>
                            <input type="date" name="tarikh_mula" class="form-control" value="<?= $data['tarikh_mula']; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tarikh Siap</label>
                            <input type="date" name="tarikh_siap" class="form-control" value="<?= $data['tarikh_siap']; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tarikh Guna</label>
                            <input type="date" name="tarikh_guna" class="form-control" value="<?= $data['tarikh_guna']; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tarikh Dibeli / Diterima</label>
                            <input type="date" name="tarikh_dibeli" class="form-control" value="<?= $data['tarikh_dibeli']; ?>">
                        </div>
                    </div>
                </div>

                <!-- SECTION 3 — TEKNIKAL -->
                <div class="card p-3 mt-3">
                    <h5>Maklumat Teknikal</h5>

                    <label class="form-label">Kategori</label>
                    <select name="id_kategori" class="form-select">
                        <?php foreach ($lookup_kategori as $k): ?>
                        <option value="<?= $k['id_kategori']; ?>" <?= $data['id_kategori']==$k['id_kategori']?'selected':''; ?>>
                            <?= $k['kategori']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Bilangan Pengguna</label>
                    <input type="text" name="bil_pengguna" class="form-control" value="<?= $data['bil_pengguna']; ?>">

                    <label class="form-label mt-2">Bilangan Modul</label>
                    <input type="text" name="bil_modul" class="form-control" value="<?= $data['bil_modul']; ?>">

                    <label class="form-label mt-2">Bahasa Pengaturcaraan</label>
                    <input type="text" name="bahasa_pengaturcaraan" class="form-control" value="<?= $data['bahasa_pengaturcaraan']; ?>">

                    <label class="form-label mt-2">Pangkalan Data</label>
                    <input type="text" name="pangkalan_data" class="form-control" value="<?= $data['pangkalan_data']; ?>">

                    <label class="form-label mt-2">Rangkaian</label>
                    <input type="text" name="rangkaian" class="form-control" value="<?= $data['rangkaian']; ?>">

                    <label class="form-label mt-2">Integrasi</label>
                    <textarea name="integrasi" class="form-control" rows="2"><?= $data['integrasi']; ?></textarea>

                    <label class="form-label mt-2">Kategori Pengguna</label>
                    <select name="id_kategoriuser" class="form-select">
                        <?php foreach ($lookup_kategoriuser as $ku): ?>
                            <option value="<?= $ku['id_kategoriuser'] ?>"
                                <?= $data['id_kategoriuser'] == $ku['id_kategoriuser'] ? 'selected' : '' ?>>
                                <?= ($ku['jenis_dalaman'] ? "Dalaman" : "") ?>
                                <?= ($ku['jenis_dalaman'] && $ku['jenis_umum']) ? " & " : "" ?>
                                <?= ($ku['jenis_umum'] ? "Umum" : "") ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SECTION 4 — Peralatan -->
                <div class="card p-3 mt-3">
                    <h5>Maklumat Peralatan</h5>

                    <label class="form-label">Jenis Peralatan</label>
                    <select name="id_jenisperalatan" class="form-select">
                        <?php foreach ($lookup_jenisperalatan as $jp): ?>
                        <option value="<?= $jp['id_jenisperalatan']; ?>" <?= $data['id_jenisperalatan']==$jp['id_jenisperalatan']?'selected':''; ?>>
                            <?= $jp['jenis_peralatan']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" value="<?= $data['lokasi']; ?>">

                    <label class="form-label mt-2">Nombor Siri</label>
                    <input type="text" name="no_siri" class="form-control" value="<?= $data['no_siri']; ?>">

                    <label class="form-label mt-2">Jenama Model</label>
                    <input type="text" name="jenama_model" class="form-control" value="<?= $data['jenama_model']; ?>">

                    <label class="form-label mt-2">Tempoh Jaminan (Tahun)</label>
                    <input type="text" name="tempoh_warranty" class="form-control" value="<?= $data['tempoh_warranty']; ?>">

                    <label class="form-label mt-2">Tarikh Luput Jaminan</label>
                    <input type="text" name="expired_warranty" class="form-control" value="<?= $data['expired_warranty']; ?>">
                </div>

                <!-- SECTION 5 — Kos -->
                <div class="card p-3 mt-3">
                    <h5>Kos & Penyelenggaraan</h5>

                    <label class="form-label">Kos Perkakasan</label>
                    <input type="number" step="0.01" name="kos_perkakasan" class="form-control" value="<?= $data['kos_perkakasan']; ?>">

                    <label class="form-label mt-2">Kos Perisian</label>
                    <input type="number" step="0.01" name="kos_perisian" class="form-control" value="<?= $data['kos_perisian']; ?>">

                    <label class="form-label mt-2">Kos Lesen Perisian</label>
                    <input type="number" step="0.01" name="kos_lesen_perisian" class="form-control" value="<?= $data['kos_lesen_perisian']; ?>">

                    <label class="form-label mt-2">Kos Penyelenggaraan</label>
                    <input type="number" step="0.01" name="kos_penyelenggaraan" class="form-control" value="<?= $data['kos_penyelenggaraan']; ?>">

                    <label class="form-label mt-2">Lain-lain Kos</label>
                    <input type="number" step="0.01" name="kos_lain" class="form-control" value="<?= $data['kos_lain']; ?>">

                    <label class="form-label mt-2">Keterangan Kos</label>
                    <textarea name="description_kos" class="form-control"><?= $data['description_kos']; ?></textarea>

                    <hr>

                    <label class="form-label">Kaedah Penyelenggaraan</label>
                    <select name="id_penyelenggaraan" class="form-select">
                        <?php foreach ($lookup_penyelenggaraan as $pen): ?>
                        <option value="<?= $pen['id_penyelenggaraan']; ?>" <?= $data['id_penyelenggaraan']==$pen['id_penyelenggaraan']?'selected':''; ?>>
                            <?= $pen['penyelenggaraan']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Tarikh Akhir Penyelenggaraan</label>
                    <input type="date" name="tarikh_akhir_penyelenggaraan" class="form-control" value="<?= $data['tarikh_akhir_penyelenggaraan']; ?>">
                </div>

                <!-- SECTION 6 — Pembangunan & Pembekal -->
                <div class="card p-3 mt-3">
                    <h5>Maklumat Pembangunan</h5>

                    <label class="form-label">Kaedah Pembangunan</label>
                    <select name="id_kaedahpembangunan" class="form-select">
                        <?php foreach ($lookup_kaedah as $kp): ?>
                        <option value="<?= $kp['id_kaedahPembangunan']; ?>" <?= $data['id_kaedahpembangunan']==$kp['id_kaedahPembangunan']?'selected':''; ?>>
                            <?= $kp['kaedahPembangunan']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <hr>

                    <h6>Maklumat Dalaman</h6>
                    <label class="form-label mt-2">Bahagian Bertanggungjawab</label>
                    <select name="inhouse" class="form-select">
                        <?php foreach ($lookup_bahagianunit as $bu): ?>
                        <option value="<?= $bu['id_bahagianunit']; ?>" <?= $data['inhouse']==$bu['id_bahagianunit']?'selected':''; ?>>
                            <?= $bu['bahagianunit']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <hr>

                    <h6>Maklumat Pembekal</h6>
                    <label class="form-label">Nama Syarikat</label>
                    <select name="id_pembekal" class="form-select">
                        <?php foreach ($lookup_pembekal as $pb): ?>
                        <option value="<?= $pb['id_pembekal']; ?>" <?= $data['id_pembekal']==$pb['id_pembekal']?'selected':''; ?>>
                            <?= $pb['nama_syarikat']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SECTION 7 — RUJUKAN PENGGUNA -->
                <div class="card p-3 mt-3">
                    <h5>Maklumat Akses & Rujukan</h5>

                    <label class="form-label">Pengurus Akses</label>
                    <select name="pengurus_akses" class="form-select">
                        <?php foreach ($lookup_bahagianunit as $bu): ?>
                        <option value="<?= $bu['id_bahagianunit']; ?>" <?= $data['pengurus_akses']==$bu['id_bahagianunit']?'selected':''; ?>>
                            <?= $bu['bahagianunit']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Pegawai Rujukan</label>
                    <select name="pegawai_rujukan" class="form-select">
                        <?php foreach ($lookup_userprofile as $u): ?>
                        <option value="<?= $u['id_userprofile']; ?>" <?= $data['pegawai_rujukan']==$u['id_userprofile']?'selected':''; ?>>
                            <?= $u['nama_user']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SECTION 8 — ENTITI -->
                <div class="card p-3 mt-3">
                    <h5>Maklumat Entiti</h5>

                    <label class="form-label">Nama Entiti</label>
                    <input type="text" name="nama_entiti" class="form-control" value="<?= $data['nama_entiti']; ?>">

                    <label class="form-label mt-2">Alamat Pejabat</label>
                    <textarea name="alamat_pejabat" class="form-control"><?= $data['alamat_pejabat']; ?></textarea>

                    <label class="form-label mt-2">Bahagian / Unit</label>
                    <select name="id_bahagianunit" class="form-select">
                        <?php foreach ($lookup_bahagianunit as $bu): ?>
                        <option value="<?= $bu['id_bahagianunit']; ?>" <?= $data['id_bahagianunit']==$bu['id_bahagianunit']?'selected':''; ?>>
                            <?= $bu['bahagianunit']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Ketua Bahagian</label>
                    <select name="nama_ketua" class="form-select">
                        <?php foreach ($lookup_userprofile as $u): ?>
                        <option value="<?= $u['id_userprofile']; ?>" <?= $data['nama_ketua']==$u['id_userprofile']?'selected':''; ?>>
                            <?= $u['nama_user']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">CIO</label>
                    <select name="nama_cio" class="form-select">
                        <?php foreach ($lookup_userprofile as $u): ?>
                        <option value="<?= $u['id_userprofile']; ?>" <?= $data['nama_cio']==$u['id_userprofile']?'selected':''; ?>>
                            <?= $u['nama_user']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">ICTSO</label>
                    <select name="nama_ictso" class="form-select">
                        <?php foreach ($lookup_userprofile as $u): ?>
                        <option value="<?= $u['id_userprofile']; ?>" <?= $data['nama_ictso']==$u['id_userprofile']?'selected':''; ?>>
                            <?= $u['nama_user']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label mt-2">Carta</label>
                    <select name="id_carta" class="form-select">
                        <?php foreach ($lookup_carta as $c): ?>
                        <option value="<?= $c['id_carta']; ?>" <?= $data['id_carta']==$c['id_carta']?'selected':''; ?>>
                            <?= $c['carta']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="mt-4 text-end">
                    <a href="view_profil.php?id=<?= $id ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>

            </form>

        </div>

    </div>

</div>
</body>
</html>
