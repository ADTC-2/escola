<?php

include_once 'Congregacao.php';

class CongregacaoController {
    private $congregacao;

    public function __construct($db) {
        $this->congregacao = new Congregacao($db);
    }

    // Criar Congregação
    public function criarCongregacao($nome, $endereco) {
        if ($this->congregacao->criar($nome, $endereco)) {
            return json_encode(['status' => 'sucesso', 'mensagem' => 'Congregação criada com sucesso']);
        } else {
            return json_encode(['status' => 'erro', 'mensagem' => 'Falha ao criar congregação']);
        }
    }

    // Listar todas as Congregações
    public function listarCongregacoes() {
        $congregacoes = $this->congregacao->listar();
        return json_encode(['status' => 'sucesso', 'congregacoes' => $congregacoes]);
    }

    // Buscar Congregação por ID
    public function buscarCongregacaoPorId($id) {
        $congregacao = $this->congregacao->buscarPorId($id);
        if ($congregacao) {
            return json_encode(['status' => 'sucesso', 'congregacao' => $congregacao]);
        } else {
            return json_encode(['status' => 'erro', 'mensagem' => 'Congregação não encontrada']);
        }
    }

    // Atualizar Congregação
    public function atualizarCongregacao($id, $nome, $endereco) {
        if ($this->congregacao->atualizar($id, $nome, $endereco)) {
            return json_encode(['status' => 'sucesso', 'mensagem' => 'Congregação atualizada com sucesso']);
        } else {
            return json_encode(['status' => 'erro', 'mensagem' => 'Falha ao atualizar congregação']);
        }
    }

    // Deletar Congregação
    public function deletarCongregacao($id) {
        if ($this->congregacao->deletar($id)) {
            return json_encode(['status' => 'sucesso', 'mensagem' => 'Congregação deletada com sucesso']);
        } else {
            return json_encode(['status' => 'erro', 'mensagem' => 'Falha ao deletar congregação']);
        }
    }
}
