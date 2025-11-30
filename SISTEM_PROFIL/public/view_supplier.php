<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id'])) {
    header("Location: profil_supplier.php");
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("
        SELECT P.*, PIC.nama_PIC, PIC.emel_PIC, PIC.notelefon_PIC, PIC.fax_PIC, PIC.jawatan_PIC
        FROM lookup_pembekal P
        LEFT JOIN lookup_pic PIC ON P.id_PIC = PIC.id_PIC
        WHERE P.id_pembekal = ?
    ");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Rekod pembekal tidak dijumpai!");
    }

} catch (Exception $e) {
    die("Ralat pangkalan data.");
}
?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>View Supplier | Sistem Profil</title>

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
                    <span><?= htmlspecialchars($data['nama_syarikat']); ?></span>

                    <a href="kemaskini_supplier.php?id=<?= $data['id_pembekal']; ?>"      
                    class="btn btn-warning btn-sm ms-auto">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
            </div>

            <!-- MAKLUMAT PEMBEKAL -->
            <div class="view-section-box">
                <div class="view-section-title">MAKLUMAT PEMBEKAL</div>

                <div class="info-row">
                    <div class="info-label">Nama Syarikat</div>
                    <div class="info-value"><?= $data['nama_syarikat']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Alamat Syarikat</div>
                    <div class="info-value"><?= $data['alamat_syarikat']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tempoh Kontrak</div>
                    <div class="info-value"><?= $data['tempoh_kontrak']; ?></div>
                </div>
            </div>

            <!-- MAKLUMAT PIC -->
            <div class="view-section-box">
                <div class="view-section-title">MAKLUMAT PIC</div>

                <div class="info-row">
                    <div class="info-label">Nama PIC</div>
                    <div class="info-value"><?= $data['nama_PIC']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Jawatan</div>
                    <div class="info-value"><?= $data['jawatan_PIC']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= $data['emel_PIC']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">No Telefon</div>
                    <div class="info-value"><?= $data['notelefon_PIC']; ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">No Faks</div>
                    <div class="info-value"><?= $data['fax_PIC']; ?></div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
