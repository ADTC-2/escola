<?php

class Chamada {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function gerarRelatorioFrequencia($classe_id, $data_inicio, $data_fim) {
        $sql = "SELECT a.nome, 
                       SUM(CASE WHEN ca.presente = 1 THEN 1 ELSE 0 END) AS presencas,
                       SUM(CASE WHEN ca.presente = 0 THEN 1 ELSE 0 END) AS faltas
                FROM chamada_alunos ca
                JOIN alunos a ON ca.aluno_id = a.id
                JOIN chamadas c ON ca.chamada_id = c.id
                WHERE c.classe_id = ? AND c.data BETWEEN ? AND ?
                GROUP BY a.nome
                ORDER BY presencas DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$classe_id, $data_inicio, $data_fim]);
        return $stmt->fetchAll();
    }

    public function alunosMaisAssiduosFaltosos($classe_id, $data_inicio, $data_fim) {
        $sql = "SELECT a.nome, 
                       SUM(CASE WHEN ca.presente = 1 THEN 1 ELSE 0 END) AS presencas,
                       SUM(CASE WHEN ca.presente = 0 THEN 1 ELSE 0 END) AS faltas
                FROM chamada_alunos ca
                JOIN alunos a ON ca.aluno_id = a.id
                JOIN chamadas c ON ca.chamada_id = c.id
                WHERE c.classe_id = ? AND c.data BETWEEN ? AND ?
                GROUP BY a.nome
                ORDER BY presencas DESC, faltas DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$classe_id, $data_inicio, $data_fim]);
        return $stmt->fetchAll();
    }
}
