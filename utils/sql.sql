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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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

-- Tabela de professores
CREATE TABLE professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    congregacao_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (congregacao_id) REFERENCES congregacoes(id) ON DELETE SET NULL
);

-- Tabela de classes (turmas)
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    congregacao_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (congregacao_id) REFERENCES congregacoes(id) ON DELETE CASCADE
);

-- Tabela de matrículas (vincula alunos às classes)
CREATE TABLE matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    classe_id INT NOT NULL,
    data_matricula DATE NOT NULL DEFAULT CURRENT_DATE,
    status ENUM('ativo', 'concluido', 'cancelado') NOT NULL DEFAULT 'ativo',
    trimestre INT NOT NULL CHECK (trimestre BETWEEN 1 AND 4), -- Campo trimestre
    UNIQUE (aluno_id, classe_id, status),
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE CASCADE,
    INDEX idx_aluno (aluno_id),
    INDEX idx_classe (classe_id)
); 

-- Tabela de chamadas (registro de presença)
CREATE TABLE chamadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATE NOT NULL,
    classe_id INT NOT NULL,
    professor_id INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (professor_id) REFERENCES professores(id) ON DELETE CASCADE
);

-- Tabela de chamada de alunos (presença)
CREATE TABLE chamada_alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chamada_id INT NOT NULL,
    aluno_id INT NOT NULL,
    presente BOOLEAN NOT NULL,
    FOREIGN KEY (chamada_id) REFERENCES chamadas(id) ON DELETE CASCADE,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE
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