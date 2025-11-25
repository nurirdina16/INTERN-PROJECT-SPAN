<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid ID.");
}

$id = intval($_GET['id']);

// Fetch profil sistem
$sql = "SELECT * FROM PROFIL_SISTEM WHERE id_profilsistem = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$profil = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$profil) die("Rekod tidak dijumpai.");

// Fetch sistem
$sql2 = "SELECT * FROM SISTEM WHERE id_profilsistem = ?";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([$id]);
$sistem = $stmt2->fetch(PDO::FETCH_ASSOC);

// Fetch lookup data for dropdowns
$statuses = $pdo->query("SELECT * FROM LOOKUP_STATUS")->fetchAll(PDO::FETCH_ASSOC);
$jenisprofils = $pdo->query("SELECT * FROM LOOKUP_JENISPROFIL")->fetchAll(PDO::FETCH_ASSOC);
$bahagianunits = $pdo->query("SELECT * FROM LOOKUP_BAHAGIANUNIT")->fetchAll(PDO::FETCH_ASSOC);
$kaedahpembangunan = $pdo->query("SELECT * FROM LOOKUP_KAEDAHPEMBANGUNAN")->fetchAll(PDO::FETCH_ASSOC);

// Process form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_sistem = $_POST['nama_sistem'] ?? '';
    $id_status = $_POST['id_status'] ?? '';
    $id_jenisprofil = $_POST['id_jenisprofil'] ?? '';
    $pemilik_sistem = $_POST['pemilik_sistem'] ?? '';
    $tarikh_mula = $_POST['tarikh_mula'] ?? '';
    $tarikh_siap = $_POST['tarikh_siap'] ?? '';
    $tarikh_guna = $_POST['tarikh_guna'] ?? '';

    // Update PROFIL_SISTEM
    $stmt_up = $pdo->prepare("
        UPDATE PROFIL_SISTEM 
        SET id_status=?, id_jenisprofil=?
        WHERE id_profilsistem=?
    ");
    $stmt_up->execute([$id_status, $id_jenisprofil, $id]);

    // Update SISTEM
    $stmt_up2 = $pdo->prepare("
        UPDATE SISTEM 
        SET nama_sistem=?, pemilik_sistem=?, tarikh_mula=?, tarikh_siap=?, tarikh_guna=?
        WHERE id_profilsistem=?
    ");
    $stmt_up2->execute([$nama_sistem, $pemilik_sistem, $tarikh_mula, $tarikh_siap, $tarikh_guna, $id]);

    $message = "Rekod berjaya dikemaskini.";
    // Refresh data
    $stmt->execute([$id]);
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt2->execute([$id]);
    $sistem = $stmt2->fetch(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
    <title>Kemaskini Sistem | Profil Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/profil.css">
</head>

<body>

    <div class="container py-4">
        <!-- FIXED HEADER + HOME -->
        <div class="sticky-top bg-white py-2 mb-3 d-flex align-items-center justify-content-between shadow-sm px-3" style="z-index: 1050;">
            <a href="profil_sistem.php" class="btn btn-secondary">
                <i class="bi bi-house-door"></i>
            </a>
            <div style="flex: 1;"><?php include 'header.php'; ?></div>
        </div>

        <div class="card profil-card shadow-sm p-4">
            <div class="title-section mb-4">Kemaskini Sistem</div>

            <?php if($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Sistem</label>
                    <input type="text" name="nama_sistem" class="form-control" value="<?= htmlspecialchars($sistem['nama_sistem'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="id_status" class="form-select" required>
                        <?php foreach($statuses as $s): ?>
                            <option value="<?= $s['id_status'] ?>" <?= $profil['id_status']==$s['id_status']?'selected':'' ?>><?= $s['status'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Profil</label>
                    <select name="id_jenisprofil" class="form-select" required>
                        <?php foreach($jenisprofils as $j): ?>
                            <option value="<?= $j['id_jenisprofil'] ?>" <?= $profil['id_jenisprofil']==$j['id_jenisprofil']?'selected':'' ?>><?= $j['jenisprofil'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pemilik Sistem</label>
                    <select name="pemilik_sistem" class="form-select" required>
                        <?php foreach($bahagianunits as $b): ?>
                            <option value="<?= $b['id_bahagianunit'] ?>" <?= $sistem['pemilik_sistem']==$b['id_bahagianunit']?'selected':'' ?>><?= $b['bahagianunit'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tarikh Mula</label>
                        <input type="date" name="tarikh_mula" class="form-control" value="<?= $sistem['tarikh_mula'] ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tarikh Siap</label>
                        <input type="date" name="tarikh_siap" class="form-control" value="<?= $sistem['tarikh_siap'] ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tarikh Guna</label>
                        <input type="date" name="tarikh_guna" class="form-control" value="<?= $sistem['tarikh_guna'] ?>">
                    </div>
                </div>

                <button type="submit" class="btn add-btn mt-3"><i class="bi bi-check-circle"></i> Simpan</button>
            </form>
        </div>

    </div>

</body>
</html>
