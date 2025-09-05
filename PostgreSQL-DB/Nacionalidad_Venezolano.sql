-- Conectar a la base de datos ceia_db antes de ejecutar.

-- Actualiza la tabla de estudiantes
UPDATE estudiantes
SET nacionalidad = 'Venezolano'
WHERE nacionalidad ILIKE 'venezolan%'; -- Busca cualquier texto que comience con 'venezolan' (ignora mayúsculas/minúsculas)

-- Actualiza la tabla de padres
UPDATE padres
SET padre_nacionalidad = 'Venezolano'
WHERE padre_nacionalidad ILIKE 'venezolan%';

-- Actualiza la tabla de madres
UPDATE madres
SET madre_nacionalidad = 'Venezolano'
WHERE madre_nacionalidad ILIKE 'venezolan%';