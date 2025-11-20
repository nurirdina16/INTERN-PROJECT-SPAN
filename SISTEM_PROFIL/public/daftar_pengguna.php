<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

$current_page = basename($_SERVER['PHP_SELF']);
$message = "";

// ============================
// Ambil senarai bahagian unit
// ============================
$stmt = $pdo->query("SELECT id_bahagianunit, bahagianunit FROM lookup_bahagianunit ORDER BY bahagianunit ASC");
$bahagian_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ============================
// Bila user submit borang
// ============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_user = $_POST['nama_user'] ?? '';
    $jawatan_user = $_POST['jawatan_user'] ?? '';
    $emel_user = $_POST['emel_user'] ?? '';
    $notelefon_user = $_POST['notelefon_user'] ?? '';
    $fax_user = $_POST['fax_user'] ?? '';
    $alamat_pejabat = $_POST['alamat_pejabat'] ?? '';
    $id_bahagianunit = $_POST['id_bahagianunit'] ?? '';

    try {
        $sql = "INSERT INTO lookup_userprofile 
                (nama_user, jawatan_user, emel_user, notelefon_user, fax_user, alamat_pejabat, id_bahagianunit)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nama_user,
            $jawatan_user,
            $emel_user,
            $notelefon_user,
            $fax_user,
            $alamat_pejabat,
            $id_bahagianunit
        ]);

        $message = "<div class='alert alert-success'>Berjaya daftar pengguna!</div>";

    } catch (PDOException $e) {
        // Duplicate email error code = 1062
        if ($e->getCode() == 1062) {
            $message = "<div class='alert alert-danger fade-alert'>Emel sudah digunakan! Sila gunakan emel lain.</div>";
        } else {
            $message = "<div class='alert alert-danger fade-alert'>Ralat: " . $e->getMessage() . "</div>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pengguna | Sistem Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
    
    <link href="../public/css/pengguna.css" rel="stylesheet">
</head>

<script>
    // Auto hide alert after 3 seconds
    setTimeout(function() {
        const alert = document.querySelector('.fade-alert');
        if (alert) {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.8s ease';

            setTimeout(() => alert.remove(), 800);
        }
    }, 3000);
</script>

<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <!-- HEADER -->
        <?php include 'header.php'; ?>

        <div class="main-header mt-4 mb-3">
            <i class="bi bi-person-plus-fill"></i> Daftar Profil Pengguna
        </div>

        <div class="section-card p-4">

            <?= $message ?>

            <form method="POST" class="mt-3">
                <!-- Section Title -->
                <div class="section-title mb-4">
                    <span>Maklumat Pengguna</span>
                </div>

                <div class="row g-4">

                    <!-- NAMA -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Penuh</label>
                        <div class="input-group">
                            <span class="input-group-text form-icon"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="nama_user" class="form-control form-input" required>
                        </div>
                    </div>

                    <!-- JAWATAN -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jawatan</label>
                        <div class="input-group">
                            <span class="input-group-text form-icon"><i class="bi bi-briefcase-fill"></i></span>
                            <input type="text" name="jawatan_user" class="form-control form-input" required>
                        </div>
                    </div>

                    <!-- EMEL -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Emel</label>
                        <div class="input-group">
                            <span class="input-group-text form-icon"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" name="emel_user" class="form-control form-input" required>
                        </div>
                    </div>

                    <!-- NO TELEFON -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. Telefon</label>
                        <div class="input-group">
                            <span class="input-group-text form-icon"><i class="bi bi-telephone-fill"></i></span>
                            <input type="text" name="notelefon_user" class="form-control form-input">
                        </div>
                    </div>

                    <!-- FAX -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. Faks</label>
                        <div class="input-group">
                            <span class="input-group-text form-icon"><i class="bi bi-printer-fill"></i></span>
                            <input type="text" name="fax_user" class="form-control form-input">
                        </div>
                    </div>

                    <!-- BAHAGIAN -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bahagian / Unit</label>
                        <div class="input-group">
                            <span class="input-group-text form-icon"><i class="bi bi-diagram-3-fill"></i></span>
                            <select name="id_bahagianunit" class="form-select form-input" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($bahagian_list as $b): ?>
                                    <option value="<?= $b['id_bahagianunit'] ?>">
                                        <?= htmlspecialchars($b['bahagianunit']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- ALAMAT -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat Pejabat</label>
                        <textarea name="alamat_pejabat" class="form-control form-input" rows="3"></textarea>
                    </div>

                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-save px-4 py-2">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>

        </div>

    </div>
</body>
</html>
