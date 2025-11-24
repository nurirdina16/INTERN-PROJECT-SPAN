<?php
require_once '../app/config.php';

$outsource_id = $_GET['outsource_id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT PIC.id_PIC, PIC.nama_PIC
    FROM LOOKUP_OUTSOURCE O
    LEFT JOIN LOOKUP_PIC PIC ON O.id_PIC = PIC.id_PIC
    WHERE O.id_outsource = ?
");
$stmt->execute([$outsource_id]);

$pic = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($pic);
