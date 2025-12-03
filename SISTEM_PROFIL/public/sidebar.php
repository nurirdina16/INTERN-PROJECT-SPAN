<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="logo">
        <img src="../assets/img/span-logo.png">
    </div>

    <div class="title">S I S T E M &nbsp; P R O F I L</div>

    <!-- Dashboard -->
    <a href="maindashboard.php" 
       class="nav-link <?= $current_page == 'maindashboard.php' ? 'active' : '' ?>">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <!-- DAFTAR PROFIL -->
    <a href="daftar_profil.php" 
       class="nav-link <?= $current_page == 'daftar_profil.php' ? 'active' : '' ?>">
        <i class="bi bi-pencil-square"></i> Daftar Profil
    </a>

    <!-- DAFTAR PROFIL -->
    <a href="profil.php" 
       class="nav-link <?= $current_page == 'profil.php' ? 'active' : '' ?>">
        <i class="bi bi-folder2-open"></i> Profil
    </a>

    <!-- Laporan -->
    <a href="laporan.php" 
       class="nav-link <?= $current_page == 'laporan.php' ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
    </a>
</div>
