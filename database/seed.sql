-- Inserindo usuários (Admin e Professores)
INSERT INTO usuarios (nome, email, senha, perfil) VALUES
('Admin', 'admin@igreja.com', '$2y$10$EiPtvMZY4uVh9Tbtdq2tBuIQj9UeZT2R8wGejYUpFJ7U8ZQgsxFfG', 'admin'),
('Pr. João', 'joao@igreja.com', '$2y$10$Wvay8wbS1NRkAq9Fkc3EnQpdY.O4FOhH9F2kXWoTfmcQj8A9sWRhO', 'professor'),
('Pr. Maria', 'maria@igreja.com', '$2y$10$4dfkX.5wEvKD9oN9bdsH2O7paA0bd1IT4dftLSz58N1NYUBG2RxAW', 'professor');
-- Inserindo professores (associando ao usuário)
INSERT INTO professores (usuario_id, data_contratacao, especialidade) VALUES
(2, '2015-03-01', 'Teologia Infantil'),
(3, '2018-07-15', 'Teologia Juvenil');

-- Inserindo classes
INSERT INTO classes (nome, professor_id) VALUES
('Infantil', 1),
('Juvenil', 2),
('Adultos', NULL);

-- Inserindo alunos
INSERT INTO alunos (nome, data_nascimento, classe_id, status) VALUES
('Carlos Silva', '2010-05-14', 1, 'ativo'),
('Ana Souza', '2008-09-21', 2, 'ativo'),
('Pedro Lima', '1985-12-03', 3, 'ativo');

-- Inserindo registros de chamadas
INSERT INTO chamadas (aluno_id, classe_id, professor_id, data_chamada, status, semestre, observacao) VALUES
(1, 1, 1, '2025-02-04', 'presente', '1', 'Chegou no horário'),
(2, 2, 2, '2025-02-04', 'falta', '1', 'Faltou sem justificar'),
(3, 3, NULL, '2025-02-04', 'presente', '1', NULL);

