-- Migrações para CRUD completo do SGM
USE sgm_db;

-- Tabela de notificações (usada pelo sistema)
CREATE TABLE IF NOT EXISTS notificacoes (
    id_notificacao INT(11) NOT NULL AUTO_INCREMENT,
    id_usuario INT(11) NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    mensagem TEXT NOT NULL,
    link VARCHAR(255) DEFAULT NULL,
    lida TINYINT(1) DEFAULT 0,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id_notificacao),
    KEY fk_notif_usuario (id_usuario),
    CONSTRAINT fk_notif_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Anexo opcional em comentários técnicos (ignore erro se coluna já existir)
ALTER TABLE chamados_comentarios
    ADD COLUMN caminho_arquivo VARCHAR(255) DEFAULT NULL;
