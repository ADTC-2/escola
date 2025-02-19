<?php
include '../includes/db.php';

$chamada_id = $_POST['chamada_id'];
$alunos = $_POST['alunos'];

foreach ($alunos as $aluno_id) {
    $stmt = $pdo->prepare("INSERT INTO chamada_alunos (chamada_id, aluno_id, presente) VALUES (?, ?, 1)");
    $stmt->execute([$chamada_id, $aluno_id]);
}

echo 'success';
?>