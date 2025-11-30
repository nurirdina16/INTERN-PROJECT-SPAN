<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id'])) {
    header("Location: profil_pengguna.php");
    exit;
}

$id = intval($_GET['id']);

// ===========================
// FETCH DATA PROFIL PENGGUNA
// ===========================
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.id_userprofile,
            u.nama_user,
            u.jawatan_user,
            u.emel_user,
            u.notelefon_user,
            u.fax_user,
            b.bahagianunit
        FROM lookup_userprofile u
        LEFT JOIN lookup_bahagianunit b 
            ON u.id_bahagianunit = b.id_bahagianunit
        WHERE u.id_userprofile = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profil) {
        die("<div class='alert alert-danger m-4'>Rekod pengguna tidak ditemui!</div>");
    }

} catch (PDOException $e) {
    die("<div class='alert alert-danger m-4'>SQL Error: " . $e->getMessage() . "</div>");
}

?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>View Pengguna | Sistem Profil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>

    <link href="css/profil.css" rel="stylesheet">
</head>

<body>
    <!-- SIDEBAR -->
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <!-- HEADER -->
        <?php include 'header.php'; ?>

        <div class="profil-card shadow-sm p-4">
            <div class="view-main-header">
                <div class="header-wrapper">
                    <i class="bi bi-person-badge-fill"></i>
                    <span><?= htmlspecialchars($profil['nama_user']); ?></span>

                    <a href="kemaskini_pengguna.php?id=<?= $profil['id_userprofile']; ?>"      
                    class="btn btn-warning btn-sm ms-auto">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
            </div>

            <!-- MAKLUMAT PENGGUNA -->
            <div class="view-section-box">
                <div class="view-section-title">MAKLUMAT PENGGUNA</div>

                <div class="info-row">
                    <div class="info-label">Nama Penuh</div>
                    <div class="info-value"><?= $profil['nama_user']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Jawatan</div>
                    <div class="info-value"><?= $profil['jawatan_user']; ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Bahagian / Unit</div>
                    <div class="info-value"><?= $profil['bahagianunit']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Emel</div>
                    <div class="info-value"><?= $profil['emel_user']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">No. Telefon</div>
                    <div class="info-value"><?= $profil['notelefon_user']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">No. Faks</div>
                    <div class="info-value"><?= $profil['fax_user']; ?></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
