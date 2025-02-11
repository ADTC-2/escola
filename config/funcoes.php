<?php
session_start();
require_once "conexao.php";

// Função para formatar datas
function formatarData($data) {
    return date("d/m/Y", strtotime($data));
}
?>