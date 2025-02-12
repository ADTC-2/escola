<?php session_start(); ?>
<?php if (!isset($_SESSION['usuario_id'])) { header("Location: ../auth/login.php"); exit; } ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Professores</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3>Gestão de Professores</h3>
        <button class="btn btn-success mb-3" data-toggle="modal" data-target="#modalAdicionarProfessor">Adicionar Professor</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Congregação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="listaProfessores">
                <!-- A lista de professores será carregada aqui via AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Modal Adicionar Professor -->
    <div class="modal fade" id="modalAdicionarProfessor" tabindex="-1" aria-labelledby="modalAdicionarProfessorLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAdicionarProfessorLabel">Adicionar Professor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formAdicionarProfessor">
                        <div class="mb-3">
                            <label for="usuario_id">ID do Usuário</label>
                            <input type="number" class="form-control" id="usuario_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="congregacao_id">ID da Congregação</label>
                            <input type="number" class="form-control" id="congregacao_id" required>
                        </div>
                        <button type="submit" class="btn btn-success">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Carregar professores via AJAX
            function listarProfessores() {
                $.ajax({
                    url: "../controllers/professores.php",
                    type: "POST",
                    data: { acao: "listar" },
                    success: function(res) {
                        $("#listaProfessores").html(res);
                    }
                });
            }

            listarProfessores(); // Chama a função ao carregar a página

            // Adicionar professor via AJAX
            $("#formAdicionarProfessor").submit(function(e) {
                e.preventDefault();
                var usuario_id = $("#usuario_id").val();
                var congregacao_id = $("#congregacao_id").val();

                $.ajax({
                    url: "../controllers/professores.php",
                    type: "POST",
                    data: { acao: "criar", usuario_id: usuario_id, congregacao_id: congregacao_id },
                    success: function(res) {
                        alert(res);
                        $("#modalAdicionarProfessor").modal("hide");
                        listarProfessores(); // Recarrega a lista de professores
                    }
                });
            });

            // Excluir professor via AJAX
            $(document).on("click", ".excluir", function() {
                var id = $(this).data("id");

                if (confirm("Tem certeza que deseja excluir?")) {
                    $.ajax({
                        url: "../controllers/professores.php",
                        type: "POST",
                        data: { acao: "excluir", id: id },
                        success: function(res) {
                            alert(res);
                            listarProfessores(); // Recarrega a lista de professores
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
