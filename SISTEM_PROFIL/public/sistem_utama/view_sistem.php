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
    SELECT 
      su.id_sistemutama AS su_id,
      su.nama_entiti AS su_nama_entiti,
      su.tarikh_kemaskini AS su_tarikh_kemaskini,
      su.bahagian AS su_bahagian,
      su.alamat AS su_alamat,
      su.nama_ketua AS su_nama_ketua,
      su.no_telefon AS su_no_telefon,
      su.no_faks AS su_no_faks,
      su.emel_ketua AS su_emel_ketua,
      su.cio AS su_cio,
      su.ictso AS su_ictso,
      su.carta_organisasi AS su_carta_organisasi,
      
      sa.nama_sistem AS sa_nama_sistem,
      sa.objektif AS sa_objektif,
      sa.pemilik AS sa_pemilik,
      sa.tarikh_mula AS sa_tarikh_mula,
      sa.tarikh_siap AS sa_tarikh_siap,
      sa.tarikh_guna AS sa_tarikh_guna,
      sa.bil_pengguna AS sa_bil_pengguna,
      sa.kaedah_pembangunan AS sa_kaedah_pembangunan,
      sa.inhouse AS sa_inhouse,
      sa.outsource AS sa_outsource,
      sa.bil_modul AS sa_bil_modul,
      sa.kategori AS sa_kategori,
      sa.bahasa_pengaturcaraan AS sa_bahasa_pengaturcaraan,
      sa.pangkalan_data AS sa_pangkalan_data,
      sa.rangkaian AS sa_rangkaian,
      sa.integrasi AS sa_integrasi,
      sa.penyelenggaraan AS sa_penyelenggaraan,

      ks.keseluruhan AS ks_keseluruhan,
      ks.perkakasan AS ks_perkakasan,
      ks.perisian AS ks_perisian,
      ks.lesen_perisian AS ks_lesen_perisian,
      ks.penyelenggaraan_kos AS ks_penyelenggaraan_kos,
      ks.kos_lain AS ks_kos_lain,

      ak.kategori_dalaman AS ak_kategori_dalaman,
      ak.kategori_umum AS ak_kategori_umum,
      ak.pegawai_urus_akses AS ak_pegawai_urus_akses,

      pr.nama_pegawai AS pr_nama_pegawai,
      pr.jawatan_gred AS pr_jawatan_gred,
      pr.bahagian AS pr_bahagian,
      pr.emel_pegawai AS pr_emel_pegawai,
      pr.no_telefon AS pr_no_telefon

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

<!-- DISPLAY DETAILS -->
      <!-- HEADER BOX -->
      <div class="profile-header d-flex justify-content-between align-items-center">
        <h2 class="system-title">
          <i class="bi bi-grid-fill me-1"></i> 
          <?= htmlspecialchars($data['sa_nama_sistem'] ?? 'Nama Sistem') ?>
        </h2>

        <button class="btn btn-primary shadow-sm edit-profile-btn" data-bs-toggle="modal" data-bs-target="#editModal">
          <i class="bi bi-pencil-square me-1"></i> Edit
        </button>
      </div>

      <!-- SECTION A -->
      <div class="profile-section">
        <h4 class="section-header"><i class="bi bi-building"></i>   MAKLUMAT AM ENTITI</h4>
        <div class="info-grid">
          <div><span>Nama Entiti</span><p><?= $data['su_nama_entiti'] ?></p></div>
          <div><span>Tarikh Kemaskini</span><p><?= $data['su_tarikh_kemaskini'] ?></p></div>
          <div><span>Nama Bahagian</span><p><?= $data['su_bahagian'] ?></p></div>
          <div><span>Alamat Pejabat</span><p><?= $data['su_alamat'] ?></p></div>
          <div><span>Nama Ketua Bahagian IT</span><p><?= $data['su_nama_ketua'] ?></p></div>
          <div><span>No. Telefon</span><p><?= $data['su_no_telefon'] ?></p></div>
          <div><span>No. Faks</span><p><?= $data['su_no_faks'] ?></p></div>
          <div><span>Emel Ketua Bahagian IT</span><p><?= $data['su_emel_ketua'] ?></p></div>
          <div><span>Nama Chief Information Officer (CIO)</span><p><?= $data['su_cio'] ?></p></div>
          <div><span>Nama Chief Security Officer (ICTSO)</span><p><?= $data['su_ictso'] ?></p></div>
          <div><span>Carta Organisasi Entiti</span><p><?= $data['su_carta_organisasi'] ?></p></div>
        </div>
      </div>

      <!-- SECTION B -->
      <div class="profile-section">
        <h4 class="section-header"><i class="bi bi-window-sidebar"></i>   MAKLUMAT SISTEM APLIKASI</h4>
        <div class="info-grid">
          <div><span>Nama Sistem</span><p><?= $data['sa_nama_sistem'] ?></p></div>
          <div><span>Objektif Sistem</span><p class="long-text"><?= nl2br($data['sa_objektif']) ?></p></div>
          <div><span>Pemilik Sistem</span><p><?= $data['sa_pemilik'] ?></p></div>
          <div><span>Tarikh Mula Pembangunan Sistem</span><p><?= $data['sa_tarikh_mula'] ?></p></div>
          <div><span>Tarikh Sistem Digunakan</span><p><?= $data['sa_tarikh_guna'] ?></p></div>
          <div><span>Anggaran Bilangan Pengguna</span><p><?= $data['sa_bil_pengguna'] ?></p></div>
          <div><span>Kaedah Pembangunan</span><p><?= $data['sa_kaedah_pembangunan'] ?></p></div>
          <div><span>Kaedah: In-House</span><p><?= $data['sa_inhouse'] ?></p></div>
          <div><span>Kaedah: Outsource</span><p><?= $data['sa_outsource'] ?></p></div>
          <div><span>Bilangan Modul</span><p><?= $data['sa_bil_modul'] ?></p></div>
          <div><span>Kategori Sistem</span><p><?= $data['sa_kategori'] ?></p></div>
          <div><span>Bahasa Pengaturcaraan</span><p><?= $data['sa_bahasa_pengaturcaraan'] ?></p></div>
          <div><span>Jenis Pangkalan Data</span><p><?= $data['sa_pangkalan_data'] ?></p></div>
          <div><span>Rangkaian</span><p><?= $data['sa_rangkaian'] ?></p></div>
          <div><span>Integrasi Sistem</span><p><?= $data['sa_integrasi'] ?></p></div>
          <div><span>Penyelenggara Sistem</span><p><?= $data['sa_penyelenggaraan'] ?></p></div>

        </div>
      </div>

      <!-- SECTION C -->
      <div class="profile-section">
        <h4 class="section-header"><i class="bi bi-cash-stack"></i>   KOS SISTEM</h4>
        <div class="info-grid">
          <div><span>Kos Keseluruhan Pembangunan</span><p>RM <?= number_format($data['ks_keseluruhan'],2) ?></p></div>
          <div><span>Kos Perkakasan</span><p>RM <?= number_format($data['ks_perkakasan'],2) ?></p></div>
          <div><span>Kos Perisian</span><p>RM <?= number_format($data['ks_perisian'],2) ?></p></div>
          <div><span>Kos Lesen Perisian</span><p>RM <?= number_format($data['ks_lesen_perisian'],2) ?></p></div>
          <div><span>Kos Penyelenggaraan</span><p>RM <?= number_format($data['ks_penyelenggaraan_kos'],2) ?></p></div>
          <div><span>Kos Lain</span><p>RM <?= number_format($data['ks_kos_lain'],2) ?></p></div>
        </div>
      </div>

      <!-- SECTION D -->
      <div class="profile-section">
        <h4 class="section-header"><i class="bi bi-shield-lock"></i>   AKSES SISTEM</h4>
        <div class="info-grid">
          <div>Kategori Jenis Pengguna</div>
          <div><span>Kategori: Dalaman</span><p><?= $data['ak_kategori_dalaman'] ? 'Ya' : 'Tidak' ?></p></div>
          <div><span>Kategori: Umum</span><p><?= $data['ak_kategori_umum'] ? 'Ya' : 'Tidak' ?></p></div>
          <div><span>Pegawai Mengurus Akses Pengguna</span><p><?= $data['ak_pegawai_urus_akses'] ?></p></div>
        </div>
      </div>

      <!-- SECTION E -->
      <div class="profile-section">
        <h4 class="section-header"><i class="bi bi-person-badge"></i>    PEGAWAI RUJUKAN</h4>
        <div class="info-grid">
          <div><span>Nama Pegawai</span><p><?= $data['pr_nama_pegawai'] ?></p></div>
          <div><span>Jawatan & Gred</span><p><?= $data['pr_jawatan_gred'] ?></p></div>
          <div><span>Bahagian / Seksyen / Unit</span><p><?= $data['pr_bahagian'] ?></p></div>
          <div><span>Emel</span><p><?= $data['pr_emel_pegawai'] ?></p></div>
          <div><span>No. Telefon</span><p><?= $data['pr_no_telefon'] ?></p></div>
        </div>
      </div>

    </div>
  </div>

<!-- KEMASKINI PROFIL FORM -->
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content shadow-lg border-0">

        <div class="modal-header text-white" style="background: linear-gradient(90deg, #006EA0, #0096C7);">
          <h5 class="modal-title fw-semibold"><i class="bi bi-pencil-square me-2"></i>Kemaskini Profil Sistem Utama</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <form action="kemaskini_sistem.php" method="POST">
          <input type="hidden" name="id_sistemutama" value="<?= $data['su_id'] ?>">

          <div class="modal-body p-4" style="background-color:#f7fbfd; max-height: 75vh; overflow-y:auto;">

            <!-- A. Maklumat Am Entiti -->
            <div class="section-card">
              <div class="section-title">A. Maklumat Am Entiti</div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label">Nama Entiti</label>
                  <input type="text" name="nama_entiti" class="form-control" value="<?= htmlspecialchars($data['su_nama_entiti'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Tarikh Kemaskini</label>
                  <input type="date" name="tarikh_kemaskini" class="form-control" value="<?= htmlspecialchars($data['su_tarikh_kemaskini'] ?? '') ?>">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">Nama Bahagian</label><input type="text" name="bahagian" class="form-control" value="<?= htmlspecialchars($data['su_bahagian'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="form-label">Alamat Pejabat</label><input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($data['su_alamat'] ?? '') ?>"></div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-4"><label class="form-label">Nama Ketua Bahagian IT</label><input type="text" name="nama_ketua" class="form-control" value="<?= htmlspecialchars($data['su_nama_ketua'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">No. Telefon</label><input type="text" name="no_telefon" class="form-control" value="<?= htmlspecialchars($data['su_no_telefon'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">No. Faks</label><input type="text" name="no_faks" class="form-control" value="<?= htmlspecialchars($data['su_no_faks'] ?? '') ?>"></div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-4"><label class="form-label">Emel Ketua Bahagian IT</label><input type="email" name="emel_ketua" class="form-control" value="<?= htmlspecialchars($data['su_emel_ketua'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Nama CIO</label><input type="text" name="cio" class="form-control" value="<?= htmlspecialchars($data['su_cio'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Nama ICTSO</label><input type="text" name="ictso" class="form-control" value="<?= htmlspecialchars($data['su_ictso'] ?? '') ?>"></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Carta Organisasi</label>
                <input type="text" name="carta_organisasi" class="form-control" value="<?= htmlspecialchars($data['su_carta_organisasi'] ?? '') ?>">
              </div>
            </div>

            <!-- B. Maklumat Sistem Aplikasi -->
            <div class="section-card">
              <div class="section-title">B. Maklumat Sistem Aplikasi</div>

              <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">Nama Sistem</label><input type="text" name="nama_sistem" class="form-control" value="<?= htmlspecialchars($data['sa_nama_sistem'] ?? '') ?>"></div>
                <div class="col-md-6"><label class="form-label">Objektif</label><textarea name="objektif" class="form-control" rows="1"><?= htmlspecialchars($data['sa_objektif'] ?? '') ?></textarea></div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">Pemilik Sistem</label><input type="text" name="pemilik" class="form-control" value="<?= htmlspecialchars($data['sa_pemilik'] ?? '') ?>"></div>
                <div class="col-md-2"><label class="form-label">Tarikh Mula</label><input type="date" name="tarikh_mula" class="form-control" value="<?= htmlspecialchars($data['sa_tarikh_mula'] ?? '') ?>"></div>
                <div class="col-md-2"><label class="form-label">Tarikh Siap</label><input type="date" name="tarikh_siap" class="form-control" value="<?= htmlspecialchars($data['sa_tarikh_siap'] ?? '') ?>"></div>
                <div class="col-md-2"><label class="form-label">Tarikh Guna</label><input type="date" name="tarikh_guna" class="form-control" value="<?= htmlspecialchars($data['sa_tarikh_guna'] ?? '') ?>"></div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-3"><label class="form-label">Bil. Pengguna</label><input type="number" name="bil_pengguna" class="form-control" value="<?= htmlspecialchars($data['sa_bil_pengguna'] ?? '') ?>"></div>
                <div class="col-md-3"><label class="form-label">Kaedah Pembangunan</label>
                  <select name="kaedah_pembangunan" class="form-control">
                    <option value="In-House" <?= ($data['sa_kaedah_pembangunan'] ?? '')=='In-House' ? 'selected' : '' ?>>In-House</option>
                    <option value="Outsource" <?= ($data['sa_kaedah_pembangunan'] ?? '')=='Outsource' ? 'selected' : '' ?>>Outsource</option>
                  </select>
                </div>
                <div class="col-md-3"><label class="form-label">In-House</label><input type="text" name="inhouse" class="form-control" value="<?= htmlspecialchars($data['sa_inhouse'] ?? '') ?>"></div>
                <div class="col-md-3"><label class="form-label">Outsource</label><input type="text" name="outsource" class="form-control" value="<?= htmlspecialchars($data['sa_outsource'] ?? '') ?>"></div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-3"><label class="form-label">Bilangan Modul</label><input type="text" name="bil_modul" class="form-control" value="<?= htmlspecialchars($data['sa_bil_modul'] ?? '') ?>"></div>
                <div class="col-md-3"><label class="form-label">Kategori Sistem</label><input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($data['sa_kategori'] ?? '') ?>"></div>
                <div class="col-md-3"><label class="form-label">Bahasa Pengaturcaraan</label><input type="text" name="bahasa_pengaturcaraan" class="form-control" value="<?= htmlspecialchars($data['sa_bahasa_pengaturcaraan'] ?? '') ?>"></div>
                <div class="col-md-3"><label class="form-label">Jenis Pangkalan Data</label><input type="text" name="pangkalan_data" class="form-control" value="<?= htmlspecialchars($data['sa_pangkalan_data'] ?? '') ?>"></div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label">Rangkaian</label><textarea name="rangkaian" class="form-control" rows="2"><?= htmlspecialchars($data['sa_rangkaian'] ?? '') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Integrasi Sistem Lain</label><textarea name="integrasi" class="form-control" rows="2"><?= htmlspecialchars($data['sa_integrasi'] ?? '') ?></textarea></div>
              </div>

              <div class="mb-3"><label class="form-label">Penyelenggaraan Sistem</label><input type="text" name="penyelenggaraan" class="form-control" value="<?= htmlspecialchars($data['sa_penyelenggaraan'] ?? '') ?>"></div>

            </div>

            <!-- C. Kos Sistem -->
            <div class="section-card">
              <div class="section-title">C. Kos Sistem</div>

              <div class="row g-3 mb-3">
                <div class="col-md-4"><label class="form-label">Kos Keseluruhan (RM)</label><input type="number" step="0.01" name="keseluruhan" class="form-control" value="<?= htmlspecialchars($data['ks_keseluruhan'] ?? 0) ?>"></div>
                <div class="col-md-4"><label class="form-label">Kos Perkakasan (RM)</label><input type="number" step="0.01" name="perkakasan" class="form-control" value="<?= htmlspecialchars($data['ks_perkakasan'] ?? 0) ?>"></div>
                <div class="col-md-4"><label class="form-label">Kos Perisian (RM)</label><input type="number" step="0.01" name="perisian" class="form-control" value="<?= htmlspecialchars($data['ks_perisian'] ?? 0) ?>"></div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-4"><label class="form-label">Kos Lesen Perisian (RM)</label><input type="number" step="0.01" name="lesen_perisian" class="form-control" value="<?= htmlspecialchars($data['ks_lesen_perisian'] ?? 0) ?>"></div>
                <div class="col-md-4"><label class="form-label">Kos Penyelenggaraan (RM)</label><input type="number" step="0.01" name="penyelenggaraan_kos" class="form-control" value="<?= htmlspecialchars($data['ks_penyelenggaraan_kos'] ?? 0) ?>"></div>
                <div class="col-md-4"><label class="form-label">Kos Lain (RM)</label><input type="number" step="0.01" name="kos_lain" class="form-control" value="<?= htmlspecialchars($data['ks_kos_lain'] ?? 0) ?>"></div>
              </div>
            </div>

            <!-- D. Akses Sistem -->
            <div class="section-card">
              <div class="section-title">D. Akses Sistem</div>
              <div class="row g-3 mb-3">
                <div class="col-md-4"><label class="form-label">Kategori Jenis Pengguna</label>
                <div class="col-md-4"><label class="form-label">Dalaman</label>
                  <select name="kategori_dalaman" class="form-control">
                    <option value="1" <?= ($data['ak_kategori_dalaman'] ?? 0)==1?'selected':'' ?>>Ya</option>
                    <option value="0" <?= ($data['ak_kategori_dalaman'] ?? 0)==0?'selected':'' ?>>Tidak</option>
                  </select>
                </div>
                <div class="col-md-4"><label class="form-label">Umum</label>
                  <select name="kategori_umum" class="form-control">
                    <option value="1" <?= ($data['ak_kategori_umum'] ?? 0)==1?'selected':'' ?>>Ya</option>
                    <option value="0" <?= ($data['ak_kategori_umum'] ?? 0)==0?'selected':'' ?>>Tidak</option>
                  </select>
                </div>
                <div class="col-md-4"><label class="form-label">Pegawai Mengurus Akses Pengguna</label><input type="text" name="pegawai_urus_akses" class="form-control" value="<?= htmlspecialchars($data['ak_pegawai_urus_akses'] ?? '') ?>"></div>
              </div>
            </div>

            <!-- E. Pegawai Rujukan Sistem -->
            <div class="section-card mb-2">
              <div class="section-title">E. Pegawai Rujukan Sistem</div>
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label">Nama Pegawai</label>
                  <input type="text" name="nama_pegawai" class="form-control" 
                  value="<?= htmlspecialchars($data['pr_nama_pegawai'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Jawatan & Gred</label>
                  <input type="text" name="jawatan_gred" class="form-control" 
                  value="<?= htmlspecialchars($data['pr_jawatan_gred'] ?? '') ?>">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <label class="form-label">Bahagian / Seksyen / Unit</label>
                  <input type="text" name="bahagian_pegawai" class="form-control" 
                  value="<?= htmlspecialchars($data['pr_bahagian'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Emel Pegawai</label>
                  <input type="email" name="emel_pegawai" class="form-control" 
                  value="<?= htmlspecialchars($data['pr_emel_pegawai'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                  <label class="form-label">No. Telefon</label>
                  <input type="text" name="no_telefon_pegawai" class="form-control" 
                  value="<?= htmlspecialchars($data['pr_no_telefon'] ?? '') ?>">
                </div>
              </div>
            </div>

          </div>

          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancel</button>
            <button type="submit" class="btn btn-success px-4"><i class="bi bi-save2 me-1"></i>Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>
