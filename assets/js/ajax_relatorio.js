$(document).ready(function () {
    $("#formRelatorio").submit(function (e) {
        e.preventDefault();
        $.post("../controllers/relatorios.php", $(this).serialize(), function (data) {
            let relatorio = JSON.parse(data);
            let html = "";
            let htmlAssiduos = "";
            let htmlFaltosos = "";

            relatorio.forEach(aluno => {
                html += `<tr>
                    <td>${aluno.nome}</td>
                    <td>${aluno.presencas}</td>
                    <td>${aluno.faltas}</td>
                </tr>`;
            });

            // Top 5 mais assíduos
            relatorio.sort((a, b) => b.presencas - a.presencas);
            for (let i = 0; i < 5 && i < relatorio.length; i++) {
                htmlAssiduos += `<tr>
                    <td>${relatorio[i].nome}</td>
                    <td>${relatorio[i].presencas}</td>
                </tr>`;
            }

            // Top 5 mais faltosos
            relatorio.sort((a, b) => b.faltas - a.faltas);
            for (let i = 0; i < 5 && i < relatorio.length; i++) {
                htmlFaltosos += `<tr>
                    <td>${relatorio[i].nome}</td>
                    <td>${relatorio[i].faltas}</td>
                </tr>`;
            }

            $("#listaRelatorio").html(html);
            $("#listaMaisAssiduos").html(htmlAssiduos);
            $("#listaMaisFaltosos").html(htmlFaltosos);
        });
    });
});
