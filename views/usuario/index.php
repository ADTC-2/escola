<?php
require_once '../../auth/valida_sessao.php';
require_once '../../config/conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema E.B.D - Usuários</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Gerenciamento de Usuários</h2>
        <button class="btn btn-success mt-4" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
            Adicionar Novo Usuário
        </button><br><br>

        <table class="table table-striped" id="tabelaUsuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Congregação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="listaUsuarios">
                <!-- Usuários serão carregados aqui -->
            </tbody>
        </table>
    </div>

    <!-- Modal Cadastrar -->
    <div class="modal" id="modalCadastrar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCadastrarUsuario">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" required>
                        </div>
                        <div class="mb-3">
                            <label for="perfil" class="form-label">Perfil</label>
                            <select class="form-control" id="perfil" required>
                                <option value="admin">Administrador</option>
                                <option value="user">Usuário</option>
                                <option value="professor">Professor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="congregacao_id" class="form-label">Congregação</label>
                            <select class="form-control" id="congregacao_id" required>
                                <!-- As opções serão carregadas dinamicamente -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Função para listar os usuários
            function listarUsuarios() {
                $.post('../../controllers/usuarios.php', { acao: 'listar' }, function(response) {
                    if (response.sucesso) {
                        let lista = '';
                        response.data.forEach(usuario => {
                            lista += `<tr>
                                        <td>${usuario.id}</td>
                                        <td>${usuario.nome}</td>
                                        <td>${usuario.email}</td>
                                        <td>${usuario.perfil}</td>
                                        <td>${usuario.congregacao_nome}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm editar" data-id="${usuario.id}">Editar</button>
                                            <button class="btn btn-danger btn-sm excluir" data-id="${usuario.id}">Excluir</button>
                                        </td>
                                      </tr>`;
                        });
                        $('#listaUsuarios').html(lista);
                    }
                }, 'json');
            }

            listarUsuarios();

            // Função para salvar usuário
            $('#formCadastrarUsuario').submit(function(e) {
                e.preventDefault();
                const nome = $('#nome').val();
                const email = $('#email').val();
                const senha = $('#senha').val();
                const perfil = $('#perfil').val();
                const congregacao_id = $('#congregacao_id').val();

                $.post('../../controllers/usuarios.php', {
                    acao: 'salvar',
                    nome: nome,
                    email: email,
                    senha: senha,
                    perfil: perfil,
                    congregacao_id: congregacao_id
                }, function(response) {
                    if (response.sucesso) {
                        alert(response.mensagem);
                        $('#modalCadastrar').modal('hide');
                        listarUsuarios();
                    } else {
                        alert(response.mensagem);
                    }
                }, 'json');
            });
        });
    </script>
</body>
</html>
