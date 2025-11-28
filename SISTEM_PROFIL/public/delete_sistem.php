<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

if (!isset($_GET['id'])) {
    header("Location: profil_sistem.php?error=missing_id");
    exit;
}

$id = intval($_GET['id']);

try {
    // Delete dari table SISTEM dulu (FK)
    $stmt1 = $pdo->prepare("DELETE FROM SISTEM WHERE id_profilsistem = :id");
    $stmt1->execute([':id' => $id]);

    // Delete dari table PROFIL
    $stmt2 = $pdo->prepare("DELETE FROM PROFIL WHERE id_profilsistem = :id");
    $stmt2->execute([':id' => $id]);

    header("Location: profil_sistem.php?success=deleted");
    exit;

} catch (PDOException $e) {
    header("Location: profil_sistem.php?error=db_error");
    exit;
}
