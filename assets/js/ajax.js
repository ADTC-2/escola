$(document).ready(function () {
    function carregarAlunos() {
        $.get('/escola_biblica/controllers/alunos.php', function (data) {
            console.log("Resposta do servidor:", data); // Debug

            if (data.status === "sucesso" && Array.isArray(data.dados) && data.dados.length > 0) {
                let html = "";
                data.dados.forEach(aluno => {
                    html += `<tr>
                        <td>${aluno.nome}</td>
                        <td>${aluno.data_nascimento}</td>
                        <td>${aluno.telefone}</td>
                        <td>${aluno.email}</td>
                        <td>
                            <button class="btn btn-warning btn-sm editar" data-id="${aluno.id}">Editar</button>
                            <button class="btn btn-danger btn-sm excluir" data-id="${aluno.id}">Excluir</button>
                        </td>
                    </tr>`;
                });
                $("#listaAlunos").html(html);
            } else {
                console.warn("Nenhum aluno encontrado.");
                $("#listaAlunos").html("<tr><td colspan='5' class='text-center'>Nenhum aluno cadastrado.</td></tr>");
            }
        }, "json").fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro ao carregar alunos:", textStatus, errorThrown);
        });
    }

    $("#formAluno").submit(function (e) {
        e.preventDefault();
        $.post("/escola_biblica/controllers/alunos.php", $(this).serialize(), function (data) {
            console.log("Resposta do servidor:", data); // Debug

            if (data.status === "sucesso") {
                $("#modalAluno").modal("hide");
                carregarAlunos();
            } else {
                alert("Erro ao salvar: " + data.mensagem);
            }
        }, "json").fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição AJAX:", textStatus, errorThrown);
        });
    });

    $(document).on("click", ".editar", function () {
        let id = $(this).data("id");
        $.get('/escola_biblica/controllers/alunos.php', { id: id }, function (data) {
            console.log("Dados do aluno para edição:", data); // Debug

            if (data.status === "sucesso" && data.dados) {
                let aluno = data.dados;
                $("#id").val(aluno.id);
                $("#nome").val(aluno.nome);
                $("#data_nascimento").val(aluno.data_nascimento);
                $("#telefone").val(aluno.telefone);
                $("#email").val(aluno.email);
                $("#endereco").val(aluno.endereco);
                $("#acao").val("editar");
                $("#modalAluno").modal("show");
            } else {
                alert("Erro ao carregar dados do aluno.");
            }
        }, "json");
    });

    $(document).on("click", ".excluir", function () {
        let id = $(this).data("id");
        if (confirm("Tem certeza que deseja excluir este aluno?")) {
            $.post('/escola_biblica/controllers/alunos.php', { acao: 'excluir', id: id }, function (data) {
                console.log("Resposta ao excluir:", data); // Debug

                if (data.status === "sucesso") {
                    carregarAlunos();
                } else {
                    alert("Erro ao excluir aluno!");
                }
            }, "json");
        }
    });

    carregarAlunos();
});
