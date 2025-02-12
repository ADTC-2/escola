<?php session_start(); ?>
<?php if (!isset($_SESSION['usuario_id'])) { header("Location: ../auth/login.php"); exit; } ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Classes</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3>Gestão de Classes</h3>
        <button class="btn btn-success mb-3" data-toggle="modal" data-target="#modalAdicionarClasse">Adicionar Classe</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nome da Classe</th>
                    <th>ID da Congregação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="listaClasses">
                <!-- A lista de classes será carregada aqui via AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Modal Adicionar Classe -->
    <div class="modal fade" id="modalAdicionarClasse" tabindex="-1" aria-labelledby="modalAdicionarClasseLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAdicionarClasseLabel">Adicionar Classe</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formAdicionarClasse">
                        <div class="mb-3">
                            <label for="nome">Nome da Classe</label>
                            <input type="text" class="form-control" id="nome" required>
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
            // Carregar classes via AJAX
            function listarClasses() {
                $.ajax({
                    url: "../controllers/classes.php",
                    type: "POST",
                    data: { acao: "listar" },
                    success: function(res) {
                        $("#listaClasses").html(res);
                    }
                });
            }

            listarClasses(); // Chama a função ao carregar a página

            // Adicionar classe via AJAX
            $("#formAdicionarClasse").submit(function(e) {
                e.preventDefault();
                var nome = $("#nome").val();
                var congregacao_id = $("#congregacao_id").val();

                $.ajax({
                    url: "../controllers/classes.php",
                    type: "POST",
                    data: { acao: "criar", nome: nome, congregacao_id: congregacao_id },
                    success: function(res) {
                        alert(res);
                        $("#modalAdicionarClasse").modal("hide");
                        listarClasses(); // Recarrega a lista de classes
                    }
                });
            });

            // Excluir classe via AJAX
            $(document).on("click", ".excluir", function() {
                var id = $(this).data("id");

                if (confirm("Tem certeza que deseja excluir?")) {
                    $.ajax({
                        url: "../controllers/classes.php",
                        type: "POST",
                        data: { acao: "excluir", id: id },
                        success: function(res) {
                            alert(res);
                            listarClasses(); // Recarrega a lista de classes
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
