<?php
require_once '../app/config.php';
require_once '../app/auth.php';
require_login();

// Validate User ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: profil.php?id_jenisprofil=4&msg=invalid");
    exit;
}

$id = intval($_GET['id']);

// =====================
// CHECK EXISTING USER
// =====================
$stmt = $pdo->prepare("SELECT * FROM lookup_userprofile WHERE id_userprofile = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: profil.php?id_jenisprofil=4&msg=notfound");
    exit;
}

// ===============================
// TRY DELETE (HANDLE FK ERROR)
// ===============================
try {
    $stmt = $pdo->prepare("DELETE FROM lookup_userprofile WHERE id_userprofile = :id");
    $stmt->execute([':id' => $id]);

    header("Location: profil.php?id_jenisprofil=4&msg=deleted");
    exit;

} catch (PDOException $e) {

    // FOREIGN KEY CONFLICT
    if ($e->getCode() == "23000") {
        header("Location: profil.php?id_jenisprofil=4&msg=fkerror");
        exit;
    }

    // GENERAL ERROR
    header("Location: profil.php?id_jenisprofil=4&msg=error");
    exit;
}
