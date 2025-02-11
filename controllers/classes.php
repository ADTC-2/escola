<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../models/Classe.php';
require_once '../config/conexao.php';

$classe = new Classe($pdo);

// Listar Classes
if (isset($_GET['acao']) && $_GET['acao'] == 'listar') {
    $classes = $classe->listarClasses();
    echo json_encode($classes);
    exit();
}

// Registrar Classe
if (isset($_POST['nome']) && isset($_POST['professor_id'])) {
    $classe->registrarClasse($_POST['nome'], $_POST['professor_id']);
    header("Location: ../views/classes.php");
    exit();
}

// Editar Classe
if (isset($_POST['id']) && isset($_POST['nome']) && isset($_POST['professor_id'])) {
    $classe->editarClasse($_POST['id'], $_POST['nome'], $_POST['professor_id']);
    header("Location: ../views/classes.php");
    exit();
}

// Excluir Classe
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $classe->excluirClasse($_GET['id']);
    header("Location: ../views/classes.php");
    exit();
}
?>

