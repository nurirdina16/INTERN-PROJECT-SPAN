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
           lp2.nama_PIC, lp2.emel_PIC, lp2.notelefon_PIC
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

// GET PEGAWAI RUJUKAN
$peg = $pdo->query("
    SELECT p.*, up.nama_user, up.jawatan_user
    FROM PEGAWAI_RUJUKAN_SISTEM p
    LEFT JOIN LOOKUP_USERPROFILE up ON up.id_userprofile = p.id_userprofile
    WHERE p.id_profilsistem = $id
")->fetch(PDO::FETCH_ASSOC);

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

<div class="container py-4">

    <!-- HEADER -->
    <?php include 'header.php'; ?>

    <!-- BUTTON HOME -->
    <a href="profil_sistem.php" class="btn btn-secondary mb-3">
        <i class="bi bi-house-door"></i> Kembali ke Senarai Sistem
    </a>

    <!-- MAIN HEADER WITH SYSTEM NAME -->
    <div class="view-main-header d-flex align-items-center justify-content-between">
        <div>
            <i class="bi bi-pc-display"></i>
            <?= htmlspecialchars($sistem['nama_sistem'] ?? 'Maklumat Sistem') ?>
        </div>
    </div>

    <!-- SECTION: PROFIL SISTEM -->
    <div class="view-section-box">
      <div class="view-section-title">A. Maklumat Profil Sistem</div>

      <div class="row">
        <div class="col-md-4">
          <p><span class="view-label-title">Status:</span><br><?= $profil['nama_status'] ?></p>
        </div>
        <div class="col-md-4">
          <p><span class="view-label-title">Jenis Profil:</span><br><?= $profil['jenisprofil'] ?></p>
        </div>
        <div class="col-md-4">
          <p><span class="view-label-title">Didaftar Oleh:</span><br><?= $profil['nama_pendaftar'] ?></p>
        </div>
      </div>
    </div>

    <!-- SECTION: SISTEM -->
    <div class="view-section-box">
      <div class="view-section-title">B. Maklumat Sistem</div>

      <div class="row">
        <div class="col-md-4">
          <p><span class="view-label-title">Nama Sistem:</span><br><?= $sistem['nama_sistem'] ?></p>
        </div>

        <div class="col-md-4">
          <p><span class="view-label-title">Pemilik Sistem:</span><br><?= $sistem['bahagianunit'] ?></p>
        </div>

        <div class="col-md-4">
          <p><span class="view-label-title">Kategori:</span><br><?= $sistem['kategori'] ?></p>
        </div>
      </div>

      <p><span class="view-label-title">Objektif Sistem:</span><br><?= nl2br($sistem['objektif']) ?></p>

      <div class="row mt-3">
        <div class="col-md-4">
          <p><span class="view-label-title">Tarikh Mula:</span><br><?= $sistem['tarikh_mula'] ?></p>
        </div>
        <div class="col-md-4">
          <p><span class="view-label-title">Tarikh Siap:</span><br><?= $sistem['tarikh_siap'] ?></p>
        </div>
        <div class="col-md-4">
          <p><span class="view-label-title">Tarikh Guna:</span><br><?= $sistem['tarikh_guna'] ?></p>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-4"><p><span class="view-label-title">Bilangan Pengguna:</span><br><?= $sistem['bil_pengguna'] ?></p></div>
        <div class="col-md-4"><p><span class="view-label-title">Bilangan Modul:</span><br><?= $sistem['bil_modul'] ?></p></div>
        <div class="col-md-4"><p><span class="view-label-title">Kaedah Pembangunan:</span><br><?= $sistem['kaedahPembangunan'] ?></p></div>
      </div>

      <?php if ($sistem['id_outsource']): ?>
        <hr>
        <div class="view-section-title mt-3">Maklumat Outsource</div>

        <p><span class="view-label-title">Nama Syarikat:</span><br><?= $sistem['nama_syarikat'] ?></p>
        <p><span class="view-label-title">Alamat Syarikat:</span><br><?= $sistem['alamat_syarikat'] ?></p>

        <div class="mt-3">
          <span class="view-label-title">Person-In-Charge (PIC):</span>
          <p><?= $sistem['nama_PIC'] ?><br><?= $sistem['emel_PIC'] ?><br><?= $sistem['notelefon_PIC'] ?></p>
        </div>
      <?php endif; ?>
    </div>

    <!-- SECTION: PEGAWAI RUJUKAN -->
    <div class="view-section-box">
      <div class="view-section-title">C. Pegawai Rujukan Sistem</div>

      <?php if ($peg): ?>
        <p><span class="view-label-title">Nama:</span><br><?= $peg['nama_user'] ?></p>
        <p><span class="view-label-title">Jawatan:</span><br><?= $peg['jawatan_user'] ?></p>
      <?php else: ?>
        <p class="text-muted">Tiada maklumat pegawai rujukan.</p>
      <?php endif; ?>
    </div>

    <!-- SECTION: KOS -->
    <div class="view-section-box">
      <div class="view-section-title">D. Maklumat Kos</div>

      <?php if ($kos): ?>
        <div class="row">
          <div class="col-md-4"><p><span class="view-label-title">Kos Keseluruhan:</span><br>RM <?= $kos['kos_keseluruhan'] ?></p></div>
          <div class="col-md-4"><p><span class="view-label-title">Kos Perkakasan:</span><br>RM <?= $kos['kos_perkakasan'] ?></p></div>
          <div class="col-md-4"><p><span class="view-label-title">Kos Perisian:</span><br>RM <?= $kos['kos_perisian'] ?></p></div>
        </div>

        <div class="row">
          <div class="col-md-4"><p><span class="view-label-title">Kos Lesen:</span><br>RM <?= $kos['kos_lesen_perisian'] ?></p></div>
          <div class="col-md-4"><p><span class="view-label-title">Kos Penyelenggaraan:</span><br>RM <?= $kos['kos_penyelenggaraan'] ?></p></div>
          <div class="col-md-4"><p><span class="view-label-title">Kos Lain:</span><br>RM <?= $kos['kos_lain'] ?></p></div>
        </div>
      <?php else: ?>
        <p class="text-muted">Tiada maklumat kos.</p>
      <?php endif; ?>
    </div>

</div>

</body>
</html>
