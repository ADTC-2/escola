<?php
require_once '../models/Chamada.php';

header('Content-Type: text/html; charset=UTF-8');

$action = $_GET['action'] ?? '';

if ($action == 'getClasses') {
    echo Chamada::getClasses();
} elseif ($action == 'getAlunos') {
    $classeId = $_GET['classe'] ?? 0;
    echo Chamada::getAlunos($classeId);
} elseif ($action == 'salvar') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo Chamada::salvar($data);
}
?>
