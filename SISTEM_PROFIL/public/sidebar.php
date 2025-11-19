<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="logo">
        <img src="../assets/img/span-logo.png">
    </div>

    <div class="title">S I S T E M &nbsp; P R O F I L</div>

    <!-- Dashboard -->
    <a href="dashboard.php" 
       class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <!-- DAFTAR PROFIL (dropdown) -->
    <?php $daftar_active = in_array($current_page, ['daftar_sistem.php','daftar_peralatan.php','daftar_pengguna.php']); ?>
    <div class="dropdown-btn <?= $daftar_active ? 'active' : '' ?>" role="button">
        <span><i class="bi bi-pencil-square"></i> Daftar Profil</span>
        <i class="bi bi-caret-down-fill dropdown-arrow"></i>
    </div>
    <div class="dropdown-container" style="<?= $daftar_active ? 'display:block;' : '' ?>">
        <a href="daftar_sistem.php" class="<?= $current_page == 'daftar_sistem.php' ? 'active' : '' ?>">Sistem</a>
        <a href="daftar_peralatan.php" class="<?= $current_page == 'daftar_peralatan.php' ? 'active' : '' ?>">Peralatan</a>
        <a href="daftar_pengguna.php" class="<?= $current_page == 'daftar_pengguna.php' ? 'active' : '' ?>">Pengguna</a>
    </div>


    <!-- PROFIL (dropdown) -->
    <?php $profil_active = in_array($current_page, ['profil_sistem.php','profil_peralatan.php','profil_supplier.php','profil_pengguna.php']); ?>
    <div class="dropdown-btn <?= $profil_active ? 'active' : '' ?>" role="button">
        <span><i class="bi bi-folder2-open"></i> Profil</span>
        <i class="bi bi-caret-down-fill dropdown-arrow"></i>
    </div>
    <div class="dropdown-container" style="<?= $profil_active ? 'display:block;' : '' ?>">
        <a href="profil_sistem.php" class="<?= $current_page == 'profil_sistem.php' ? 'active' : '' ?>">Sistem</a>
        <a href="profil_peralatan.php" class="<?= $current_page == 'profil_peralatan.php' ? 'active' : '' ?>">Peralatan</a>
        <a href="profil_supplier.php" class="<?= $current_page == 'profil_supplier.php' ? 'active' : '' ?>">Supplier</a>
        <a href="profil_pengguna.php" class="<?= $current_page == 'profil_pengguna.php' ? 'active' : '' ?>">Pengguna</a>
    </div>

    <!-- Laporan -->
    <a href="laporan.php" 
       class="nav-link <?= $current_page == 'laporan.php' ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
    </a>
</div>
