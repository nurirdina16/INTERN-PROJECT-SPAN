<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

function login($emel, $kata_laluan) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT ulog.id_userlog, ulog.kata_laluan, ulog.peranan, up.nama_user, up.emel_user
        FROM userlog ulog
        JOIN lookup_userprofile up ON ulog.id_userprofile = up.id_userprofile
        WHERE up.emel_user = ?
    ");
    $stmt->execute([$emel]);
    $user = $stmt->fetch();

    if ($user && password_verify($kata_laluan, $user['kata_laluan'])) {
        $_SESSION['userlog'] = [
            'id_userlog' => $user['id_userlog'], // ubah dari 'id'
            'id_userprofile' => $user['id_userprofile'], // optional, tapi bagus ada
            'nama' => $user['nama_user'],
            'emel' => $user['emel_user'],
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
