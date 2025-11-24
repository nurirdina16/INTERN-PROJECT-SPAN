<?php
require '../app/config.php';

$id = $_GET['id_outsource'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM lookup_pic WHERE id_outsource = ?");
$stmt->execute([$id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
