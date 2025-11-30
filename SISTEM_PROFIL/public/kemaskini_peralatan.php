<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id'])) {
    header("Location: profil_peralatan.php");
    exit;
}

$id = intval($_GET['id']);

// =======================
// FETCH LOOKUPS
// =======================
$jenisperalatan = $pdo->query("SELECT * FROM lookup_jenisperalatan ORDER BY jenis_peralatan")->fetchAll(PDO::FETCH_ASSOC);
$penyelenggaraan = $pdo->query("SELECT * FROM lookup_penyelenggaraan ORDER BY penyelenggaraan")->fetchAll(PDO::FETCH_ASSOC);
$pembekal = $pdo->query("SELECT * FROM lookup_pembekal ORDER BY nama_syarikat")->fetchAll(PDO::FETCH_ASSOC);
$pegawai = $pdo->query("SELECT id_userprofile, nama_user FROM lookup_userprofile ORDER BY nama_user")->fetchAll(PDO::FETCH_ASSOC);
$status = $pdo->query("SELECT * FROM lookup_status")->fetchAll(PDO::FETCH_ASSOC);
$bahagian = $pdo->query("SELECT * FROM lookup_bahagianunit")->fetchAll(PDO::FETCH_ASSOC);
$carta = $pdo->query("SELECT * FROM lookup_carta")->fetchAll(PDO::FETCH_ASSOC);

// =======================
// FETCH PERALATAN + PROFIL
// =======================
$stmt = $pdo->prepare("
    SELECT p.*, pr.*
    FROM PROFIL p
    INNER JOIN PERALATAN pr ON p.id_profilsistem = pr.id_profilsistem
    WHERE p.id_profilsistem = :id
");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("<div class='alert alert-danger'>Peralatan tidak ditemui.</div>");
}

// =======================
// FETCH PERALATAN DATA
// =======================
$stmt = $pdo->prepare("
    SELECT P.*, E.*
    FROM PROFIL P
    INNER JOIN PERALATAN E ON P.id_profilsistem = E.id_profilsistem
    WHERE P.id_profilsistem = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Rekod tidak dijumpai!");
}

// =======================
// UPDATE PEMBEKAL + PIC
// =======================
if (!empty($_POST['id_pembekal'])) {
    $idPembekal = $_POST['id_pembekal'];

    // Update pembekal
    $sqlPemb = "UPDATE lookup_pembekal SET
        nama_syarikat = :nama,
        alamat_syarikat = :alamat,
        tempoh_kontrak = :kontrak
        WHERE id_pembekal = :id";
    
    $stmtPemb = $pdo->prepare($sqlPemb);
    $stmtPemb->execute([
        ':nama' => $_POST['edit_nama_syarikat'],
        ':alamat' => $_POST['edit_alamat_syarikat'],
        ':kontrak' => $_POST['edit_tempoh_kontrak'],
        ':id' => $idPembekal
    ]);

    // Update PIC jika ada existing_id_PIC
    if (!empty($_POST['existing_id_PIC'])) {
        $sqlPIC = "UPDATE lookup_PIC SET
            nama_PIC = :nama,
            jawatan_PIC = :jawatan,
            emel_PIC = :emel,
            notelefon_PIC = :telefon,
            fax_PIC = :fax
            WHERE id_PIC = :id";
        
        $stmtPIC = $pdo->prepare($sqlPIC);
        $stmtPIC->execute([
            ':nama' => $_POST['edit_nama_PIC'],
            ':jawatan' => $_POST['edit_jawatan_PIC'],
            ':emel' => $_POST['edit_emel_PIC'],
            ':telefon' => $_POST['edit_notelefon_PIC'],
            ':fax' => $_POST['edit_fax_PIC'],
            ':id' => $_POST['existing_id_PIC']
        ]);
    }
}

// =======================
// FETCH CURRENT PEMBEKAL + PIC
// =======================
$currentPembekalData = null;

if ($data['id_pembekal']) {
    $stmtPemb = $pdo->prepare("
        SELECT p.*, pic.nama_PIC, pic.emel_PIC, pic.notelefon_PIC, pic.fax_PIC, pic.jawatan_PIC
        FROM lookup_pembekal p
        LEFT JOIN lookup_PIC pic ON p.id_PIC = pic.id_PIC
        WHERE p.id_pembekal = ?
    ");
    $stmtPemb->execute([$data['id_pembekal']]);
    $currentPembekalData = $stmtPemb->fetch(PDO::FETCH_ASSOC);
}

// jika user pilih pembekal baru (dropdown change)
if (isset($_GET['pembekal']) && is_numeric($_GET['pembekal'])) {
    $data['id_pembekal'] = intval($_GET['pembekal']);

    $stmtPemb = $pdo->prepare("
        SELECT p.*, pic.nama_PIC, pic.emel_PIC, pic.notelefon_PIC, pic.fax_PIC, pic.jawatan_PIC
        FROM lookup_pembekal p
        LEFT JOIN lookup_PIC pic ON p.id_PIC = pic.id_PIC
        WHERE p.id_pembekal = ?
    ");
    $stmtPemb->execute([$_GET['pembekal']]);
    $currentPembekalData = $stmtPemb->fetch(PDO::FETCH_ASSOC);
}

// Lookup pembekal
$pembekals = $pdo->query("SELECT * FROM lookup_pembekal")->fetchAll();

// =======================
// UPDATE PROCESS
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        // Update PROFiL
        $sql1 = "
            UPDATE PROFIL SET
                nama_entiti = :nama_entiti,
                alamat_pejabat = :alamat_pejabat,
                id_status = :status,
                id_bahagianunit = :bahagian,
                id_carta = :carta,
                nama_ketua = :ketua,
                nama_cio = :cio,
                nama_ictso = :ictso,
                tarikh_kemaskini = NOW()
            WHERE id_profilsistem = :id
        ";

        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([
            ':nama_entiti' => $_POST['nama_entiti'],
            ':alamat_pejabat' => $_POST['alamat_pejabat'],
            ':status' => $_POST['id_status'],
            ':bahagian' => $_POST['id_bahagianunit'],
            ':carta' => $_POST['id_carta'],
            ':ketua' => $_POST['nama_ketua'],
            ':cio' => $_POST['nama_cio'],
            ':ictso' => $_POST['nama_ictso'],
            ':id' => $id
        ]);


        // Update PERALATAN
        $sql2 = "
            UPDATE PERALATAN SET
                nama_peralatan = :nama_peralatan,
                id_jenisperalatan = :jenis_peralatan,
                siri_peralatan = :siri,
                lokasi_peralatan = :lokasi,
                jenama_model = :model,
                tarikh_dibeli = :tarikh_dibeli,
                tempoh_jaminan_peralatan = :jaminan,
                expired_jaminan = :expired,
                id_penyelenggaraan = :penyelenggaraan,
                kos_penyelenggaraan_tahunan = :kos,
                tarikh_penyelenggaraan_terakhir = :tarikh_akhir,
                id_pembekal = :pembekal,
                pegawai_rujukan_peralatan = :pegawai
            WHERE id_profilsistem = :id
        ";

        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([
            ':nama_peralatan' => $_POST['nama_peralatan'],
            ':jenis_peralatan' => $_POST['id_jenisperalatan'],
            ':siri' => $_POST['siri_peralatan'],
            ':lokasi' => $_POST['lokasi_peralatan'],
            ':model' => $_POST['jenama_model'],
            ':tarikh_dibeli' => $_POST['tarikh_dibeli'],
            ':jaminan' => $_POST['tempoh_jaminan_peralatan'],
            ':expired' => $_POST['expired_jaminan'],
            ':penyelenggaraan' => $_POST['id_penyelenggaraan'],
            ':kos' => $_POST['kos_penyelenggaraan_tahunan'],
            ':tarikh_akhir' => $_POST['tarikh_penyelenggaraan_terakhir'],
            ':pembekal' => $_POST['id_pembekal'],
            ':pegawai' => $_POST['pegawai_rujukan_peralatan'],
            ':id' => $id
        ]);

        header("Location: view_peralatan.php?id=$id&update=success");
        exit;

    } catch (PDOException $e) {
        $error = "Ralat DB: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kemaskini Peralatan | Sistem Profil</title>

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
            <h4 class="page-title">Kemaskini Profil Peralatan</h4>
        </div>

        <div class="edit-sistem-card shadow-sm rounded-4 p-4">
            <form method="POST">

                <!-- ============= MAKLUMAT PERALATAN ============= -->
                <div class="section-title">MAKLUMAT PERALATAN</div>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label>Nama Peralatan</label>
                        <input type="text" name="nama_peralatan" class="form-control"
                            value="<?= $data['nama_peralatan']; ?>">
                    </div>

                    <div class="col-md-6">
                        <label>Jenis Peralatan</label>
                        <select name="id_jenisperalatan" class="form-select">
                            <?php foreach ($jenisperalatan as $row): ?>
                                <option value="<?= $row['id_jenisperalatan']; ?>"
                                    <?= $row['id_jenisperalatan'] == $data['id_jenisperalatan'] ? 'selected' : '' ?>>
                                    <?= $row['jenis_peralatan']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>No Siri</label>
                        <input type="text" name="siri_peralatan" class="form-control"
                            value="<?= $data['siri_peralatan']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label>Lokasi Peralatan</label>
                        <input type="text" name="lokasi_peralatan" class="form-control"
                            value="<?= $data['lokasi_peralatan']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label>Jenama / Model</label>
                        <input type="text" name="jenama_model" class="form-control"
                            value="<?= $data['jenama_model']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label>Tarikh Dibeli</label>
                        <input type="date" name="tarikh_dibeli" class="form-control"
                            value="<?= $data['tarikh_dibeli']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label>Tempoh Jaminan</label>
                        <input type="text" name="tempoh_jaminan_peralatan" class="form-control"
                            value="<?= $data['tempoh_jaminan_peralatan']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label>Expired Jaminan</label>
                        <input type="date" name="expired_jaminan" class="form-control"
                            value="<?= $data['expired_jaminan']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label>Penyelenggaraan</label>
                        <select name="id_penyelenggaraan" class="form-select">
                            <?php foreach ($penyelenggaraan as $row): ?>
                                <option value="<?= $row['id_penyelenggaraan']; ?>"
                                    <?= $row['id_penyelenggaraan'] == $data['id_penyelenggaraan'] ? 'selected' : '' ?>>
                                    <?= $row['penyelenggaraan']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Kos Penyelenggaraan Tahunan (RM)</label>
                        <input type="number" step="0.01" name="kos_penyelenggaraan_tahunan" class="form-control"
                            value="<?= $data['kos_penyelenggaraan_tahunan']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label>Tarikh Penyelenggaraan Terakhir</label>
                        <input type="date" name="tarikh_penyelenggaraan_terakhir" class="form-control"
                            value="<?= $data['tarikh_penyelenggaraan_terakhir']; ?>">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Pilih Pembekal</label>
                        <select name="id_pembekal" id="id_pembekal" class="form-select mb-3"
                            onchange="window.location='kemaskini_peralatan.php?id=<?= $id ?>&pembekal=' + this.value">
                            <option value="">-- Pilih Pembekal --</option>
                            <?php foreach ($pembekals as $p): ?>
                                <option value="<?= $p['id_pembekal'] ?>"
                                    <?= ($data['id_pembekal'] == $p['id_pembekal']) ? 'selected' : '' ?>>
                                    <?= $p['nama_syarikat'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- FORM EDIT PEMBEKAL -->
                        <div id="editPembekalForm" class="p-3 border rounded-3 bg-light"
                            style="display: <?= $data['id_pembekal'] ? 'block' : 'none' ?>;">

                            <h6 class="fw-bold">Maklumat Pembekal</h6>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Nama Syarikat</label>
                                    <input type="text" name="edit_nama_syarikat" class="form-control"
                                        value="<?= $currentPembekalData['nama_syarikat'] ?? '' ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tempoh Kontrak</label>
                                    <input type="text" name="edit_tempoh_kontrak" class="form-control"
                                        value="<?= $currentPembekalData['tempoh_kontrak'] ?? '' ?>">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Alamat Syarikat</label>
                                    <input type="text" name="edit_alamat_syarikat" class="form-control"
                                        value="<?= $currentPembekalData['alamat_syarikat'] ?? '' ?>">
                                </div>

                                <div class="col-md-12 mt-3">
                                    <h6 class="fw-bold">Maklumat PIC</h6>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama PIC</label>
                                    <input type="text" name="edit_nama_PIC" class="form-control"
                                        value="<?= $currentPembekalData['nama_PIC'] ?? '' ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Jawatan PIC</label>
                                    <input type="text" name="edit_jawatan_PIC" class="form-control"
                                        value="<?= $currentPembekalData['jawatan_PIC'] ?? '' ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Emel PIC</label>
                                    <input type="email" name="edit_emel_PIC" class="form-control"
                                        value="<?= $currentPembekalData['emel_PIC'] ?? '' ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">No. Telefon PIC</label>
                                    <input type="text" name="edit_notelefon_PIC" class="form-control"
                                        value="<?= $currentPembekalData['notelefon_PIC'] ?? '' ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Fax PIC</label>
                                    <input type="text" name="edit_fax_PIC" class="form-control"
                                        value="<?= $currentPembekalData['fax_PIC'] ?? '' ?>">
                                </div>

                                <input type="hidden" name="existing_id_PIC" 
                                    value="<?= $currentPembekalData['id_PIC'] ?? '' ?>">

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label>Pegawai Rujukan</label>
                        <select name="pegawai_rujukan_peralatan" class="form-select">
                            <?php foreach ($pegawai as $row): ?>
                                <option value="<?= $row['id_userprofile']; ?>"
                                    <?= $row['id_userprofile'] == $data['pegawai_rujukan_peralatan'] ? 'selected' : '' ?>>
                                    <?= $row['nama_user']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr>

                <!-- ============= MAKLUMAT ENTITI ============= -->
                <div class="section-title">MAKLUMAT ENTITI</div>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label>Nama Entiti</label>
                        <input type="text" name="nama_entiti" class="form-control"
                            value="<?= $data['nama_entiti']; ?>">
                    </div>

                    <div class="col-md-6">
                        <label>Alamat Pejabat</label>
                        <input type="text" name="alamat_pejabat" class="form-control"
                            value="<?= $data['alamat_pejabat']; ?>">
                    </div>

                    <div class="col-md-4">
                        <label>Status</label>
                        <select name="id_status" class="form-select">
                            <?php foreach ($status as $s): ?>
                                <option value="<?= $s['id_status']; ?>"
                                    <?= $s['id_status'] == $data['id_status'] ? 'selected' : '' ?>>
                                    <?= $s['status']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Bahagian / Unit</label>
                        <select name="id_bahagianunit" class="form-select">
                            <?php foreach ($bahagian as $b): ?>
                                <option value="<?= $b['id_bahagianunit']; ?>"
                                    <?= $b['id_bahagianunit'] == $data['id_bahagianunit'] ? 'selected' : '' ?>>
                                    <?= $b['bahagianunit']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Carta Organisasi</label>
                        <select name="id_carta" class="form-select">
                            <?php foreach ($carta as $c): ?>
                                <option value="<?= $c['id_carta']; ?>"
                                    <?= $c['id_carta'] == $data['id_carta'] ? 'selected' : '' ?>>
                                    <?= $c['carta']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Ketua Bahagian</label>
                        <select name="nama_ketua" class="form-select">
                            <?php foreach ($pegawai as $row): ?>
                                <option value="<?= $row['id_userprofile']; ?>"
                                    <?= $row['id_userprofile'] == $data['nama_ketua'] ? 'selected' : '' ?>>
                                    <?= $row['nama_user']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>CIO</label>
                        <select name="nama_cio" class="form-select">
                            <?php foreach ($pegawai as $row): ?>
                                <option value="<?= $row['id_userprofile']; ?>"
                                    <?= $row['id_userprofile'] == $data['nama_cio'] ? 'selected' : '' ?>>
                                    <?= $row['nama_user']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>ICTSO</label>
                        <select name="nama_ictso" class="form-select">
                            <?php foreach ($pegawai as $row): ?>
                                <option value="<?= $row['id_userprofile']; ?>"
                                    <?= $row['id_userprofile'] == $data['nama_ictso'] ? 'selected' : '' ?>>
                                    <?= $row['nama_user']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <a href="view_peralatan.php?id=<?= $id ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>

            </form>

        </div>
    </div>

</body>
</html>
