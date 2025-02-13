<?php
require_once "../models/Aluno.php";

$alunoModel = new Aluno();
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

try {
    switch ($acao) {
        case "listar":
            echo json_encode(["sucesso" => true, "data" => $alunoModel->listar()]);
            break;

        case "salvar":
            $id = $_POST["id"] ?? null;
            $dados = [
                "nome" => $_POST["nome"] ?? '',
                "data_nascimento" => $_POST["data_nascimento"] ?? '',
                "telefone" => $_POST["telefone"] ?? ''
            ];

            if ($id) {
                $dados["id"] = $id;
                $sucesso = $alunoModel->editar($dados);
                echo json_encode(["sucesso" => $sucesso, "mensagem" => "Aluno atualizado com sucesso"]);
            } else {
                $sucesso = $alunoModel->salvar($dados);
                echo json_encode(["sucesso" => $sucesso, "mensagem" => "Aluno cadastrado com sucesso"]);
            }
            break;

        case "editar":
            $id = $_GET["id"] ?? null;
            if ($id) {
                echo json_encode(["sucesso" => true, "data" => $alunoModel->buscarPorId($id)]);
            } else {
                echo json_encode(["sucesso" => false, "mensagem" => "ID não informado"]);
            }
            break;

        case "excluir":
            $id = $_POST["id"] ?? null;
            if ($id) {
                $sucesso = $alunoModel->excluir($id);
                echo json_encode(["sucesso" => $sucesso, "mensagem" => "Aluno excluído com sucesso"]);
            } else {
                echo json_encode(["sucesso" => false, "mensagem" => "ID não informado"]);
            }
            break;

        default:
            echo json_encode(["sucesso" => false, "mensagem" => "Ação inválida"]);
    }
} catch (Exception $e) {
    echo json_encode(["sucesso" => false, "mensagem" => "Erro: " . $e->getMessage()]);
}
?>



