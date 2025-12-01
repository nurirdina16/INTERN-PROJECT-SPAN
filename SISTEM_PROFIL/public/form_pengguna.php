<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// Fetch Bahagian/Unit
$bahagianunits = $pdo->query("SELECT * FROM lookup_bahagianunit ORDER BY bahagianunit ASC")->fetchAll(PDO::FETCH_ASSOC);

// Initialize variables
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_user = trim($_POST['nama_user']);
    $jawatan_user = trim($_POST['jawatan_user']);
    $emel_user = trim($_POST['emel_user']);
    $notelefon_user = trim($_POST['notelefon_user']);
    $fax_user = trim($_POST['fax_user']);
    $id_bahagianunit = $_POST['id_bahagianunit'];

    // Basic validation
    if (!$nama_user || !$emel_user || !$id_bahagianunit) {
        $errors[] = "Sila lengkapkan semua medan wajib.";
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lookup_userprofile WHERE emel_user = :emel");
    $stmt->execute([':emel' => $emel_user]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Emel sudah digunakan.";
    }

    // Insert to database if no errors
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO lookup_userprofile 
            (nama_user, jawatan_user, emel_user, notelefon_user, fax_user, id_bahagianunit)
            VALUES (:nama, :jawatan, :emel, :notelefon, :fax, :id_bahagianunit)
        ");
        $result = $stmt->execute([
            ':nama' => $nama_user,
            ':jawatan' => $jawatan_user,
            ':emel' => $emel_user,
            ':notelefon' => $notelefon_user,
            ':fax' => $fax_user,
            ':id_bahagianunit' => $id_bahagianunit
        ]);

        if ($result) {
            $success = true;
        } else {
            $errors[] = "Gagal menyimpan data pengguna.";
        }
    }
}

?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengguna | Sistem Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    
    <link rel="stylesheet" href="css/profil.css">

    <script src="js/sidebar.js" defer></script> 
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <?php include 'header.php'; ?>

        <div class="main-header mt-4 mb-1"><i class="bi bi-person-plus"></i> Tambah Pengguna</div>

        <div class="profil-card shadow-sm p-4 tambah-pengguna-card" style="margin-left: 0;">
            <!-- Alerts -->
            <div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 300px;">
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Pengguna berjaya ditambah.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            <?php foreach($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
            <script>
                // Auto-hide alerts after 5 seconds and clear form if success
                setTimeout(() => {
                    const alerts = document.querySelectorAll('#alert-container .alert');
                    alerts.forEach(alert => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });

                    <?php if($success): ?>
                        // Clear form fields after success
                        const form = document.querySelector('form');
                        form.reset();
                    <?php endif; ?>
                }, 5000);
            </script>

            <form method="POST">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label>Nama Penuh <span class="text-danger">*</span></label>
                        <input type="text" name="nama_user" class="form-control" required value="<?= htmlspecialchars($_POST['nama_user'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Jawatan</label>
                        <input type="text" name="jawatan_user" class="form-control" value="<?= htmlspecialchars($_POST['jawatan_user'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Emel <span class="text-danger">*</span></label>
                        <input type="email" name="emel_user" class="form-control" required value="<?= htmlspecialchars($_POST['emel_user'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label>No Telefon</label>
                        <input type="text" name="notelefon_user" class="form-control" value="<?= htmlspecialchars($_POST['notelefon_user'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label>No Faks</label>
                        <input type="text" name="fax_user" class="form-control" value="<?= htmlspecialchars($_POST['fax_user'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Bahagian / Unit <span class="text-danger">*</span></label>
                        <select name="id_bahagianunit" class="form-control" required>
                            <option value="">-- Pilih Bahagian/Unit --</option>
                            <?php foreach($bahagianunits as $b): ?>
                                <option value="<?= $b['id_bahagianunit'] ?>" <?= (($_POST['id_bahagianunit'] ?? '') == $b['id_bahagianunit']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['bahagianunit']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="text-end">
                    <a href="profil.php?id_jenisprofil=4" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
