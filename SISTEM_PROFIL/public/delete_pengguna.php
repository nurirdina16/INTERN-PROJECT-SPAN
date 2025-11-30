<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id'])) {
    header("Location: profil_pengguna.php?error=missing_id");
    exit;
}

$id = intval($_GET['id']);

try {
    // 1. Pastikan tiada child record
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM peralatan WHERE pegawai_rujukan_peralatan = :id");
    $stmtCheck->execute([':id' => $id]);
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        header("Location: profil_pengguna.php?error=referenced");
        exit;
    }

    // 2. Delete user
    $stmt1 = $pdo->prepare("DELETE FROM lookup_userprofile WHERE id_userprofile = :id");
    $stmt1->execute([':id' => $id]);

    header("Location: profil_pengguna.php?success=deleted");
    exit;

// Gantikan blok catch sedia ada dalam DELETE_PENGGUNA.PHP
} catch (PDOException $e) {
    // HANYA UNTUK DEBUGGING. JANGAN BIARKAN INI DALAM PRODUKSI
    // header("Location: profil_pengguna.php?error=db_error");
    // exit; 

    die("Ralat Pangkalan Data: " . $e->getMessage()); // Ini akan memaparkan ralat SQL sebenar
}

?>
