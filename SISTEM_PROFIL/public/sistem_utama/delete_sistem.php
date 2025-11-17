<?php
require_once '../../app/config.php';
require_once '../../app/auth.php';
require_login();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM sistem_aplikasi WHERE id_sistemutama = ?");
    if ($stmt->execute([$id])) {
        // Redirect back with success message
        header("Location: ../sistemUtama.php?delete=success");
        exit;
    } else {
        header("Location: ../sistemUtama.php?delete=fail");
        exit;
    }
} else {
    header("Location: ../sistemUtama.php");
    exit;
}
