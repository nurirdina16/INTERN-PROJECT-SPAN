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
    <div class="dropdown-btn" role="button" aria-expanded="false" tabindex="0">
        <span><i class="bi bi-pencil-square"></i> Daftar Profil</span>
        <i class="bi bi-caret-down-fill dropdown-arrow"></i>
    </div>

    <div class="dropdown-container">
        <a href="daftar_sistem.php">Sistem</a>
        <a href="daftar_peralatan.php">Peralatan</a>
        <a href="daftar_pengguna.php">Pengguna</a>
    </div>

    <!-- PROFIL (dropdown) -->
    <div class="dropdown-btn">
        <i class="bi bi-folder2-open"></i> Profil
        <i class="bi bi-caret-down-fill dropdown-arrow"></i>
    </div>
    <div class="dropdown-container">
        <a href="profil_sistem.php">Sistem</a>
        <a href="profil_peralatan.php">Peralatan</a>
        <a href="profil_supplier.php">Supplier</a>
        <a href="profil_pengguna.php">Pengguna</a>
    </div>

    <!-- Laporan -->
    <a href="laporan.php" 
       class="nav-link <?= $current_page == 'laporan.php' ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
    </a>
</div>
