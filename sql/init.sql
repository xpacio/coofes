-- psql -U postgres -f sql/init.sql

CREATE DATABASE dbf_manager;

\c dbf_manager;

CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nickname VARCHAR(100) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    es_admin BOOLEAN DEFAULT FALSE
);

CREATE TABLE logs_carga (
    id SERIAL PRIMARY KEY,
    nickname_usuario VARCHAR(100) NOT NULL,
    fecha_hora TIMESTAMP DEFAULT NOW(),
    peso_bytes INTEGER,
    hash_md5 VARCHAR(32),
    fecha_archivo TIMESTAMP,
    ruta_original VARCHAR(500),
    ip_origen VARCHAR(45),
    user_agent TEXT,
    idioma VARCHAR(100),
    estado VARCHAR(50) NOT NULL,
    detalle TEXT,
    plaza_nombre VARCHAR(100)
);

CREATE TABLE rutas (
    id SERIAL PRIMARY KEY,
    ruta VARCHAR(500) NOT NULL UNIQUE,
    plaza VARCHAR(100) NOT NULL,
    habilitado BOOLEAN NOT NULL DEFAULT TRUE
);

-- Admin user: admin / admin123
-- Generate hash: php tools/generate_hash.php
INSERT INTO usuarios (nickname, password, nombre, es_admin)
VALUES ('admin', '$2y$10$0WHhoSSlPjKDqJwY/FOeo.M1jKj0Qe5v8ExF76f6Pba7u3uDmB2Nq', 'Administrador', TRUE);
