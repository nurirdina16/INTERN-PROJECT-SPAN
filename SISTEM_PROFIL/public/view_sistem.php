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
    SELECT e.*, bu.bahagianunit, up.nama_user, up.jawatan_user, 
           up.emel_user, up.notelefon_user,
           c.carta
    FROM ENTITI e
    LEFT JOIN LOOKUP_BAHAGIANUNIT bu ON bu.id_bahagianunit = e.id_bahagianunit
    LEFT JOIN LOOKUP_USERPROFILE up ON up.id_userprofile = e.id_userprofile
    LEFT JOIN LOOKUP_CARTA c ON c.id_carta = e.id_carta
    WHERE e.id_profilsistem = ?
";
$stmt_entiti = $pdo->prepare($sql_entiti);
$stmt_entiti->execute([$id]);
$entiti_list = $stmt_entiti->fetchAll(PDO::FETCH_ASSOC);

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

  <div class="container-fluid py-4">

      <div class="d-flex align-items-center justify-content-start mb-3" style="gap: 10px;">
          <!-- BUTTON HOME -->
          <a href="profil_sistem.php" class="btn btn-secondary">
              <i class="bi bi-house-door"></i>
          </a>

          <!-- HEADER.PHP -->
          <div style="flex: 1;">
              <?php include 'header.php'; ?>
          </div>
      </div>


      <!-- MAIN HEADER WITH SYSTEM NAME -->
      <div class="view-main-header d-flex align-items-center justify-content-between">
          <div>
              <i class="bi bi-pc-display"></i>
              <?= htmlspecialchars($sistem['nama_sistem'] ?? 'Maklumat Sistem') ?>
          </div>
      </div>


      <!-- SECTION: PROFIL SISTEM -->
      <div class="view-section-box">
          <div class="view-section-title">Maklumat Profil Sistem</div>

          <div class="info-row">
              <div class="info-label">Status</div>
              <div class="info-value"><?= $profil['nama_status'] ?></div>
          </div>

          <div class="info-row">
              <div class="info-label">Jenis Profil</div>
              <div class="info-value"><?= $profil['jenisprofil'] ?></div>
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
              <div class="info-label">Kategori</div>
              <div class="info-value"><?= $sistem['kategori'] ?></div>
          </div>

          <div class="info-label mt-3">Objektif Sistem</div>
          <div class="objective-box"><?= nl2br($sistem['objektif']) ?></div>

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
              <div class="info-label">Bil. Pengguna</div>
              <div class="info-value"><?= $sistem['bil_pengguna'] ?></div>
          </div>

          <div class="info-row">
              <div class="info-label">Bil. Modul</div>
              <div class="info-value"><?= $sistem['bil_modul'] ?></div>
          </div>

          <div class="info-row">
              <div class="info-label">Kaedah Pembangunan</div>
              <div class="info-value"><?= $sistem['kaedahPembangunan'] ?></div>
          </div>

          <hr>

          <div class="view-section-title mt-3">Maklumat Pembangunan</div>

          <?php if ($sistem['id_kaedahPembangunan'] == 2): ?>

              <div class="info-row">
                  <div class="info-label">Kaedah</div>
                  <div class="info-value">Outsource</div>
              </div>

              <div class="info-row">
                  <div class="info-label">Nama Syarikat</div>
                  <div class="info-value"><?= $sistem['nama_syarikat'] ?></div>
              </div>

              <div class="info-row">
                  <div class="info-label">Alamat Syarikat</div>
                  <div class="info-value"><?= $sistem['alamat_syarikat'] ?></div>
              </div>

              <div class="info-row">
                  <div class="info-label">PIC</div>
                  <div class="info-value">
                      <?= $sistem['nama_PIC'] ?><br>
                      <?= $sistem['emel_PIC'] ?><br>
                      <?= $sistem['notelefon_PIC'] ?>
                  </div>
              </div>

          <?php else: ?>

              <div class="info-row">
                  <div class="info-label">Kaedah</div>
                  <div class="info-value">Inhouse</div>
              </div>

              <div class="info-row">
                  <div class="info-label">Bahagian Bertanggungjawab</div>
                  <div class="info-value"><?= $sistem['bahagianunit'] ?></div>
              </div>

          <?php endif; ?>
      </div>


      <!-- SECTION: ENTITI -->
      <div class="view-section-box">
          <div class="view-section-title">Maklumat Entiti</div>

          <?php if ($entiti_list): ?>

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
                      <div class="info-label">Bahagian Unit</div>
                      <div class="info-value"><?= $ent['bahagianunit'] ?></div>
                  </div>

                  <div class="info-row">
                      <div class="info-label">Maklumat Pegawai</div>
                      <div class="info-value">
                          <?= $ent['nama_user'] ?><br>
                          <?= $ent['jawatan_user'] ?><br>
                          <?= $ent['emel_user'] ?><br>
                          <?= $ent['notelefon_user'] ?>
                      </div>
                  </div>

                  <div class="info-row">
                      <div class="info-label">Carta</div>
                      <div class="info-value"><?= $ent['carta'] ?></div>
                  </div>

                  <hr style="margin: 18px 0;">
              <?php endforeach; ?>

          <?php else: ?>
              <p class="text-muted">Tiada maklumat entiti.</p>
          <?php endif; ?>
      </div>


      <!-- SECTION: AKSES -->
      <div class="view-section-box">
          <div class="view-section-title">Maklumat Akses Sistem</div>

          <?php if ($akses_list): ?>

              <?php foreach ($akses_list as $akses): ?>
                  <div class="info-row">
                      <div class="info-label">Bahagian/Unit</div>
                      <div class="info-value"><?= $akses['bahagianunit'] ?></div>
                  </div>

                  <div class="info-row">
                      <div class="info-label">Kategori Pengguna</div>
                      <div class="info-value">
                          <?php 
                              $kategori = [];
                              if ($akses['jenis_dalaman']) $kategori[] = "Pengguna Dalaman";
                              if ($akses['jenis_umum']) $kategori[] = "Pengguna Umum";
                              echo implode(", ", $kategori);
                          ?>
                      </div>
                  </div>

              <?php endforeach; ?>

          <?php else: ?>
              <p class="text-muted">Tiada maklumat akses sistem.</p>
          <?php endif; ?>
      </div>


      <!-- SECTION: ENTITI -->
      <div class="view-section-box">
          <div class="view-section-title">Maklumat Entiti</div>

          <?php if ($entiti_list): ?>

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
                      <div class="info-label">Bahagian / Unit</div>
                      <div class="info-value"><?= $ent['bahagianunit'] ?></div>
                  </div>

                  <div class="info-row">
                      <div class="info-label">Nama Pegawai</div>
                      <div class="info-value"><?= $ent['nama_user'] ?></div>
                  </div>

                  <div class="info-row">
                      <div class="info-label">Jawatan Pegawai</div>
                      <div class="info-value"><?= $ent['jawatan_user'] ?></div>
                  </div>

                  <div class="info-row">
                      <div class="info-label">Emel Pegawai</div>
                      <div class="info-value"><?= $ent['emel_user'] ?></div>
                  </div>

                  <div class="info-row">
                      <div class="info-label">No. Telefon</div>
                      <div class="info-value"><?= $ent['notelefon_user'] ?></div>
                  </div>

                  <div class="info-row">
                      <div class="info-label">Carta</div>
                      <div class="info-value"><?= $ent['carta'] ?></div>
                  </div>

                  <hr style="margin: 18px 0;">

              <?php endforeach; ?>

          <?php else: ?>
              <p class="text-muted">Tiada maklumat entiti.</p>
          <?php endif; ?>
      </div>


      <!-- SECTION: PEGAWAI RUJUKAN -->
      <div class="view-section-box">
          <div class="view-section-title">Pegawai Rujukan Sistem</div>

          <?php if ($peg): ?>

              <div class="info-row">
                  <div class="info-label">Nama</div>
                  <div class="info-value"><?= $peg['nama_user'] ?></div>
              </div>

              <div class="info-row">
                  <div class="info-label">Jawatan</div>
                  <div class="info-value"><?= $peg['jawatan_user'] ?></div>
              </div>

          <?php else: ?>
              <p class="text-muted">Tiada maklumat pegawai rujukan.</p>
          <?php endif; ?>
      </div>

  </div>

</body>
</html>
