<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);

// Ambil data senarai semua profil sistem
$sql = "
    SELECT 
        ps.id_profilsistem,
        s.nama_sistem,
        ls.status AS nama_status,
        lj.jenisprofil,
        bu.bahagianunit AS pemilik
    FROM PROFIL_SISTEM ps
    LEFT JOIN SISTEM s ON s.id_profilsistem = ps.id_profilsistem
    LEFT JOIN LOOKUP_STATUS ls ON ls.id_status = ps.id_status
    LEFT JOIN LOOKUP_JENISPROFIL lj ON lj.id_jenisprofil = ps.id_jenisprofil
    LEFT JOIN LOOKUP_BAHAGIANUNIT bu ON bu.id_bahagianunit = s.pemilik_sistem
    ORDER BY ps.id_profilsistem DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$senarai = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Profil Sistem | Sistem Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
    
    <link href="css/profil.css" rel="stylesheet">
</head>

<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <!-- HEADER -->
        <?php include 'header.php'; ?>

        <div class="main-header mt-4 mb-3">
            <i class="bi bi-pc-display"></i> Senarai Profil Sistem Utama
        </div>

        <div class="profil-card shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="title-section">
                    <i class="bi bi-list-task"></i> Senarai Sistem Berdaftar
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table custom-table align-middle">
                    <thead>
                        <tr class="text-center">
                            <th>Bil</th>
                            <th>Nama Sistem</th>
                            <th>Status</th>
                            <th>Jenis Profil</th>
                            <th>Pemilik Sistem</th>
                            <th style="width:180px;">Tindakan</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($senarai) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Tiada rekod sistem.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php $bil = 1; foreach ($senarai as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $bil++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_sistem']) ?></td>
                                    <td class="text-center">
                                        <span class="badge status-badge">
                                            <?= htmlspecialchars($row['nama_status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center"><?= htmlspecialchars($row['jenisprofil']) ?></td>
                                    <td><?= htmlspecialchars($row['pemilik']) ?></td>
                                    <td class="text-center">
                                        <a href="sistem_utama/view_sistem.php?id=<?= $row['id_profilsistem'] ?>" 
                                        class="btn action-btn view-btn">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="sistem_utama/kemaskini_sistem.php?id=<?= $row['id_profilsistem'] ?>" 
                                        class="btn action-btn edit-btn">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="sistem_utama/delete_sistem.php?id=<?= $row['id_profilsistem'] ?>" 
                                        class="btn action-btn delete-btn"
                                        onclick="return confirm('Padam sistem ini?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</body>
</html>
