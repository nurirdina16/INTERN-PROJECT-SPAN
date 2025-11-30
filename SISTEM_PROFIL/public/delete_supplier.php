<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id'])) {
    header("Location: profil_supplier.php?error=invalid");
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("DELETE FROM lookup_pembekal WHERE id_pembekal = ?");
    $stmt->execute([$id]);

    header("Location: profil_supplier.php?success=deleted");
    exit;

} catch (PDOException $e) {

    if ($e->getCode() == "23000") { 
        // Foreign key error
        header("Location: profil_supplier.php?error=referenced");
    } else {
        header("Location: profil_supplier.php?error=failed");
    }

    exit;
}
?>
