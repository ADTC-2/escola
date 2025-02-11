$(document).ready(function () {
    function carregarChamadas() {
        $.get("../controllers/chamada.php", function (data) {
            let chamadas = JSON.parse(data);
            let html = "";
            chamadas.forEach(chamada => {
                html += `<tr>
                    <td>${chamada.data}</td>
                    <td>${chamada.classe}</td>
                    <td>${chamada.professor}</td>
                    <td>
                        <button class="btn btn-success btn-sm ver-presenca" data-id="${chamada.id}">Ver Presença</button>
                    </td>
                </tr>`;
            });
            $("#listaChamadas").html(html);
        });
    }

    $("#formChamada").submit(function (e) {
        e.preventDefault();
        $.post("../controllers/chamada.php", $(this).serialize() + "&acao=iniciar", function (data) {
            let resposta = JSON.parse(data);
            if (resposta.status === "sucesso") {
                alert("Chamada iniciada com sucesso!");
                carregarChamadas();
            } else {
                alert("Erro ao iniciar chamada!");
            }
        });
    });

    carregarChamadas();
});
