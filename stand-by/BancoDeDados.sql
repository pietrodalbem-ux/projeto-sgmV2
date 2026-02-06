-- ==========================================================
-- SCRIPT DE CRIAÇÃO - SISTEMA DE GESTÃO DE MANUTENÇÃO (SGM)
-- Escopo: Gestão de Chamados por Ambiente (Sem Ativos/Custos)
-- Banco de Dados: MySQL / MariaDB (Versão Produção)
-- ==========================================================

-- Configurações de Sessão para evitar erros durante a criação
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ----------------------------------------------------------
-- 0. CRIAÇÃO DO BANCO DE DADOS
-- ----------------------------------------------------------
CREATE DATABASE IF NOT EXISTS sgm_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sgm_db;

-- ----------------------------------------------------------
-- 1. TABELA DE USUÁRIOS (Atores do Sistema)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('solicitante', 'tecnico', 'gestor') NOT NULL DEFAULT 'solicitante',
    ativo TINYINT(1) DEFAULT 1,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario),
    UNIQUE INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 2. ESTRUTURA DE LOCALIZAÇÃO (Unidade -> Bloco -> Ambiente)
-- Nota: Unidades removidas do escopo (Single Site)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS blocos (
    id_bloco INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL, -- Ex: Bloco A, Administrativo
    descricao VARCHAR(200),
    PRIMARY KEY (id_bloco)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ambientes (
    id_ambiente INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL, -- Ex: Sala 101, Copa
    id_bloco INT NOT NULL,
    PRIMARY KEY (id_ambiente),
    CONSTRAINT fk_ambientes_blocos
        FOREIGN KEY (id_bloco)
        REFERENCES blocos (id_bloco)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 3. TIPOS DE SERVIÇO
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS tipos_servico (
    id_tipo INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    descricao VARCHAR(200),
    PRIMARY KEY (id_tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 4. TABELA PRINCIPAL: CHAMADOS (Ordens de Serviço)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS chamados (
    id_chamado INT NOT NULL AUTO_INCREMENT,
    
    -- Dados da Abertura
    descricao_problema TEXT NOT NULL,
    data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('aberto', 'agendado', 'em_execucao', 'concluido', 'fechado', 'cancelado') DEFAULT 'aberto',
    
    -- Classificação
    prioridade ENUM('baixa', 'media', 'alta', 'urgente') DEFAULT 'baixa',
    data_previsao_conclusao DATE DEFAULT NULL,
    
    -- Dados do Fechamento
    solucao_tecnica TEXT,
    tempo_gasto_minutos INT DEFAULT NULL,
    data_fechamento DATETIME DEFAULT NULL,
    
    -- Chaves Estrangeiras
    id_solicitante INT NOT NULL,
    id_tecnico INT DEFAULT NULL,
    id_ambiente INT NOT NULL,
    id_tipo_servico INT NOT NULL,
    
    PRIMARY KEY (id_chamado),
    
    CONSTRAINT fk_chamados_solicitante
        FOREIGN KEY (id_solicitante)
        REFERENCES usuarios (id_usuario),
    
    CONSTRAINT fk_chamados_tecnico
        FOREIGN KEY (id_tecnico)
        REFERENCES usuarios (id_usuario)
        ON DELETE SET NULL, -- Se apagar técnico, chamado fica órfão mas existe
        
    CONSTRAINT fk_chamados_ambiente
        FOREIGN KEY (id_ambiente)
        REFERENCES ambientes (id_ambiente),
        
    CONSTRAINT fk_chamados_tipo
        FOREIGN KEY (id_tipo_servico)
        REFERENCES tipos_servico (id_tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 5. ANEXOS / EVIDÊNCIAS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS chamados_anexos (
    id_anexo INT NOT NULL AUTO_INCREMENT,
    caminho_arquivo VARCHAR(255) NOT NULL,
    tipo_anexo ENUM('abertura', 'conclusao') NOT NULL,
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_chamado INT NOT NULL,
    
    PRIMARY KEY (id_anexo),
    CONSTRAINT fk_anexos_chamados
        FOREIGN KEY (id_chamado)
        REFERENCES chamados (id_chamado)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 6. HISTÓRICO DE COMENTÁRIOS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS chamados_comentarios (
    id_comentario INT NOT NULL AUTO_INCREMENT,
    texto TEXT NOT NULL,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_chamado INT NOT NULL,
    id_usuario INT NOT NULL,
    
    PRIMARY KEY (id_comentario),
    
    CONSTRAINT fk_comentarios_chamado
        FOREIGN KEY (id_chamado)
        REFERENCES chamados (id_chamado)
        ON DELETE CASCADE,
        
    CONSTRAINT fk_comentarios_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- DADOS INICIAIS (SEED) - Opcional para popular a base
-- ==========================================================

-- Inserir Usuários Padrão (Senha '123456' hash exemplo)
INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES 
('Admin Gestor', 'admin@sgm.com', '$2y$10$abcdefgh...', 'gestor'),
('João Técnico', 'tecnico@sgm.com', '$2y$10$abcdefgh...', 'tecnico'),
('Maria Solicitante', 'usuario@sgm.com', '$2y$10$abcdefgh...', 'solicitante');

-- Inserir Tipos de Serviço Básicos
INSERT INTO tipos_servico (nome) VALUES 
('Elétrica'), ('Hidráulica'), ('Ar Condicionado'), ('Civil/Predial');

-- Inserir Blocos e Ambientes Exemplo
INSERT INTO blocos (nome) VALUES ('Bloco Administrativo'), ('Produção');
INSERT INTO ambientes (nome, id_bloco) VALUES ('Recepção', 1), ('Copa', 1), ('Linha 1', 2);

-- Restaurar configurações de sessão
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;