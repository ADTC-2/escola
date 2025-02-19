<?php
include '../includes/db.php';

$data = $_POST['data'];
$classe_id = $_POST['classe_id'];
$professor_id = $_POST['professor_id'];

$stmt = $pdo->prepare("INSERT INTO chamadas (data, classe_id, professor_id) VALUES (?, ?, ?)");
$stmt->execute([$data, $classe_id, $professor_id]);

echo 'success';
?>