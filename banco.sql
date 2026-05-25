CREATE DATABASE adotapet
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

use adotapet;
-- ==============================
-- TABELA: espécies
-- ==============================
CREATE TABLE especie (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- ==============================
-- TABELA: vacinas
-- ==============================
CREATE TABLE vacinas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    especie_id BIGINT UNSIGNED NOT NULL,

    CONSTRAINT fk_vacinas_especie
        FOREIGN KEY (especie_id) REFERENCES especie(id)
        ON UPDATE NO ACTION 
        ON DELETE NO ACTION
);
-- ==============================
-- TABELA: animais
-- ==============================
CREATE TABLE animais (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    sexo ENUM('macho', 'femea') NOT NULL,
    sobre TEXT NULL,
    idade INT,
    castracao bool NOT NULL,
    especie_id bigint unsigned not null,
    adotado bool not null,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

	CONSTRAINT fk_animais_especie
        FOREIGN KEY (especie_id) REFERENCES especie(id)
        ON UPDATE NO ACTION ON DELETE NO ACTION
);


SELECT * FROM users;
DELETE from animais where id = 1;
-- ==============================
-- TABELA: animal_vacinas
-- ==============================

CREATE TABLE animal_vacinas (
    animal_id BIGINT UNSIGNED NOT NULL,
    vacina_id BIGINT UNSIGNED NOT NULL,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    PRIMARY KEY (animal_id, vacina_id),

    CONSTRAINT fk_animal_vacinas_animal
        FOREIGN KEY (animal_id) REFERENCES animais(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_animal_vacinas_vacina
        FOREIGN KEY (vacina_id) REFERENCES vacinas(id)
        ON DELETE CASCADE
);

-- ==============================
-- TABELA: animal_fotos
-- ==============================

CREATE TABLE animal_fotos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    animal_id BIGINT UNSIGNED NOT NULL,
    caminho VARCHAR(500) NOT NULL,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_fotos_animal
        FOREIGN KEY (animal_id) REFERENCES animais(id)
        ON DELETE CASCADE
);
-- ==============================
-- TABELA: adocoes
-- ==============================
CREATE TABLE adocoes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    adotante_id BIGINT UNSIGNED NOT NULL,
    doador_id BIGINT UNSIGNED NOT NULL,
    
    animal_id BIGINT UNSIGNED NOT NULL,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    status ENUM('pendente','aprovada','recusada','cancelada'),
    mensagem TEXT,
	data_adocao DATETIME,

    CONSTRAINT fk_adocoes_adotante
        FOREIGN KEY (adotante_id) REFERENCES users(id)
        ON UPDATE NO ACTION ON DELETE NO ACTION,

    CONSTRAINT fk_adocoes_doador
        FOREIGN KEY (doador_id) REFERENCES users(id)
        ON UPDATE NO ACTION ON DELETE NO ACTION,

    CONSTRAINT fk_adocoes_animal
        FOREIGN KEY (animal_id) REFERENCES animais(id)
        ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE favoritos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    animal_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    -- Evita favoritos duplicados
    UNIQUE KEY unique_favorito (animal_id, user_id),

    -- Foreign Keys
    CONSTRAINT fk_favoritos_animal
        FOREIGN KEY (animal_id)
        REFERENCES animais(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_favoritos_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

-- ==============================
-- TABELA: conversas
-- ==============================

CREATE TABLE conversas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    animal_id BIGINT UNSIGNED NOT NULL,
    adotante_id BIGINT UNSIGNED NOT NULL,
    doador_id BIGINT UNSIGNED NOT NULL,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_conversas_animal
        FOREIGN KEY (animal_id) REFERENCES animais(id),

    CONSTRAINT fk_conversas_adotante
        FOREIGN KEY (adotante_id) REFERENCES users(id),

    CONSTRAINT fk_conversas_doador
        FOREIGN KEY (doador_id) REFERENCES users(id)
);

-- ==============================
-- TABELA: mensagens
-- ==============================

CREATE TABLE mensagens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    conversa_id BIGINT UNSIGNED NOT NULL,
    remetente_id BIGINT UNSIGNED NOT NULL,

    mensagem TEXT NOT NULL,
    lida BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    CONSTRAINT fk_mensagem_conversa
        FOREIGN KEY (conversa_id) REFERENCES conversas(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_mensagem_user
        FOREIGN KEY (remetente_id) REFERENCES users(id)
);
