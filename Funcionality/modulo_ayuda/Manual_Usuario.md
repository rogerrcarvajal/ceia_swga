# Manual de Usuario del Sistema Web de Gestión Académica (SWGA)

## Introducción

Bienvenido al Manual de Usuario del SWGA. Este documento está diseñado para guiar a los administradores y usuarios del sistema a través de sus diversas funcionalidades, asegurando un manejo eficiente y correcto de la plataforma.

---

## Módulo de Control de Acceso

Este módulo es el punto de entrada al sistema y gestiona la seguridad.

*   **Login**: Los usuarios acceden utilizando su cédula y contraseña.
*   **Roles**: El sistema reconoce diferentes roles (ej. master, admin) que determinan el acceso a los distintos módulos.
*   **Dashboard**: Una vez dentro, el dashboard (`dashboard.php`) presenta el menú principal, que es la puerta de entrada a todas las demás secciones.

---

## Módulo de Estudiantes

Permite la gestión integral de la información de los estudiantes.

*   **Inscripción**: A través de `planilla_inscripcion.php`, se registran nuevos estudiantes junto con los datos de sus padres y su ficha médica.
*   **Gestión de Expedientes**: En `administrar_planilla_estudiantes.php`, se puede buscar, visualizar y **actualizar** la información de cualquier estudiante, incluyendo datos personales, de los padres y médicos, todo de forma asíncrona gracias a AJAX.
*   **Asignación a Períodos**: En `gestionar_estudiantes.php`, se vincula a los estudiantes con el período escolar activo y se les asigna un grado.

---

## Módulo de Staff

Administra al personal de la institución (docentes, administrativos, etc.).

*   **Registro**: `profesores_registro.php` permite crear nuevos perfiles para el personal.
*   **Gestión**: `gestionar_profesor.php` ofrece una lista del personal para editar su información o eliminar registros.
*   **Control de Entradas/Salidas**: `gestion_es_staff.php` registra los movimientos del personal mediante la lectura de su código QR o cédula, actualizando su estado (dentro o fuera del plantel).

---

## Módulo de Late-Pass (Pases de Llegada Tarde)

Diseñado para el control de retardos de los estudiantes.

*   **Generación de Pase**: En `gestion_latepass.php`, se busca al estudiante por su cédula, se registra el retardo en la base de datos y se genera un pase en formato PDF listo para imprimir.

---

## Módulo de Reportes

Centraliza la exportación de información clave del sistema.

*   **Generación de Reportes**: Desde `gestionar_reportes.php`, los administradores pueden generar una variedad de listados en PDF, tales como:
    *   Listas de estudiantes por grado.
    *   Listas de personal por departamento.
    *   Historial de movimientos de personal y vehículos.
    *   Planillas de inscripción individuales.
    *   Carnets con códigos QR para estudiantes, personal y vehículos.

---

## Módulo de Mantenimiento

Contiene herramientas críticas para la administración del sistema, accesibles solo para el usuario 'master'.

*   **Períodos Escolares**: `periodos_escolares.php` permite crear nuevos períodos y activar el que corresponda al ciclo académico actual.
*   **Gestión de Usuarios**: `configurar_usuarios.php` se utiliza para crear las cuentas de usuario que podrán acceder al sistema, vinculándolas a un miembro del staff.
*   **Respaldo de Base de Datos**: `backup_db.php` ofrece la funcionalidad para crear respaldos manuales de la base de datos y descargar respaldos existentes.