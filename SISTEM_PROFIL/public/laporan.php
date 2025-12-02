<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();


?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Profil | Sistem Profil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/laporan.css">

    <script src="js/sidebar.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <?php include 'header.php'; ?>
            
        <div class="main-header mt-4 mb-1"><i class="bi bi-pc-display"></i> Laporan Profil</div>

        <div class="profil-card shadow-sm p-4">

            

        </div>
    </div>

</body>
</html>
