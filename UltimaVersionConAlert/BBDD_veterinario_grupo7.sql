DROP DATABASE IF EXISTS bd_veterinaria;

CREATE DATABASE IF NOT EXISTS bd_veterinaria;
USE bd_veterinaria;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30)  NOT NULL,
    email VARCHAR(40)  NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE = INNODB;

CREATE TABLE razas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    peso DECIMAL(5,2)  NOT NULL,
    altura DECIMAL(5,2)  NOT NULL,
    temperamento VARCHAR(100)
) ENGINE = INNODB;

CREATE TABLE veterinarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    telefono CHAR(9) NOT NULL UNIQUE,
    especialidad VARCHAR(50) NOT NULL,
    salario DECIMAL(8,2)  NOT NULL
) ENGINE = INNODB;

CREATE TABLE propietarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(50)  NOT NULL UNIQUE,
    telefono CHAR(9) NOT NULL UNIQUE,
    direccion VARCHAR(100),
    mascota_chip CHAR(15)
) ENGINE = INNODB;

CREATE TABLE mascotas (
    chip  CHAR(15) NOT NULL PRIMARY KEY UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    sexo ENUM('M','F') NOT NULL,
    especie VARCHAR(30) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    raza_id INT NOT NULL,
    propietario_id INT NOT NULL,
    veterinario_id INT NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE = INNODB;

ALTER TABLE mascotas
ADD CONSTRAINT fk_mascotas_raza FOREIGN KEY (raza_id)
REFERENCES razas(id);

ALTER TABLE mascotas
ADD CONSTRAINT fk_mascotas_propietario FOREIGN KEY (propietario_id)
REFERENCES propietarios(id);

ALTER TABLE mascotas
ADD CONSTRAINT fk_mascotas_veterinario FOREIGN KEY (veterinario_id)
REFERENCES veterinarios(id);

ALTER TABLE propietarios
ADD CONSTRAINT fk_propietarios_mascota FOREIGN KEY (mascota_chip)
REFERENCES mascotas(chip);