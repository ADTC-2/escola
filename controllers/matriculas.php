<?php
include_once '../config/conexao.php';
include_once '../models/matricula.php';
include_once '../models/aluno.php';
include_once '../models/classe.php';
include_once '../models/congregacao.php';
include_once '../models/professor.php';

class MatriculaController {
    private $matricula;
    private $aluno;
    private $classe;
    private $congregacao;
    private $professor;

    public function __construct($pdo) {
        if (!$pdo) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Falha na conexão com o banco de dados']);
            exit;
        }
        $this->matricula = new Matricula($pdo);
        $this->aluno = new Aluno($pdo);
        $this->classe = new Classe($pdo);
        $this->congregacao = new Congregacao($pdo);
        $this->professor = new Professor($pdo);
    }
    
    public function listar() {
        $result = $this->matricula->listarMatriculas();
        echo json_encode(['sucesso' => true, 'matriculas' => $result]);
    }

    public function cadastrar() {
        if (isset($_POST['aluno_id'], $_POST['classe_id'], $_POST['congregacao_id'], $_POST['professor_id'], $_POST['trimestre'])) {
            $aluno_id = $_POST['aluno_id'];
            $classe_id = $_POST['classe_id'];
            $congregacao_id = $_POST['congregacao_id'];
            $professor_id = $_POST['professor_id'];
            $trimestre = $_POST['trimestre'];
    
            if (!is_numeric($aluno_id) || !is_numeric($classe_id) || !is_numeric($congregacao_id) || !is_numeric($professor_id) || !is_numeric($trimestre)) {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
                return;
            }
    
            $result = $this->matricula->cadastrarMatricula($aluno_id, $classe_id, $congregacao_id, $professor_id, $trimestre);
            if ($result['sucesso']) {
                echo json_encode(['sucesso' => true]);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => $result['mensagem']]);
            }
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados faltando']);
        }
    }
    
    public function excluir() {
        if (isset($_POST['matricula_id']) && is_numeric($_POST['matricula_id'])) {
            $matricula_id = $_POST['matricula_id'];
            $result = $this->matricula->excluirMatricula($matricula_id);
            if ($result['sucesso']) {
                echo json_encode(['sucesso' => true]);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => $result['mensagem']]);
            }
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID da matrícula inválido']);
        }
    }

    public function editarMatricula() {
        if (isset($_POST['matricula_id'], $_POST['aluno_id'], $_POST['classe_id'], $_POST['congregacao_id'], $_POST['professor_id'], $_POST['trimestre'])) {
            $matricula_id = $_POST['matricula_id'];
            $aluno_id = $_POST['aluno_id'];
            $classe_id = $_POST['classe_id'];
            $congregacao_id = $_POST['congregacao_id'];
            $professor_id = $_POST['professor_id'];
            $trimestre = $_POST['trimestre'];
    
            if (!is_numeric($matricula_id) || !is_numeric($aluno_id) || !is_numeric($classe_id) || !is_numeric($congregacao_id) || !is_numeric($professor_id) || !is_numeric($trimestre)) {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
                return;
            }
    
            $result = $this->matricula->editarMatricula($matricula_id, $aluno_id, $classe_id, $congregacao_id, $professor_id, $trimestre);
            if ($result['sucesso']) {
                echo json_encode(['sucesso' => true]);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => $result['mensagem']]);
            }
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados faltando']);
        }
    }
    public function carregarMatricula() {
        if (isset($_POST['matricula_id']) && is_numeric($_POST['matricula_id'])) {
            $matricula_id = $_POST['matricula_id'];
            $matricula = $this->matricula->obterMatriculaPorId($matricula_id);
            if ($matricula) {
                echo json_encode(['sucesso' => true, 'matricula' => $matricula]);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Matrícula não encontrada']);
            }
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID da matrícula inválido']);
        }
    }
    

    public function listarAlunos() {
        $result = $this->aluno->listar();
        echo json_encode(['sucesso' => true, 'data' => $result]);
    }

    public function listarClasses() {
        $result = $this->classe->listarClasses();
        echo json_encode(['sucesso' => true, 'data' => $result]);
    }

    public function listarCongregacoes() {
        $result = $this->congregacao->listar();
        echo json_encode(['sucesso' => true, 'data' => $result]);
    }

    public function listarProfessores() {
        $result = $this->professor->listar();
        echo json_encode(['sucesso' => true, 'data' => $result]);
    }
}

// Criar instância do controller com a conexão existente
$controller = new MatriculaController($pdo);

// Captura a ação via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    switch ($_POST['acao']) {
        case 'listarMatriculas':
            $controller->listar();
            break;
        case 'cadastrarMatricula':
            $controller->cadastrar();
            break;
        case 'excluirMatricula':
            $controller->excluir();
            break;
        case 'editarMatricula':
            $controller->editarMatricula();
            break;
        case 'carregarMatricula':
            $controller->carregarMatricula();
            break;
        case 'listarAlunos':
            $controller->listarAlunos();
            break;
        case 'listarClasses':
            $controller->listarClasses();
            break;
        case 'listarCongregacoes':
            $controller->listarCongregacoes();
            break;
        case 'listarProfessores':
            $controller->listarProfessores();
            break;
        default:
            echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida']);
            break;
    }
}
?>






