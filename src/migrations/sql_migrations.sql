CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nivel_permissao TINYINT NOT NULL DEFAULT 1,
    email VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO usuarios (nome, senha, nivel_permissao, email) VALUES
('Jackson Neves', '$2y$10$6MT1HRyDtDHVuuryi4n2LeBjd6gdAkF3AGq5XVo/0iUejdEvrZ8Uu', 1, 'jackson@example.com'),
('Maria Silva', '$2y$10$abcdefghijklmnopqrstuv', 2, 'maria@example.com'),
('Carlos Souza', '$2y$10$abcdefghijklmnopqrstuv', 1, 'carlos@example.com'),
('Ana Paula', '$2y$10$abcdefghijklmnopqrstuv', 3, 'ana@example.com'),
('Pedro Lima', '$2y$10$abcdefghijklmnopqrstuv', 2, 'pedro@example.com');
