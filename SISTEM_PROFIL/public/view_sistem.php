<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// Check ID parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid ID.");
}

$id = intval($_GET['id']);

// GET PROFIL SISTEM MAIN DETAILS
$sql = "
    SELECT 
        ps.*,
        ls.status AS nama_status,
        lj.jenisprofil,
        u.nama_penuh AS nama_pendaftar
    FROM PROFIL_SISTEM ps
    LEFT JOIN LOOKUP_STATUS ls ON ls.id_status = ps.id_status
    LEFT JOIN LOOKUP_JENISPROFIL lj ON lj.id_jenisprofil = ps.id_jenisprofil
    LEFT JOIN USERLOG u ON u.id_user = ps.id_user
    WHERE ps.id_profilsistem = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profil) {
    die("Rekod tidak dijumpai.");
}

// GET SISTEM DETAILS
$sql2 = "
    SELECT s.*, bu.bahagianunit, 
           lk.kategori,
           lp.penyelenggaraan,
           lkp.kaedahPembangunan,
           lo.nama_syarikat, lo.alamat_syarikat,
           lp2.nama_PIC, lp2.emel_PIC, lp2.notelefon_PIC, lp2.fax_PIC, lp2.jawatan_PIC
    FROM SISTEM s
    LEFT JOIN LOOKUP_BAHAGIANUNIT bu ON bu.id_bahagianunit = s.pemilik_sistem
    LEFT JOIN LOOKUP_KATEGORI lk ON lk.id_kategori = s.id_kategori
    LEFT JOIN LOOKUP_PENYELENGGARAAN lp ON lp.id_penyelenggaraan = s.id_penyelenggaraan
    LEFT JOIN LOOKUP_KAEDAHPEMBANGUNAN lkp ON lkp.id_kaedahPembangunan = s.id_kaedahPembangunan
    LEFT JOIN LOOKUP_OUTSOURCE lo ON lo.id_outsource = s.id_outsource
    LEFT JOIN LOOKUP_PIC lp2 ON lp2.id_PIC = lo.id_PIC
    WHERE s.id_profilsistem = ?
";

$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([$id]);
$sistem = $stmt2->fetch(PDO::FETCH_ASSOC);

// GET KOS
$kos = $pdo->query("SELECT * FROM KOS WHERE id_profilsistem = $id")->fetch(PDO::FETCH_ASSOC);

// GET AKSES SISTEM
$sql_akses = "
    SELECT a.*, bu.bahagianunit, ku.jenis_dalaman, ku.jenis_umum
    FROM AKSES a
    LEFT JOIN LOOKUP_BAHAGIANUNIT bu ON bu.id_bahagianunit = a.id_bahagianunit
    LEFT JOIN LOOKUP_KATEGORIUSER ku ON ku.id_kategoriuser = a.id_kategoriuser
    WHERE a.id_profilsistem = ?
";
$stmt_akses = $pdo->prepare($sql_akses);
$stmt_akses->execute([$id]);
$akses_list = $stmt_akses->fetchAll(PDO::FETCH_ASSOC);

// GET ENTITI SISTEM
$sql_entiti = "
    SELECT 
        e.*,
        bu.bahagianunit,
        
        -- Ketua Bahagian
        up.nama_user AS ketua_nama,
        up.emel_user AS ketua_emel,

        -- CIO
        cio.nama_user AS cio_nama,
        cio.emel_user AS cio_emel,

        -- ICTSO
        ictso.nama_user AS ictso_nama,
        ictso.emel_user AS ictso_emel,

        c.carta

    FROM ENTITI e
    LEFT JOIN LOOKUP_BAHAGIANUNIT bu ON bu.id_bahagianunit = e.id_bahagianunit

    -- Ketua Bahagian (id_userprofile)
    LEFT JOIN LOOKUP_USERPROFILE up ON up.id_userprofile = e.id_userprofile

    -- CIO (e.cio)
    LEFT JOIN LOOKUP_USERPROFILE cio ON cio.id_userprofile = e.cio

    -- ICTSO (e.ictso)
    LEFT JOIN LOOKUP_USERPROFILE ictso ON ictso.id_userprofile = e.ictso

    LEFT JOIN LOOKUP_CARTA c ON c.id_carta = e.id_carta
    WHERE e.id_profilsistem = ?
";
$stmt_entiti = $pdo->prepare($sql_entiti);
$stmt_entiti->execute([$id]);
$entiti_list = $stmt_entiti->fetchAll(PDO::FETCH_ASSOC);

// GET PEGAWAI RUJUKAN
$sql_peg = "
    SELECT 
        p.id_rujukansistem,
        up.nama_user,
        up.jawatan_user,
        up.emel_user,
        up.notelefon_user,
        up.fax_user,
        bu.bahagianunit
    FROM PEGAWAI_RUJUKAN_SISTEM p
    LEFT JOIN LOOKUP_USERPROFILE up 
        ON up.id_userprofile = p.id_userprofile
    LEFT JOIN LOOKUP_BAHAGIANUNIT bu
        ON bu.id_bahagianunit = up.id_bahagianunit
    WHERE p.id_profilsistem = ?
";
$stmt_peg = $pdo->prepare($sql_peg);
$stmt_peg->execute([$id]);
$peg = $stmt_peg->fetch(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>View Sistem | Profil Sistem</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/profil.css">
</head>

<body>

    <div class="container-fluid py-4">
        <!-- FIXED HEADER + HOME -->
        <div class="sticky-top bg-white py-2 mb-3 d-flex align-items-center justify-content-between shadow-sm px-3" style="z-index: 1050;">
            <a href="profil_sistem.php" class="btn btn-secondary">
                <i class="bi bi-house-door"></i>
            </a>
            <div style="flex: 1;"><?php include 'header.php'; ?></div>
        </div>

        <!-- MAIN HEADER WITH SYSTEM NAME + STATUS + PROFIL BADGE -->
        <div class="view-main-header d-flex align-items-center justify-content-between flex-wrap">

            <div class="d-flex align-items-center" style="gap: 10px;">
                <i class="bi bi-pc-display"></i>
                <?= htmlspecialchars($sistem['nama_sistem'] ?? 'Maklumat Sistem') ?>
            </div>

            <div class="d-flex align-items-center" style="gap: 10px;">

                <!-- STATUS BADGE -->
                <?php
                    $statusColor = ($profil['nama_status'] == "Aktif") ? "#0096C7" : "#a9c6d8";
                ?>
                <span class="status-tag" style="background: <?= $statusColor ?>;">
                    <?= $profil['nama_status'] ?>
                </span>

                <!-- JENIS PROFIL BADGE -->
                <?php
                    $jenisColor = "#006EA0";
                    if ($profil['jenisprofil'] == "Peralatan") $jenisColor = "#0077A8";
                    if ($profil['jenisprofil'] == "Pengguna")  $jenisColor = "#004b73";
                ?>
                <span class="jenis-tag" style="background: <?= $jenisColor ?>;">
                    <?= $profil['jenisprofil'] ?>
                </span>

            </div>

        </div>


        <!-- SECTION: SISTEM -->
        <div class="view-section-box">
            <div class="view-section-title">Maklumat Sistem</div>

            <div class="info-row">
                <div class="info-label">Nama Sistem</div>
                <div class="info-value"><?= $sistem['nama_sistem'] ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Pemilik Sistem</div>
                <div class="info-value"><?= $sistem['bahagianunit'] ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Kategori Sistem</div>
                <div class="info-value"><?= $sistem['kategori'] ?></div>
            </div>

            <div class="info-row align-items-start">
                <div class="info-label">Objektif Sistem</div>
                <div class="objective-box" style="width:100%; max-width:750px;">
                    <?= nl2br($sistem['objektif']) ?>
                </div>
            </div>

            <div class="sub-label mt-3">Pembangunan Sistem:</div>

            <div class="info-row">
                <div class="info-label">Tarikh Mula</div>
                <div class="info-value"><?= $sistem['tarikh_mula'] ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Tarikh Siap</div>
                <div class="info-value"><?= $sistem['tarikh_siap'] ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Tarikh Guna</div>
                <div class="info-value"><?= $sistem['tarikh_guna'] ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Bilangan Pengguna</div>
                <div class="info-value"><?= $sistem['bil_pengguna'] ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">Bilangan Modul Sistem</div>
                <div class="info-value"><?= $sistem['bil_modul'] ?></div>
            </div>
        </div>
        <div class="view-section-box">
            <div class="view-section-title">Maklumat Pembangunan</div>

            <div class="info-row">
                <div class="info-label">Kaedah Pembangunan</div>
                <div class="info-value"><?= $sistem['kaedahPembangunan'] ?></div>
            </div>

            <?php if ($sistem['id_kaedahPembangunan'] == 2): ?>

                <div class="sub-label">Maklumat Pembekal:</div>

                <div class="info-row">
                    <div class="info-label">Nama Syarikat</div>
                    <div class="info-value"><?= $sistem['nama_syarikat'] ?></div>
                </div>

                <div class="info-row align-items-start">
                    <div class="info-label">Alamat Syarikat</div>
                    <div class="info-value"><?= nl2br($sistem['alamat_syarikat']) ?></div>
                </div>

                <!-- PIC -->
                <div class="info-row align-items-start">
                    <div class="sub-label"  style="width:220px;">Maklumat PIC:</div>
                    <div class="info-multi-box" style="width:100%; max-width:650px;">
                        <?= $sistem['nama_PIC'] ?><br>
                        <?= $sistem['jawatan_PIC'] ?><br>
                        <?= $sistem['emel_PIC'] ?><br>
                        <?= $sistem['notelefon_PIC'] ?><br>
                        <?= $sistem['fax_PIC'] ?>
                    </div>
                </div>

            <?php else: ?>

                <div class="sub-label">Maklumat Dalaman:</div>
                <div class="info-row">
                    <div class="info-label">Bahagian Bertanggungjawab</div>
                    <div class="info-value"><?= $sistem['bahagianunit'] ?></div>
                </div>

            <?php endif; ?>
        </div>


        <!-- SECTION: AKSES -->
        <div class="view-section-box">
            <div class="view-section-title">Maklumat Akses Sistem</div>

            <?php foreach ($akses_list as $akses): ?>

                <!-- PENGURUS AKSES -->
                <div class="info-row">
                    <div class="info-label">Pengurus Akses Sistem</div>
                    <div class="info-value"><?= $akses['bahagianunit'] ?></div>
                </div>

                <!-- KATEGORI PENGGUNA -->
                <div class="info-row align-items-start" style="margin-top: 15px;">
                    <div class="info-label sub-label" style="width:220px; margin:0; padding:0;">
                        Kategori Pengguna:
                    </div>

                    <div style="display:flex; flex-direction:column; gap:8px;">

                        <!-- DALAMAN -->
                        <div class="info-row" style="margin:0; padding:0;">
                            <div class="info-label" style="width:110px;">Dalaman</div>
                            <div class="info-value">
                                <?= $akses['jenis_dalaman'] == 1 ? 'Ya' : 'Tidak' ?>
                            </div>
                        </div>

                        <!-- UMUM -->
                        <div class="info-row" style="margin:0; padding:0;">
                            <div class="info-label" style="width:110px;">Umum</div>
                            <div class="info-value">
                                <?= $akses['jenis_umum'] == 1 ? 'Ya' : 'Tidak' ?>
                            </div>
                        </div>

                    </div>
                </div>

            <?php endforeach; ?>
        </div>


        <!-- SECTION: ENTITI -->
        <div class="view-section-box">
            <div class="view-section-title">Maklumat Entiti</div>

            <?php foreach ($entiti_list as $ent): ?>

                <div class="info-row">
                    <div class="info-label">Nama Entiti</div>
                    <div class="info-value"><?= $ent['nama_entiti'] ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tarikh Kemaskini</div>
                    <div class="info-value"><?= $ent['tarikh_kemaskini'] ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Bahagian Entiti</div>
                    <div class="info-value"><?= $ent['bahagianunit'] ?></div>
                </div>


                <!-- KETUA BAHAGIAN -->
                <div class="info-row align-items-start">
                    <div class="sub-label"  style="width:220px;">Ketua Bahagian</div>
                    <div class="info-multi-box" style="width:100%; max-width:550px;">
                        <?= $ent['ketua_nama'] ?><br>
                        <?= $ent['ketua_emel'] ?>
                    </div>
                </div>

                <!-- CIO -->
                <div class="info-row align-items-start">
                    <div class="sub-label"  style="width:220px;">Chief Information Officer (CIO)</div>
                    <div class="info-multi-box" style="width:100%; max-width:550px;">
                        <?= $ent['cio_nama'] ?><br>
                        <?= $ent['cio_emel'] ?>
                    </div>
                </div>

                <!-- ICTSO -->
                <div class="info-row align-items-start">
                    <div class="sub-label"  style="width:220px;">Chief Security Officer (ICTSO)</div>
                    <div class="info-multi-box" style="width:100%; max-width:550px;">
                        <?= $ent['ictso_nama'] ?><br>
                        <?= $ent['ictso_emel'] ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Carta</div>
                    <div class="info-value"><?= $ent['carta'] ?></div>
                </div>

            <?php endforeach; ?>
        </div>


        <!-- SECTION: RUJUKAN SISTEM -->
        <div class="view-section-box">
            <div class="view-section-title">Rujukan Sistem</div>

            <?php if ($peg): ?>

                <div class="info-row">
                    <div class="info-label">Nama Pegawai</div>
                    <div class="info-value"><?= $peg['nama_user'] ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Jawatan</div>
                    <div class="info-value"><?= $peg['jawatan_user'] ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Bahagian</div>
                    <div class="info-value"><?= $peg['bahagianunit'] ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Emel</div>
                    <div class="info-value"><?= $peg['emel_user'] ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">No Telefon</div>
                    <div class="info-value"><?= $peg['notelefon_user'] ?></div>
                </div>

            <?php else: ?>
                <p class="text-muted">Tiada maklumat pegawai rujukan.</p>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>
