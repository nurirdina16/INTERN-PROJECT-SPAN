<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// Pastikan ID dihantar
if (!isset($_GET['id'])) {
    header("Location: profil.php?id_jenisprofil=3&msg=notfound");
    exit;
}

$id = intval($_GET['id']);

try {
    // 1. Check pembekal wujud
    $stmt = $pdo->prepare("SELECT * FROM lookup_pembekal WHERE id_pembekal = :id");
    $stmt->execute([':id' => $id]);
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$supplier) {
        header("Location: profil.php?id_jenisprofil=3&msg=notfound");
        exit;
    }

    // 2. Cuba padam
    $delete = $pdo->prepare("DELETE FROM lookup_pembekal WHERE id_pembekal = :id");
    $delete->execute([':id' => $id]);

    header("Location: profil.php?id_jenisprofil=3&msg=deleted");
    exit;

} catch (PDOException $e) {

    // 3. Detect FK constraint error 1451
    if ($e->getCode() == '23000') {
        header("Location: profil.php?id_jenisprofil=3&msg=fkerror");
        exit;
    }

    // 4. Error lain
    header("Location: profil.php?id_jenisprofil=3&msg=error");
    exit;
}
