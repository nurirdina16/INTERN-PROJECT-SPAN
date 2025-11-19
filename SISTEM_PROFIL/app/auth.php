<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

function login($emel, $kata_laluan) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM userlog WHERE emel = ?");
    $stmt->execute([$emel]);
    $user = $stmt->fetch();

    if ($user && password_verify($kata_laluan, $user['kata_laluan'])) {
        $_SESSION['userlog'] = [
            'id' => $user['id_user'],
            'nama' => $user['nama_penuh'],
            'emel' => $user['emel'],
            'peranan' => $user['peranan']
        ];
        return true;
    }
    return false;
}

function require_login() {
    if (!isset($_SESSION['userlog'])) {
        header("Location: /SISTEM_PROFIL/public/login.php");
        exit();
    }
}

function logout() {
    session_destroy();
    header("Location: /SISTEM_PROFIL/public/login.php");
    exit();
}
?>
