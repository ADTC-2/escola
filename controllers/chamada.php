<?php
require_once '../models/Chamada.php';

$action = $_GET['action'] ?? '';

if ($action === 'getClasses') {
    echo json_encode(Chamada::getClasses());
} elseif ($action === 'getAlunos' && isset($_GET['classe'])) {
    echo json_encode(Chamada::getAlunos($_GET['classe']));
} elseif ($action === 'salvar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo Chamada::salvar($data);
}
?>




