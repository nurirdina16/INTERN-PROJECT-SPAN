<?php
  require_once '../../app/config.php';
  require_once '../../app/auth.php';
  require_login();

  // Check if ID given
  $id = $_GET['id'] ?? null;
  if (!$id) {
    die("<div style='text-align:center;margin-top:50px;color:red;'>❌ ID sistem tidak sah.</div>");
  }

  // Fetch data by id_sistemutama
  $stmt = $pdo->prepare("
    SELECT su.*, sa.*, ks.*, ak.*, pr.*
    FROM sistem_utama su
    LEFT JOIN sistem_aplikasi sa ON su.id_sistemutama = sa.id_sistemutama
    LEFT JOIN kos_sistem ks ON su.id_sistemutama = ks.id_sistemutama
    LEFT JOIN akses_sistem ak ON su.id_sistemutama = ak.id_sistemutama
    LEFT JOIN pegawai_rujukan_sistem pr ON su.id_sistemutama = pr.id_sistemutama
    WHERE su.id_sistemutama = ?
  ");
  $stmt->execute([$id]);
  $data = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$data) {
    die("<div style='text-align:center;margin-top:50px;color:red;'>❌ Rekod tidak dijumpai.</div>");
  }
?>


<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Lihat Profil Sistem | Sistem Profil</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="../css/sidebar.css">
  <link href="../css/sistemUtama.css" rel="stylesheet">
</head>

<?php if (isset($_GET['updated'])): ?>
  <script>
    alert('✅ Profil sistem berjaya dikemaskini!');
  </script>
<?php endif; ?>

<body class="layout">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <div class="logo">
      <img src="../../assets/img/span-logo.png">
    </div>

    <div class="title">S I S T E M &nbsp; P R O F I L</div>

    <a href="../dashboard.php" class="nav-link">
      <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <a href="../sistemUtama.php" class="nav-link active">
      <i class="bi bi-pc-display"></i> Sistem Utama
    </a>

    <a href="../peralatan/peralatan.php" class="nav-link">
      <i class="bi bi-hdd-stack"></i> Peralatan
    </a>
  </div>

  <!-- CONTENT WRAPPER -->
  <div class="content">

    <!-- HEADER -->
    <?php include '../header.php'; ?>

    <div class="view-container">      

      <!-- HEADER BOX -->
      <div class="profile-header d-flex justify-content-between align-items-center">
        <h2 class="system-title">
          <i class="bi bi-grid-fill me-1"></i> 
          <?= htmlspecialchars($data['nama_sistem'] ?? 'Maklumat Sistem') ?>
        </h2>

        <button class="btn btn-primary shadow-sm edit-profile-btn" data-bs-toggle="modal" data-bs-target="#editModal">
          <i class="bi bi-pencil-square me-1"></i> Edit
        </button>
      </div>

      <!-- SECTION A -->
      <div class="profile-section">
        <h4 class="section-header"><i class="bi bi-building"></i> A. Maklumat Am Entiti</h4>
        <div class="info-grid">
          <div><span>Nama Entiti</span><p><?= $data['nama_entiti'] ?></p></div>
          <div><span>Tarikh Kemaskini</span><p><?= $data['tarikh_kemaskini'] ?></p></div>
          <div><span>Bahagian</span><p><?= $data['bahagian'] ?></p></div>
          <div><span>Alamat</span><p><?= $data['alamat'] ?></p></div>
          <div><span>Nama Ketua</span><p><?= $data['nama_ketua'] ?></p></div>
          <div><span>No Telefon</span><p><?= $data['no_telefon'] ?></p></div>
          <div><span>Emel Ketua</span><p><?= $data['emel_ketua'] ?></p></div>
          <div><span>CIO</span><p><?= $data['cio'] ?></p></div>
          <div><span>ICTSO</span><p><?= $data['ictso'] ?></p></div>
        </div>
      </div>

      <!-- SECTION B -->
      <div class="profile-section">
        <h4 class="section-header"><i class="bi bi-window-sidebar"></i> B. Maklumat Sistem Aplikasi</h4>
        <div class="info-grid">
          <div><span>Nama Sistem</span><p><?= $data['nama_sistem'] ?></p></div>
          <div><span>Objektif</span><p class="long-text"><?= nl2br($data['objektif']) ?></p></div>
          <div><span>Pemilik</span><p><?= $data['pemilik'] ?></p></div>
          <div><span>Tarikh Mula</span><p><?= $data['tarikh_mula'] ?></p></div>
          <div><span>Tarikh Guna</span><p><?= $data['tarikh_guna'] ?></p></div>
          <div><span>Bil Pengguna</span><p><?= $data['bil_pengguna'] ?></p></div>
          <div><span>Kaedah Pembangunan</span><p><?= $data['kaedah_pembangunan'] ?></p></div>
          <div><span>Bahasa Pengaturcaraan</span><p><?= $data['bahasa_pengaturcaraan'] ?></p></div>
          <div><span>Pangkalan Data</span><p><?= $data['pangkalan_data'] ?></p></div>
          <div><span>Integrasi</span><p><?= $data['integrasi'] ?></p></div>
        </div>
      </div>

      <!-- SECTION C -->
      <div class="profile-section">
        <h4 class="section-header"><i class="bi bi-cash-stack"></i> C. Kos Sistem</h4>
        <div class="info-grid">
          <div><span>Kos Keseluruhan</span><p>RM <?= number_format($data['keseluruhan'],2) ?></p></div>
          <div><span>Kos Perkakasan</span><p>RM <?= number_format($data['perkakasan'],2) ?></p></div>
          <div><span>Kos Perisian</span><p>RM <?= number_format($data['perisian'],2) ?></p></div>
          <div><span>Lesen Perisian</span><p>RM <?= number_format($data['lesen_perisian'],2) ?></p></div>
          <div><span>Penyelenggaraan</span><p>RM <?= number_format($data['penyelenggaraan'],2) ?></p></div>
          <div><span>Kos Lain</span><p>RM <?= number_format($data['kos_lain'],2) ?></p></div>
        </div>
      </div>

      <!-- SECTION D -->
      <div class="profile-section">
        <h4 class="section-header"><i class="bi bi-shield-lock"></i> D. Akses Sistem</h4>
        <div class="info-grid">
          <div><span>Kategori Dalaman</span><p><?= $data['kategori_dalaman'] ? 'Ya' : 'Tidak' ?></p></div>
          <div><span>Kategori Umum</span><p><?= $data['kategori_umum'] ? 'Ya' : 'Tidak' ?></p></div>
          <div><span>Pegawai Urus Akses</span><p><?= $data['pegawai_urus_akses'] ?></p></div>
        </div>
      </div>

      <!-- SECTION E -->
      <div class="profile-section">
        <h4 class="section-header"><i class="bi bi-person-badge"></i> E. Pegawai Rujukan Sistem</h4>
        <div class="info-grid">
          <div><span>Nama Pegawai</span><p><?= $data['nama_pegawai'] ?></p></div>
          <div><span>Jawatan & Gred</span><p><?= $data['jawatan_gred'] ?></p></div>
          <div><span>Bahagian</span><p><?= $data['bahagian'] ?></p></div>
          <div><span>Emel Pegawai</span><p><?= $data['emel_pegawai'] ?></p></div>
          <div><span>No. Telefon</span><p><?= $data['no_telefon'] ?></p></div>
        </div>
      </div>

    </div>
  </div>

  <!-- KEMASKINI PROFIL MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content shadow-lg border-0">

      <div class="modal-header text-white" style="background: linear-gradient(90deg, #006EA0, #0096C7);">
        <h5 class="modal-title fw-semibold"><i class="bi bi-pencil-square me-2"></i>Kemaskini Profil Sistem Utama</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form action="kemaskini_sistem.php" method="POST">
        <input type="hidden" name="id_sistemutama" value="<?= $data['id_sistemutama'] ?>">

        <div class="modal-body p-4" style="background-color:#f7fbfd; max-height: 75vh; overflow-y:auto;">

          <!-- A. Maklumat Am Entiti -->
          <div class="section-card">
            <div class="section-title">A. Maklumat Am Entiti</div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">Nama Entiti</label>
                <input type="text" name="nama_entiti" class="form-control" value="<?= htmlspecialchars($data['nama_entiti'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tarikh Kemaskini</label>
                <input type="date" name="tarikh_kemaskini" class="form-control" value="<?= htmlspecialchars($data['tarikh_kemaskini'] ?? '') ?>">
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6"><label class="form-label">Bahagian</label><input type="text" name="bahagian" class="form-control" value="<?= htmlspecialchars($data['su_bahagian'] ?? '') ?>"></div>
              <div class="col-md-6"><label class="form-label">Alamat</label><input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($data['alamat'] ?? '') ?>"></div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-4"><label class="form-label">Nama Ketua Bahagian IT</label><input type="text" name="nama_ketua" class="form-control" value="<?= htmlspecialchars($data['nama_ketua'] ?? '') ?>"></div>
              <div class="col-md-4"><label class="form-label">No. Telefon</label><input type="text" name="no_telefon" class="form-control" value="<?= htmlspecialchars($data['no_telefon'] ?? '') ?>"></div>
              <div class="col-md-4"><label class="form-label">No. Faks</label><input type="text" name="no_faks" class="form-control" value="<?= htmlspecialchars($data['no_faks'] ?? '') ?>"></div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-4"><label class="form-label">Emel Ketua Bahagian IT</label><input type="email" name="emel_ketua" class="form-control" value="<?= htmlspecialchars($data['emel_ketua'] ?? '') ?>"></div>
              <div class="col-md-4"><label class="form-label">Nama CIO</label><input type="text" name="cio" class="form-control" value="<?= htmlspecialchars($data['cio'] ?? '') ?>"></div>
              <div class="col-md-4"><label class="form-label">Nama ICTSO</label><input type="text" name="ictso" class="form-control" value="<?= htmlspecialchars($data['ictso'] ?? '') ?>"></div>
            </div>

            <div class="mb-3">
              <label class="form-label">Carta Organisasi</label>
              <input type="text" name="carta_organisasi" class="form-control" value="<?= htmlspecialchars($data['carta_organisasi'] ?? '') ?>">
            </div>
          </div>

          <!-- B. Maklumat Sistem Aplikasi -->
          <div class="section-card">
            <div class="section-title">B. Maklumat Sistem Aplikasi</div>

            <div class="row g-3 mb-3">
              <div class="col-md-6"><label class="form-label">Nama Sistem</label><input type="text" name="nama_sistem" class="form-control" value="<?= htmlspecialchars($data['nama_sistem'] ?? '') ?>"></div>
              <div class="col-md-6"><label class="form-label">Objektif</label><textarea name="objektif" class="form-control" rows="1"><?= htmlspecialchars($data['objektif'] ?? '') ?></textarea></div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6"><label class="form-label">Pemilik Sistem</label><input type="text" name="pemilik" class="form-control" value="<?= htmlspecialchars($data['pemilik'] ?? '') ?>"></div>
              <div class="col-md-2"><label class="form-label">Tarikh Mula</label><input type="date" name="tarikh_mula" class="form-control" value="<?= htmlspecialchars($data['tarikh_mula'] ?? '') ?>"></div>
              <div class="col-md-2"><label class="form-label">Tarikh Siap</label><input type="date" name="tarikh_siap" class="form-control" value="<?= htmlspecialchars($data['tarikh_siap'] ?? '') ?>"></div>
              <div class="col-md-2"><label class="form-label">Tarikh Guna</label><input type="date" name="tarikh_guna" class="form-control" value="<?= htmlspecialchars($data['tarikh_guna'] ?? '') ?>"></div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-3"><label class="form-label">Bil. Pengguna</label><input type="number" name="bil_pengguna" class="form-control" value="<?= htmlspecialchars($data['bil_pengguna'] ?? '') ?>"></div>
              <div class="col-md-3"><label class="form-label">Kaedah Pembangunan</label>
                <select name="kaedah_pembangunan" class="form-control">
                  <option value="In-House" <?= ($data['kaedah_pembangunan'] ?? '')=='In-House' ? 'selected' : '' ?>>In-House</option>
                  <option value="Outsource" <?= ($data['kaedah_pembangunan'] ?? '')=='Outsource' ? 'selected' : '' ?>>Outsource</option>
                </select>
              </div>
              <div class="col-md-3"><label class="form-label">In-House</label><input type="text" name="inhouse" class="form-control" value="<?= htmlspecialchars($data['inhouse'] ?? '') ?>"></div>
              <div class="col-md-3"><label class="form-label">Outsource</label><input type="text" name="outsource" class="form-control" value="<?= htmlspecialchars($data['outsource'] ?? '') ?>"></div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-3"><label class="form-label">Bil Modul</label><input type="text" name="bil_modul" class="form-control" value="<?= htmlspecialchars($data['bil_modul'] ?? '') ?>"></div>
              <div class="col-md-3"><label class="form-label">Kategori</label><input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($data['kategori'] ?? '') ?>"></div>
              <div class="col-md-3"><label class="form-label">Bahasa Pengaturcaraan</label><input type="text" name="bahasa_pengaturcaraan" class="form-control" value="<?= htmlspecialchars($data['bahasa_pengaturcaraan'] ?? '') ?>"></div>
              <div class="col-md-3"><label class="form-label">Pangkalan Data</label><input type="text" name="pangkalan_data" class="form-control" value="<?= htmlspecialchars($data['pangkalan_data'] ?? '') ?>"></div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6"><label class="form-label">Rangkaian</label><textarea name="rangkaian" class="form-control" rows="2"><?= htmlspecialchars($data['rangkaian'] ?? '') ?></textarea></div>
              <div class="col-md-6"><label class="form-label">Integrasi</label><textarea name="integrasi" class="form-control" rows="2"><?= htmlspecialchars($data['integrasi'] ?? '') ?></textarea></div>
            </div>

            <div class="mb-3"><label class="form-label">Penyelenggaraan</label><input type="text" name="penyelenggaraan" class="form-control" value="<?= htmlspecialchars($data['penyelenggaraan'] ?? '') ?>"></div>
          </div>

          <!-- C. Kos Sistem -->
          <div class="section-card">
            <div class="section-title">C. Kos Sistem</div>

            <div class="row g-3 mb-3">
              <div class="col-md-4"><label class="form-label">Kos Keseluruhan (RM)</label><input type="number" step="0.01" name="keseluruhan" class="form-control" value="<?= htmlspecialchars($data['keseluruhan'] ?? 0) ?>"></div>
              <div class="col-md-4"><label class="form-label">Kos Perkakasan (RM)</label><input type="number" step="0.01" name="perkakasan" class="form-control" value="<?= htmlspecialchars($data['perkakasan'] ?? 0) ?>"></div>
              <div class="col-md-4"><label class="form-label">Kos Perisian (RM)</label><input type="number" step="0.01" name="perisian" class="form-control" value="<?= htmlspecialchars($data['perisian'] ?? 0) ?>"></div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-4"><label class="form-label">Lesen Perisian (RM)</label><input type="number" step="0.01" name="lesen_perisian" class="form-control" value="<?= htmlspecialchars($data['lesen_perisian'] ?? 0) ?>"></div>
              <div class="col-md-4"><label class="form-label">Penyelenggaraan (RM)</label><input type="number" step="0.01" name="penyelenggaraan_kos" class="form-control" value="<?= htmlspecialchars($data['ks_penyelenggaraan'] ?? 0) ?>"></div>
              <div class="col-md-4"><label class="form-label">Kos Lain (RM)</label><input type="number" step="0.01" name="kos_lain" class="form-control" value="<?= htmlspecialchars($data['kos_lain'] ?? 0) ?>"></div>
            </div>
          </div>

          <!-- D. Akses Sistem -->
          <div class="section-card">
            <div class="section-title">D. Akses Sistem</div>
            <div class="row g-3 mb-3">
              <div class="col-md-4"><label class="form-label">Kategori Dalaman</label>
                <select name="kategori_dalaman" class="form-control">
                  <option value="1" <?= ($data['kategori_dalaman'] ?? 0)==1?'selected':'' ?>>Ya</option>
                  <option value="0" <?= ($data['kategori_dalaman'] ?? 0)==0?'selected':'' ?>>Tidak</option>
                </select>
              </div>
              <div class="col-md-4"><label class="form-label">Kategori Umum</label>
                <select name="kategori_umum" class="form-control">
                  <option value="1" <?= ($data['kategori_umum'] ?? 0)==1?'selected':'' ?>>Ya</option>
                  <option value="0" <?= ($data['kategori_umum'] ?? 0)==0?'selected':'' ?>>Tidak</option>
                </select>
              </div>
              <div class="col-md-4"><label class="form-label">Pegawai Urus Akses</label><input type="text" name="pegawai_urus_akses" class="form-control" value="<?= htmlspecialchars($data['pegawai_urus_akses'] ?? '') ?>"></div>
            </div>
          </div>

          <!-- E. Pegawai Rujukan Sistem -->
          <div class="section-card mb-2">
            <div class="section-title">E. Pegawai Rujukan Sistem</div>
            <div class="row g-3 mb-3">
              <div class="col-md-3"><label class="form-label">Nama Pegawai</label><input type="text" name="nama_pegawai" class="form-control" value="<?= htmlspecialchars($data['nama_pegawai'] ?? '') ?>"></div>
              <div class="col-md-3"><label class="form-label">Jawatan & Gred</label><input type="text" name="jawatan_gred" class="form-control" value="<?= htmlspecialchars($data['jawatan_gred'] ?? '') ?>"></div>
              <div class="col-md-3"><label class="form-label">Bahagian</label><input type="text" name="bahagian_pegawai" class="form-control" value="<?= htmlspecialchars($data['pr_bahagian'] ?? '') ?>"></div>
              <div class="col-md-3"><label class="form-label">Emel Pegawai</label><input type="email" name="emel_pegawai" class="form-control" value="<?= htmlspecialchars($data['emel_pegawai'] ?? '') ?>"></div>
            </div>
            <div class="mb-3"><label class="form-label">No. Telefon Pegawai</label><input type="text" name="no_telefon_pegawai" class="form-control" value="<?= htmlspecialchars($data['no_telefon'] ?? '') ?>"></div>
          </div>

        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Batal</button>
          <button type="submit" class="btn btn-success px-4"><i class="bi bi-save2 me-1"></i>Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>
