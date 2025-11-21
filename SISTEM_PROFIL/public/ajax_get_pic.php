<?php
require_once '../app/config.php';
$outsource_id = $_GET['outsource_id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT PIC.id_PIC, PIC.nama_PIC
    FROM LOOKUP_PIC PIC
    JOIN LOOKUP_OUTSOURCE O ON O.id_PIC = PIC.id_PIC
    WHERE O.id_outsource = ?
");
$stmt->execute([$outsource_id]);
$pics = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($pics);
