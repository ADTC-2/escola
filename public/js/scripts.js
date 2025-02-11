$(document).ready(function() {
    $("#classe").change(function() {
        let classe_id = $(this).val();
        if (classe_id) {
            $.get("../controllers/alunos.php", { classe_id }, function(data) {
                let alunos = JSON.parse(data);
                let html = "<table class='table table-bordered'><thead><tr><th>Aluno</th><th>Presença</th></tr></thead><tbody>";
                alunos.forEach(aluno => {
                    html += `<tr>
                        <td>${aluno.nome}</td>
                        <td>
                            <select class='form-select presenca' data-id='${aluno.id}'>
                                <option value='P'>Presente</option>
                                <option value='F'>Faltou</option>
                            </select>
                        </td>
                    </tr>`;
                });
                html += "</tbody></table>";
                $("#lista-alunos").html(html);
            });
        } else {
            $("#lista-alunos").html("");
        }
    });

    $("#salvarChamada").click(function() {
        let data = $("#data").val();
        let presencas = {};

        $(".presenca").each(function() {
            let aluno_id = $(this).data("id");
            let status = $(this).val();
            presencas[aluno_id] = status;
        });

        $.post("../controllers/chamada.php", JSON.stringify({ data, presencas }), function() {
            alert("Chamada salva com sucesso!");
            location.reload();
        });
    });
});

