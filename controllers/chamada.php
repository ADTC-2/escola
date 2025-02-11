<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../models/Chamada.php';
require_once '../config/conexao.php';

$chamada = new Chamada($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classe_id = $_POST['classe_id'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];

    $relatorio = $chamada->gerarRelatorioFrequencia($classe_id, $data_inicio, $data_fim);
    echo json_encode($relatorio);
}
?>


