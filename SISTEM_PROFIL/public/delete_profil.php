<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// 1. Pastikan ID diterima
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: profil.php?msg=notfound");
    exit;
}

$id = intval($_GET['id']);

try {
    // 2. Semak rekod wujud dalam PROFIL
    $stmt = $pdo->prepare("SELECT id_profil FROM profil WHERE id_profil = :id");
    $stmt->execute([':id' => $id]);
    
    if ($stmt->rowCount() === 0) {
        header("Location: profil.php?msg=notfound");
        exit;
    }

    // 3. DELETE PROFIL
    $delete = $pdo->prepare("DELETE FROM profil WHERE id_profil = :id");
    $delete->execute([':id' => $id]);

    header("Location: profil.php?msg=deleted");
    exit;

} catch (PDOException $e) {

    // 4. Foreign Key Error Code = 23000
    if ($e->getCode() == 23000) {
        header("Location: profil.php?msg=fkerror");
        exit;
    }

    // Error lain
    header("Location: profil.php?msg=error");
    exit;
}
?>
