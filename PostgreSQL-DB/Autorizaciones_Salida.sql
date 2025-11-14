CREATE TABLE autorizaciones_salida (
    id SERIAL PRIMARY KEY,
    estudiante_id INT NOT NULL,
    fecha_salida DATE NOT NULL,
    hora_salida TIME NOT NULL,
    retirado_por_nombre VARCHAR(255) NOT NULL,
    retirado_por_parentesco VARCHAR(100),
    motivo TEXT,
    registrado_por_usuario_id INT NOT NULL,
    fecha_registro TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes (id),
    FOREIGN KEY (registrado_por_usuario_id) REFERENCES usuarios (id)
);

COMMENT ON TABLE autorizaciones_salida IS 'Almacena las autorizaciones de salida para los estudiantes.';
COMMENT ON COLUMN autorizaciones_salida.id IS 'Identificador único de la autorización de salida.';
COMMENT ON COLUMN autorizaciones_salida.estudiante_id IS 'ID del estudiante que es retirado (FK a estudiantes.id).';
COMMENT ON COLUMN autorizaciones_salida.fecha_salida IS 'Fecha en que se efectúa la salida del estudiante.';
COMMENT ON COLUMN autorizaciones_salida.hora_salida IS 'Hora en que se efectúa la salida del estudiante.';
COMMENT ON COLUMN autorizaciones_salida.retirado_por_nombre IS 'Nombre completo de la persona que retira al estudiante.';
COMMENT ON COLUMN autorizaciones_salida.retirado_por_parentesco IS 'Parentesco de la persona que retira con el estudiante (Padre, Madre, Tío, etc.).';
COMMENT ON COLUMN autorizaciones_salida.motivo IS 'Motivo o razón de la salida anticipada del estudiante.';
COMMENT ON COLUMN autorizaciones_salida.registrado_por_usuario_id IS 'ID del usuario del sistema que registra la autorización (FK a usuarios.id).';
COMMENT ON COLUMN autorizaciones_salida.fecha_registro IS 'Fecha y hora en que se crea el registro de la autorización.';
