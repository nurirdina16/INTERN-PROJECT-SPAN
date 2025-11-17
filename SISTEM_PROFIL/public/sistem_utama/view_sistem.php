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

      <!-- TOP BUTTONS -->
      

      <!-- HEADER BOX -->
      <div class="profile-header d-flex justify-content-between align-items-center">
        <h2 class="system-title">
          <i class="bi bi-grid-fill me-1"></i> 
          <?= htmlspecialchars($data['nama_sistem'] ?? 'Maklumat Sistem') ?>
        </h2>

        <button class="btn btn-primary shadow-sm edit-profile-btn" data-bs-toggle="modal" data-bs-target="#editModal">
          <i class="bi bi-pencil-square me-1"></i> Edit Profil
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
</body>


</html>
