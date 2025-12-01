<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// Fetch lookup data
$jenisprofil = $pdo->query("SELECT * FROM lookup_jenisprofil")->fetchAll(PDO::FETCH_ASSOC);
$status_list = $pdo->query("SELECT * FROM lookup_status")->fetchAll(PDO::FETCH_ASSOC);

// Initialize selected jenisprofil
$selectedJenis = $_GET['id_jenisprofil'] ?? null;

// Fetch profil if a jenisprofil is selected
$profil_list = [];
$display_add_button = false;

if ($selectedJenis) {
    if ($selectedJenis == 3) { // PEMBEKAL
        $stmt = $pdo->query("SELECT id_pembekal AS id, nama_syarikat AS nama FROM lookup_pembekal ORDER BY nama_syarikat ASC");
        $profil_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($selectedJenis == 4) { // PENGGUNA
        $stmt = $pdo->query("SELECT id_userprofile AS id, nama_user AS nama FROM lookup_userprofile ORDER BY nama_user ASC");
        $profil_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $display_add_button = true; // show add button for pengguna
    } else { 
        // NORMAL CASE: existing profil table
        $stmt = $pdo->prepare("
            SELECT p.id_profil AS id, p.nama_profil AS nama, s.status
            FROM profil p
            LEFT JOIN lookup_status s ON p.id_status = s.id_status
            WHERE p.id_jenisprofil = :id_jenisprofil
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([':id_jenisprofil' => $selectedJenis]);
        $profil_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Profil | Sistem Profil</title>
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

        <div class="main-header mt-4 mb-1"><i class="bi bi-pc-display"></i>Senarai Profil</div>

        <div class="profil-card shadow-sm p-4">
            <!-- DELETE FUNCTION -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-dismissible fade show 
                    <?php 
                        echo ($_GET['msg'] == 'deleted') ? 'alert-success' : 'alert-danger'; 
                    ?>
                    position-fixed top-0 end-0 mt-3 me-3" 
                    style="z-index: 1055; min-width: 280px;">
                    
                    <?php if ($_GET['msg'] == 'deleted'): ?>
                        Rekod berjaya dipadam.
                    <?php elseif ($_GET['msg'] == 'notfound'): ?>
                        Ralat: Pengguna tidak dijumpai.
                    <?php elseif ($_GET['msg'] == 'fkerror'): ?>
                        Ralat: Tidak boleh padam kerana pengguna masih digunakan dalam modul lain.
                    <?php else: ?>
                        Ralat: Gagal memadam pengguna.
                    <?php endif; ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <script>
                    // Auto hide after 4 sec
                    setTimeout(() => {
                        const alertEl = document.querySelector('.alert');
                        if (alertEl) new bootstrap.Alert(alertEl).close();
                    }, 4000);
                </script>
            <?php endif; ?>

            <!-- Jenis Profil Filter -->
            <form method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <label for="id_jenisprofil" class="form-label">Pilih Jenis Profil</label>
                        <select name="id_jenisprofil" id="id_jenisprofil" class="form-select" required onchange="this.form.submit()">
                            <option value="">-- Pilih Jenis Profil --</option>
                            <?php foreach ($jenisprofil as $jp): ?>
                                <option value="<?= $jp['id_jenisprofil'] ?>" <?= ($selectedJenis == $jp['id_jenisprofil']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($jp['jenisprofil']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 text-end">
                        <?php if ($display_add_button): ?>
                            <a href="form_pengguna.php" class="btn btn-success mt-2"><i class="bi bi-plus"></i> Tambah Pengguna</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <!-- Profil Table -->
            <?php if ($selectedJenis): ?>
                <table class="table table-hover align-middle sistem-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:10%">#</th>
                            <th>Nama</th>
                            <th class="text-center" style="width:25%">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($profil_list): ?>
                            <?php foreach ($profil_list as $index => $p): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($p['nama']) ?></td>
                                    <td class="text-center">
                                        <a href="<?= $selectedJenis == 3 ? 'view_supplier.php?id=' . $p['id'] : ($selectedJenis == 4 ? 'view_pengguna.php?id=' . $p['id'] : 'view_profil.php?id=' . $p['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="<?= $selectedJenis == 3 ? 'delete_supplier.php?id=' . $p['id'] : ($selectedJenis == 4 ? 'delete_pengguna.php?id=' . $p['id'] : 'delete_profil.php?id=' . $p['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Adakah anda pasti mahu padam?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">Tiada data dijumpai.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
