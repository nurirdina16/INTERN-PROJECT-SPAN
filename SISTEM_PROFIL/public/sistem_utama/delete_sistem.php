<?php
require_once '../../app/config.php';
require_once '../../app/auth.php';
require_login();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        // Start transaction — prevent partial delete
        $pdo->beginTransaction();

        // 1. Delete from pegawai_rujukan_sistem
        $stmt = $pdo->prepare("DELETE FROM pegawai_rujukan_sistem WHERE id_sistemutama = ?");
        $stmt->execute([$id]);

        // 2. Delete from akses_sistem
        $stmt = $pdo->prepare("DELETE FROM akses_sistem WHERE id_sistemutama = ?");
        $stmt->execute([$id]);

        // 3. Delete from kos_sistem
        $stmt = $pdo->prepare("DELETE FROM kos_sistem WHERE id_sistemutama = ?");
        $stmt->execute([$id]);

        // 4. Delete from sistem_aplikasi
        $stmt = $pdo->prepare("DELETE FROM sistem_aplikasi WHERE id_sistemutama = ?");
        $stmt->execute([$id]);

        // 5. Finally delete from sistem_utama (parent row)
        $stmt = $pdo->prepare("DELETE FROM sistem_utama WHERE id_sistemutama = ?");
        $stmt->execute([$id]);

        // Commit changes
        $pdo->commit();

        header("Location: ../sistemUtama.php?delete=success");
        exit;

    } catch (Exception $e) {
        // Rollback if anything goes wrong
        $pdo->rollBack();
        header("Location: ../sistemUtama.php?delete=fail");
        exit;
    }

} else {
    header("Location: ../sistemUtama.php");
    exit;
}
