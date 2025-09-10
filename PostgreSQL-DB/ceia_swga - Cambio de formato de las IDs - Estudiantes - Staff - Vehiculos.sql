-- DROP CONSTRAINTS
ALTER TABLE estudiante_periodo DROP CONSTRAINT IF EXISTS fk_estudiante;
ALTER TABLE vehiculos DROP CONSTRAINT IF EXISTS fk_vehiculo_estudiante;
ALTER TABLE profesor_periodo DROP CONSTRAINT IF EXISTS fk_profesor;
ALTER TABLE entrada_salida_staff DROP CONSTRAINT IF EXISTS fk_staff_profesor;
ALTER TABLE registro_vehiculos DROP CONSTRAINT IF EXISTS fk_registro_vehiculo;
ALTER TABLE llegadas_tarde DROP CONSTRAINT IF EXISTS fk_llegada_estudiante;
ALTER TABLE latepass_resumen_semanal DROP CONSTRAINT IF EXISTS fk_latepass_estudiante;


--ESTUDIANTES
ALTER TABLE estudiante_periodo DROP CONSTRAINT IF EXISTS fk_estudiante;
ALTER TABLE estudiante_periodo ADD CONSTRAINT fk_estudiante FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id);
ALTER TABLE salud_estudiantil DROP CONSTRAINT salud_estudiantil_estudiante_id_fkey;

UPDATE estudiantes SET id = 20000 + id;
UPDATE salud_estudiantil SET estudiante_id = 20000 + estudiante_id;

ALTER TABLE salud_estudiantil ADD CONSTRAINT salud_estudiantil_estudiante_id_fkey FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id);


--PROFESORES
ALTER TABLE profesor_periodo DROP CONSTRAINT IF EXISTS profesor_periodo_profesor_id_fkey;
ALTER TABLE entrada_salida_staff DROP CONSTRAINT IF EXISTS entrada_salida_staff_profesor_id_fkey;
-- Agrega aquí cualquier otra tabla que tenga clave foránea a profesores(id)

ALTER TABLE usuarios ADD CONSTRAINT usuarios_profesor_id_fkey FOREIGN KEY (profesor_id) REFERENCES profesores(id);

ALTER TABLE usuarios DROP CONSTRAINT usuarios_profesor_id_fkey;

UPDATE profesores SET id = -80000 + id;
UPDATE profesor_periodo SET profesor_id = -80000 + profesor_id;
UPDATE entrada_salida_staff SET profesor_id = -80000 + profesor_id;
UPDATE usuarios SET profesor_id = -80000 + profesor_id;


ALTER TABLE profesor_periodo ADD CONSTRAINT profesor_periodo_profesor_id_fkey FOREIGN KEY (profesor_id) REFERENCES profesores(id);
ALTER TABLE entrada_salida_staff ADD CONSTRAINT entrada_salida_staff_profesor_id_fkey FOREIGN KEY (profesor_id) REFERENCES profesores(id);
-- Agrega aquí cualquier otra tabla que tenga clave foránea a profesores(id)



-- VEHICULOS
ALTER TABLE registro_vehiculos DROP CONSTRAINT IF EXISTS registro_vehiculos_vehiculo_id_fkey;
-- Agrega aquí cualquier otra tabla que tenga clave foránea a vehiculos(id)

UPDATE vehiculos SET id = 20000 + id;
UPDATE registro_vehiculos SET vehiculo_id = 20000 + vehiculo_id;
-- Agrega aquí cualquier otra tabla que tenga clave foránea a vehiculos(id)

ALTER TABLE registro_vehiculos ADD CONSTRAINT registro_vehiculos_vehiculo_id_fkey FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id);
-- Agrega aquí cualquier otra tabla que tenga clave foránea a vehiculos(id)


-- ADD CONSTRAINTS
ALTER TABLE estudiante_periodo ADD CONSTRAINT fk_estudiante FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id);
ALTER TABLE vehiculos ADD CONSTRAINT fk_vehiculo_estudiante FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id);
ALTER TABLE profesor_periodo ADD CONSTRAINT fk_profesor FOREIGN KEY (profesor_id) REFERENCES profesores(id);
ALTER TABLE entrada_salida_staff ADD CONSTRAINT fk_staff_profesor FOREIGN KEY (profesor_id) REFERENCES profesores(id);
ALTER TABLE registro_vehiculos ADD CONSTRAINT fk_registro_vehiculo FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id);
ALTER TABLE llegadas_tarde ADD CONSTRAINT fk_llegada_estudiante FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id);
ALTER TABLE latepass_resumen_semanal ADD CONSTRAINT fk_latepass_estudiante FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id);
