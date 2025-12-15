<?php
// app/config.php
//$host = '127.0.0.1';
//$db   = 'sistem_profil_span';
//$user = 'root';
//$pass = ''; // default empty password

//try {
//    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
//    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//} catch (PDOException $e) {
//    die("Gagal sambung ke pangkalan data: " . $e->getMessage());
//}

// app/config.php (UNTUK RENDER)

$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Gagal sambung ke pangkalan data: " . $e->getMessage());
}

?>
