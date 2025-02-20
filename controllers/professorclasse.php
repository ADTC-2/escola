<?php
require_once '../models/professorclasse.php';
require_once '../config/conexao.php';

class ProfessorClasse {
    private $model;

    public function __construct() {
        $this->model = new ProfessorClasse();
    }

    public function index() {
        $professor_classes = $this->model->listar();
        require '../views/classes_professores/index.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->adicionar($_POST['professor_id'], $_POST['classe_id']);
        }
        header("../views/classes_professores/index.php");
    }

    public function destroy() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $this->model->remover($_POST['id']);
        }
        header("Location: ../views/classes_professores/index.php");
    }
}
?>
