CREATE DATABASE escola;
USE escola;

-- Tabela de congregações
CREATE TABLE congregacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de alunos
CREATE TABLE alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(15),
    classe_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE CASCADE,
);

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('admin', 'user', 'professor') NOT NULL,
    congregacao_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (congregacao_id) REFERENCES congregacoes(id) ON DELETE SET NULL
);


-- Tabela de classes (turmas) - sem congregação diretamente associada
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de matrículas (vincula alunos às classes e congregações, com o usuário responsável pela matrícula)
CREATE TABLE matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    classe_id INT NOT NULL,
    congregacao_id INT NOT NULL, -- Congregação agora vinculada à matrícula
    usuario_id INT,  -- Novo campo para registrar o usuário responsável
    data_matricula DATE NOT NULL DEFAULT CURRENT_DATE,
    status ENUM('ativo', 'concluido', 'cancelado') NOT NULL DEFAULT 'ativo',
    trimestre INT NOT NULL CHECK (trimestre BETWEEN 1 AND 4), -- Campo trimestre
    UNIQUE (aluno_id, classe_id, status),
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (congregacao_id) REFERENCES congregacoes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,  -- Relacionamento com usuário
    INDEX idx_aluno (aluno_id),
    INDEX idx_classe (classe_id),
    INDEX idx_congregacao (congregacao_id),
    INDEX idx_usuario (usuario_id)  -- Índice para o relacionamento com usuário
);


-- Tabela de chamadas (registro de presença)
CREATE TABLE chamadas (
    id INT AUTO_INCREMENT PRIMARY KEY,              -- Identificador único da chamada
    data DATE NOT NULL,                             -- Data da chamada
    classe_id INT NOT NULL,                         -- Identificador da classe
    professor_id INT NOT NULL,                      -- Identificador do professor
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Data e hora de criação do registro
    oferta_classe VARCHAR(100),                     -- Oferta da classe (ex: nome do curso)
    FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE CASCADE,  -- Relacionamento com a tabela de classes
    FOREIGN KEY (professor_id) REFERENCES professores(id) ON DELETE CASCADE  -- Relacionamento com a tabela de professores
);

-- Tabela de presenças (registro de presença dos alunos)
CREATE TABLE presencas (
    id INT AUTO_INCREMENT PRIMARY KEY,             -- Identificador único da presença
    chamada_id INT NOT NULL,                        -- Identificador da chamada
    aluno_id INT NOT NULL,                          -- Identificador do aluno
    presente ENUM('presente', 'ausente', 'justificado') NOT NULL,  -- Status da presença
    FOREIGN KEY (chamada_id) REFERENCES chamadas(id) ON DELETE CASCADE,  -- Relacionamento com a tabela de chamadas
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE  -- Relacionamento com a tabela de alunos
);

-- Tabela de permissões
CREATE TABLE permissoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE COMMENT 'Ex: gerenciar_alunos'
);

-- Tabela de associação de usuários com permissões
CREATE TABLE usuario_permissoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    permissao_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (permissao_id) REFERENCES permissoes(id) ON DELETE CASCADE
);

-- Tabela de logs (registro de atividades no sistema)
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    acao VARCHAR(255) NOT NULL,
    tabela_afetada VARCHAR(100) NOT NULL,
    registro_id INT NOT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);