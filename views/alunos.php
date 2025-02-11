<?php require_once '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Gerenciamento de Alunos</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAluno" onclick="abrirModal()">Adicionar Aluno</button>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Data de Nascimento</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="listaAlunos"></tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modalAluno" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Cadastrar Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAluno" onsubmit="return false;">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="acao" name="acao" value="cadastrar">
                    
                    <div class="mb-3">
                        <label>Nome:</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label>Data de Nascimento:</label>
                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
                    </div>
                    <div class="mb-3">
                        <label>Telefone:</label>
                        <input type="text" class="form-control" id="telefone" name="telefone">
                    </div>
                    <div class="mb-3">
                        <label>Email:</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label>Endereço:</label>
                        <input type="text" class="form-control" id="endereco" name="endereco">
                    </div>
                    
                    <button type="submit" class="btn btn-success">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/jquery-3.7.1.min.js"></script>  <!-- Primeiro -->
<script src="../assets/js/ajax.js"></script>       <!-- Depois -->

<script>
    // Função para listar alunos na tabela
    function listarAlunos() {
        fetch('../controller.php', {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            const alunos = data.dados;
            const listaAlunos = document.getElementById('listaAlunos');
            listaAlunos.innerHTML = '';
            
            alunos.forEach(aluno => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${aluno.nome}</td>
                    <td>${aluno.data_nascimento}</td>
                    <td>${aluno.telefone}</td>
                    <td>${aluno.email}</td>
                    <td>
                        <button class="btn btn-warning" onclick="editarAluno(${aluno.id})">Editar</button>
                        <button class="btn btn-danger" onclick="excluirAluno(${aluno.id})">Excluir</button>
                    </td>
                `;
                listaAlunos.appendChild(tr);
            });
        })
        .catch(error => alert('Erro ao carregar os alunos'));
    }

    // Função para abrir o modal para cadastro
    function abrirModal() {
        document.getElementById('formAluno').reset();
        document.getElementById('acao').value = 'cadastrar';
        document.getElementById('modalTitle').innerText = 'Cadastrar Aluno';
    }

    // Função para editar aluno
    function editarAluno(id) {
        fetch(`../controller.php?id=${id}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            const aluno = data.dados;
            document.getElementById('id').value = aluno.id;
            document.getElementById('nome').value = aluno.nome;
            document.getElementById('data_nascimento').value = aluno.data_nascimento;
            document.getElementById('telefone').value = aluno.telefone;
            document.getElementById('email').value = aluno.email;
            document.getElementById('endereco').value = aluno.endereco;
            document.getElementById('acao').value = 'editar';
            document.getElementById('modalTitle').innerText = 'Editar Aluno';
            $('#modalAluno').modal('show');
        })
        .catch(error => alert('Erro ao carregar os dados do aluno'));
    }

    // Função para excluir aluno
    function excluirAluno(id) {
        if (confirm('Tem certeza que deseja excluir este aluno?')) {
            fetch('../controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `acao=excluir&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'sucesso') {
                    alert(data.mensagem);
                    listarAlunos();
                } else {
                    alert(data.mensagem);
                }
            })
            .catch(error => alert('Erro ao excluir o aluno'));
        }
    }

    // Função para salvar aluno (cadastrar ou editar)
    document.getElementById('formAluno').addEventListener('submit', function () {
        const formData = new FormData(this);

        fetch('../controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'sucesso') {
                alert(data.mensagem);
                $('#modalAluno').modal('hide');
                listarAlunos();
            } else {
                alert(data.mensagem);
            }
        })
        .catch(error => alert('Erro ao salvar o aluno'));
    });

    // Carregar lista de alunos ao carregar a página
    window.onload = listarAlunos;
</script>

<?php require_once '../includes/footer.php'; ?>

