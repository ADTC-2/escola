# Projeto de Sistema Escolar

Este projeto visa criar um sistema de gerenciamento escolar com autenticação, controle de presenças, CRUDs (para alunos, professores e classes), e um painel de controle (Dashboard). O sistema foi desenvolvido utilizando PHP, AJAX, PDO para conexão com o banco de dados, e é responsivo, utilizando Bootstrap, Font Awesome, CSS e JavaScript.

## Etapa 1: Configuração Inicial

### Banco de Dados

1. Defina a estrutura do banco de dados com as seguintes tabelas:
   - `alunos`: Tabela para armazenar informações dos alunos.
   - `professores`: Tabela para armazenar informações dos professores.
   - `classes`: Tabela para armazenar as informações das classes.
   - `presenca`: Tabela para registrar as presenças dos alunos em cada aula.

2. No arquivo `config/conecao.php`, configure a conexão PDO com o banco de dados.

### Funções Auxiliares

- Adicione funções úteis no arquivo `config/funcoes.php`, como:
  - Validação de login.
  - Funções de formatação de dados.
  - Outras funções auxiliares que facilitarão o trabalho no projeto.

## Etapa 2: Autenticação de Usuário

### Login e Logout

1. Crie o formulário de login em `views/auth/login.php`, com campos para usuário e senha.
2. Implemente a autenticação no arquivo `auth/validar_login.php`, verificando o login do usuário.
3. Crie o logout no arquivo `auth/logout.php`, para encerrar a sessão do usuário.

### Controle de Acesso

- Crie funções para verificar se o usuário está autenticado.
- Se não autenticado, redirecione para a página de login.
- Após o login bem-sucedido, redirecione o usuário para a dashboard ou página inicial.

## Etapa 3: CRUDs com AJAX e PHP

### Alunos

1. Crie as páginas `views/alunos/index.php`, `views/alunos/create.php`, `views/alunos/edit.php` para exibir e editar informações dos alunos.
2. No controlador `controllers/alunos.php`, implemente a lógica para gerenciar os dados dos alunos.
3. Utilize AJAX em `ajax/alunos.ajax.php` para realizar operações assíncronas (criar, editar, excluir alunos).

### Classes

1. Crie as páginas `views/classes/index.php`, `views/classes/create.php`, `views/classes/edit.php` para gerenciar as classes.
2. No controlador `controllers/classes.php`, adicione a lógica necessária para gerenciar as classes.
3. Utilize AJAX em `ajax/classes.ajax.php` para manipulação assíncrona das classes.

### Professores

1. Crie as páginas `views/professores/index.php`, `views/professores/create.php`, `views/professores/edit.php` para gerenciar os professores.
2. No controlador `controllers/professores.php`, adicione a lógica necessária para gerenciar os professores.
3. Utilize AJAX em `ajax/professores.ajax.php` para manipulação assíncrona dos professores.

## Etapa 4: Gerenciamento de Chamada

### Presenças

1. Crie a tela de chamadas em `views/chamada/index.php`, onde as presenças dos alunos serão registradas.
2. No controlador `controllers/chamada.php`, adicione a lógica para registrar as presenças.

## Etapa 5: Dashboard

1. Crie o painel de controle em `views/dashboard/index.php`, com dados resumidos sobre os alunos, professores e classes.
   
## Etapa 6: Finalização e Ajustes

### Responsividade e Estilo

- Garanta que o projeto seja responsivo utilizando o Bootstrap e personalizando com CSS conforme necessário.
- O projeto deve ser acessível em dispositivos móveis e desktops.

### Testes e Validação

- Teste todas as funcionalidades do sistema.
- Corrija bugs encontrados durante os testes.
- Valide entradas de dados para garantir a segurança e integridade do sistema.
  
## Estrutura de Diretórios

A estrutura de diretórios do projeto é a seguinte:

`
escola/ ├── ajax/ │ ├── alunos.ajax.php │ ├── classes.ajax.php │ └── professores.ajax.php ├── auth/ │ ├── login.php │ ├── logout.php │ └── validar_login.php ├── config/ │ ├── conecao.php (configuração PDO) │ └── funcoes.php (funções auxiliares) ├── controllers/ │ ├── alunos.php │ ├── classes.php │ └── professores.php ├── views/ │ ├── alunos/ │ │ ├── index.php │ │ ├── create.php │ │ └── edit.php │ ├── classes/ │ │ ├── index.php │ │ ├── create.php │ │ └── edit.php │ ├── professores/ │ │ ├── index.php │ │ ├── create.php │ │ └── edit.php │ ├── chamada/ │ │ └── index.php │ └── dashboard/ │ └── index.php ├── index.php (Raiz do projeto para redirecionamento) `


## Tecnologias Utilizadas

- **PHP**: Linguagem de programação para o backend.
- **AJAX**: Para comunicação assíncrona entre o frontend e o backend.
- **PDO**: Para conexão segura com o banco de dados.
- **Bootstrap**: Framework CSS para criar interfaces responsivas.
- **Font Awesome**: Para ícones.
- **CSS e JavaScript**: Para personalização e funcionalidades adicionais.

## Como Rodar o Projeto

1. Clone o repositório para sua máquina local.
2. Configure o banco de dados conforme especificado.
3. Configure a conexão PDO em `config/conecao.php`.
4. Acesse o projeto no seu servidor web.

## Contribuições

Contribuições são bem-vindas! Caso tenha sugestões ou melhorias, sinta-se à vontade para abrir um _pull request_.
# escola
# escola
