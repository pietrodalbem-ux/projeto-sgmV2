CREATE DATABASE IF NOT EXISTS sgm_db;
USE sgm_db;

-- =========================
-- TABELA: blocos
-- =========================

CREATE TABLE blocos (
    id_bloco INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(200) DEFAULT NULL,
    PRIMARY KEY (id_bloco)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO blocos (id_bloco, nome, descricao) VALUES
(1, 'Bloco Administrativo', NULL),
(2, 'Produção', NULL);

-- =========================
-- TABELA: ambientes
-- =========================

CREATE TABLE ambientes (
    id_ambiente INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    id_bloco INT(11) NOT NULL,
    PRIMARY KEY (id_ambiente),
    KEY fk_ambientes_blocos (id_bloco),
    CONSTRAINT fk_ambientes_blocos
        FOREIGN KEY (id_bloco)
        REFERENCES blocos(id_bloco)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ambientes (id_ambiente, nome, id_bloco) VALUES
(1, 'Recepção', 1),
(2, 'Copa', 1),
(3, 'Linha 1', 2);

-- =========================
-- TABELA: usuarios
-- =========================

CREATE TABLE usuarios (
    id_usuario INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('solicitante','tecnico','gestor') NOT NULL DEFAULT 'solicitante',
    ativo TINYINT(1) DEFAULT 1,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id_usuario),
    UNIQUE KEY idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios (id_usuario, nome, email, senha_hash, perfil, ativo, data_criacao) VALUES
(1, 'Admin Gestor', 'admin@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'gestor', 1, '2026-02-04 11:41:18'),

(2, 'João Técnico', 'tecnico@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'tecnico', 1, '2026-02-04 11:41:18'),

(3, 'Maria Solicitante', 'usuario@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'solicitante', 1, '2026-02-04 11:41:18'),

(4, 'Hugo', 'hugo@sgm.com', '$2y$10$OWj1KJGo9RJzCHaPsY40fOifXqDfA7pYlj/1nAe5b7bpQaKhPjxCK', 'gestor', 1, '2026-02-06 07:24:26');

-- =========================
-- TABELA: tipos_servico
-- =========================

CREATE TABLE tipos_servico (
    id_tipo INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    descricao VARCHAR(200) DEFAULT NULL,
    PRIMARY KEY (id_tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tipos_servico (id_tipo, nome, descricao) VALUES
(1, 'Elétrica', NULL),
(2, 'Hidráulica', NULL),
(3, 'Ar Condicionado', NULL),
(4, 'Civil/Predial', NULL);

-- =========================
-- TABELA: chamados
-- =========================

CREATE TABLE chamados (
    id_chamado INT(11) NOT NULL AUTO_INCREMENT,
    descricao_problema TEXT NOT NULL,
    data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP(),
    status ENUM(
        'aberto',
        'agendado',
        'em_execucao',
        'concluido',
        'fechado',
        'cancelado'
    ) DEFAULT 'aberto',

    prioridade ENUM(
        'baixa',
        'media',
        'alta',
        'urgente'
    ) DEFAULT 'baixa',

    data_previsao_conclusao DATE DEFAULT NULL,
    solucao_tecnica TEXT DEFAULT NULL,
    tempo_gasto_minutos INT(11) DEFAULT NULL,
    data_fechamento DATETIME DEFAULT NULL,

    id_solicitante INT(11) NOT NULL,
    id_tecnico INT(11) DEFAULT NULL,
    id_ambiente INT(11) NOT NULL,
    id_tipo_servico INT(11) NOT NULL,

    PRIMARY KEY (id_chamado),

    KEY fk_chamados_solicitante (id_solicitante),
    KEY fk_chamados_tecnico (id_tecnico),
    KEY fk_chamados_ambiente (id_ambiente),
    KEY fk_chamados_tipo (id_tipo_servico),

    CONSTRAINT fk_chamados_solicitante
        FOREIGN KEY (id_solicitante)
        REFERENCES usuarios(id_usuario),

    CONSTRAINT fk_chamados_tecnico
        FOREIGN KEY (id_tecnico)
        REFERENCES usuarios(id_usuario)
        ON DELETE SET NULL,

    CONSTRAINT fk_chamados_ambiente
        FOREIGN KEY (id_ambiente)
        REFERENCES ambientes(id_ambiente),

    CONSTRAINT fk_chamados_tipo
        FOREIGN KEY (id_tipo_servico)
        REFERENCES tipos_servico(id_tipo)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO chamados (
    id_chamado,
    descricao_problema,
    data_abertura,
    status,
    prioridade,
    data_previsao_conclusao,
    solucao_tecnica,
    tempo_gasto_minutos,
    data_fechamento,
    id_solicitante,
    id_tecnico,
    id_ambiente,
    id_tipo_servico
) VALUES (
    1,
    'caiu o telhado',
    '2026-02-11 09:15:53',
    'aberto',
    'baixa',
    NULL,
    NULL,
    NULL,
    NULL,
    4,
    NULL,
    3,
    2
);

-- =========================
-- TABELA: chamados_anexos
-- =========================

CREATE TABLE chamados_anexos (
    id_anexo INT(11) NOT NULL AUTO_INCREMENT,
    caminho_arquivo VARCHAR(255) NOT NULL,
    tipo_anexo ENUM('abertura','conclusao') NOT NULL,
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP(),
    id_chamado INT(11) NOT NULL,

    PRIMARY KEY (id_anexo),

    KEY fk_anexos_chamados (id_chamado),

    CONSTRAINT fk_anexos_chamados
        FOREIGN KEY (id_chamado)
        REFERENCES chamados(id_chamado)
        ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABELA: chamados_comentarios
-- =========================

CREATE TABLE chamados_comentarios (
    id_comentario INT(11) NOT NULL AUTO_INCREMENT,
    texto TEXT NOT NULL,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP(),
    id_chamado INT(11) NOT NULL,
    id_usuario INT(11) NOT NULL,

    PRIMARY KEY (id_comentario),

    KEY fk_comentarios_chamado (id_chamado),
    KEY fk_comentarios_usuario (id_usuario),

    CONSTRAINT fk_comentarios_chamado
        FOREIGN KEY (id_chamado)
        REFERENCES chamados(id_chamado)
        ON DELETE CASCADE,

    CONSTRAINT fk_comentarios_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;