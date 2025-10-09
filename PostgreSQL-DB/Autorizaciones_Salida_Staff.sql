-- Tabla para almacenar las autorizaciones de salida del personal (staff)

CREATE TABLE autorizaciones_salida_staff (
    id SERIAL PRIMARY KEY,
    profesor_id INT NOT NULL,
    periodo_id INT NOT NULL,
    fecha_solicitud TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    fecha_permiso DATE NOT NULL,
    hora_salida TIME NOT NULL,
    duracion_horas NUMERIC(4, 2) NOT NULL, -- Permite decimales, ej: 1.5 horas
    motivo TEXT,
    estado VARCHAR(20) DEFAULT 'Solicitado', -- Ej: Solicitado, Aprobado, Rechazado
    registrado_por_usuario_id INT NOT NULL,
    
    -- Campos para futuras aprobaciones
    aprobado_por_supervisor_id INT,
    aprobado_por_admin_id INT,
    
    -- Foreign Keys
    CONSTRAINT fk_staff
        FOREIGN KEY(profesor_id)
        REFERENCES profesores(id)
        ON DELETE CASCADE,
        
    CONSTRAINT fk_periodo
        FOREIGN KEY(periodo_id)
        REFERENCES periodos_escolares(id),
        
    CONSTRAINT fk_registrado_por
        FOREIGN KEY(registrado_por_usuario_id)
        REFERENCES usuarios(id),

    CONSTRAINT fk_aprobado_supervisor
        FOREIGN KEY(aprobado_por_supervisor_id)
        REFERENCES usuarios(id),

    CONSTRAINT fk_aprobado_admin
        FOREIGN KEY(aprobado_por_admin_id)
        REFERENCES usuarios(id)
);

COMMENT ON TABLE autorizaciones_salida_staff IS 'Almacena los registros de permisos y autorizaciones de salida para el personal del colegio.';
COMMENT ON COLUMN autorizaciones_salida_staff.profesor_id IS 'ID del miembro del personal (de la tabla profesores).';
COMMENT ON COLUMN autorizaciones_salida_staff.periodo_id IS 'ID del período escolar en el que se solicita el permiso.';
COMMENT ON COLUMN autorizaciones_salida_staff.fecha_solicitud IS 'Fecha y hora en que se crea el registro de la solicitud.';
COMMENT ON COLUMN autorizaciones_salida_staff.fecha_permiso IS 'Fecha para la cual se solicita el permiso.';
COMMENT ON COLUMN autorizaciones_salida_staff.hora_salida IS 'Hora a partir de la cual el permiso es efectivo.';
COMMENT ON COLUMN autorizaciones_salida_staff.duracion_horas IS 'La cantidad de horas del permiso.';
COMMENT ON COLUMN autorizaciones_salida_staff.motivo IS 'Descripción del motivo del permiso.';
COMMENT ON COLUMN autorizaciones_salida_staff.estado IS 'El estado actual de la solicitud (Solicitado, Aprobado, Rechazado).';
COMMENT ON COLUMN autorizaciones_salida_staff.registrado_por_usuario_id IS 'ID del usuario que registra la solicitud en el sistema.';