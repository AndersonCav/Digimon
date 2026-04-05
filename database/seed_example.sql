USE digimon;

INSERT INTO usuarios (username, email, senha)
VALUES ('demo', 'demo@example.com', '$2y$10$Qj0vTewMI0hSkG9NoF3MTOmVf2xVH4byM7WwG4JQ2uopH57Ye0fQ6');

INSERT INTO favoritos (user_id, digimon_name, digimon_image, digimon_level, digimon_attribute, digimon_href)
VALUES
(1, 'Agumon', 'https://digi-api.com/images/digimon/w/Agumon.png', 'Child', 'Vaccine', 'https://digi-api.com/api/v1/digimon/141'),
(1, 'Gabumon', 'https://digi-api.com/images/digimon/w/Gabumon.png', 'Child', 'Data', 'https://digi-api.com/api/v1/digimon/59');
