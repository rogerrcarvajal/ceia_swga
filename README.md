# Sistema Web de Gestión Académica de Inscripción y Late-Pass (SWGA) - CEIA

Desarrollado como Trabajo Especial de Grado para optar al título de Licenciado en Computación. Este sistema web ofrece una solución integral para automatizar y optimizar los procesos de inscripción y control de llegadas tarde (Late-Pass) en el Centro Educativo Internacional Anzoátegui (CEIA), solucionando las ineficiencias, problemas de seguridad y difícil acceso a la información asociados a los sistemas manuales.

**Autor:** Roger R. Carvajal

## ✨ Características Principales

El sistema está compuesto por una serie de módulos diseñados para cubrir todo el ciclo de gestión académica requerido por la institución:

* **Autenticación y Sistema de Roles Avanzado:**
    * Sistema de `login` seguro para validar a los usuarios.
    * Gestión basada en tres roles: **Superusuario (master)**, **Administrador (admin)** y **Consulta (consulta)**, permitiendo un control de acceso granular y específico según el tipo de personal registrado.

* **Gestión y Clasificación por Categorías:**
    * El sistema ahora clasifica las entidades principales en tres categorías: **Estudiantes**, **Staff** (con sub-categorías para Administrativo, Docente, Mantenimiento y Vigilancia) y **Vehículos**.
    * Este enfoque permite una organización superior y sienta las bases para futuros reportes y controles de acceso específicos.

* **Módulo de Estudiantes:**
    * Formulario de inscripción digital para registrar nuevos estudiantes.
    * Lógica inteligente para buscar y vincular representantes (padres/madres) ya existentes, evitando la duplicidad de datos.
    * Interfaz para consultar y modificar los expedientes completos de los estudiantes en tiempo real.

* **Módulo de Staff / Profesores:**
  * Registro de personal por categoría y subcategoría.
  * Asignación de cargos y períodos escolares.

* **Módulo Late-Pass (Control de Acceso con QR):**
  * Generación de **códigos QR únicos** para estudiantes, staff y vehículos.
  * Interfaz de control de acceso que valida y registra automáticamente llegadas mediante cámara o lector QR.
  * Registro en tiempo real con integración a APIs específicas:
    - `registrar_llegada.php` (estudiantes)
    - `registrar_movimiento_staff.php` (staff)
    - `registrar_movimiento_vehiculo.php` (vehículos)
  * Sistema de conteo de **“strikes” semanales** por llegadas tarde, con alertas en pantalla.

* **Módulo Vehículos:**
  * Registro y control de vehículos autorizados.
  * Generación de códigos QR en PDF para control de acceso vehicular.
  * Registro automatizado de movimientos en entradas/salidas.

* **Módulo Reportes Ampliado:**
  * Reportes en **PDF** de estudiantes, staff y vehículos.
  * Reporte “Roster” del período activo.
  * Listas independientes filtradas por entidad.
  * Estadísticas de movimientos y control de asistencia.

* **Módulo Mantenimiento:**
  * Administración de períodos escolares.
  * Activación y desactivación de períodos académicos.

* **Módulo de Ayuda (Nuevo):**
  * Documentación interna accesible desde el menú del sistema.
  * Archivos `.md` organizados en `/funcionality/modulo_ayuda/`, convertidos a HTML mediante `Parsedown.php`.
  * Incluye FAQs, guías de uso y documentación técnica del sistema.

## 🛠️ Arquitectura y Tecnologías

El sistema fue construido siguiendo una arquitectura de tres capas y prácticas de seguridad modernas para garantizar su escalabilidad y mantenibilidad.

* **Arquitectura:** Tres capas (Presentación, Lógica de Negocio, Datos) con un único punto de acceso (`/public`) para proteger el código fuente.
* **Backend:** **PHP 8**. Se encarga de procesar todas las solicitudes, aplicar las reglas de negocio y comunicarse con la base de datos a través de una serie de APIs que responden en formato JSON.
* **Frontend:** **HTML5, CSS3, y JavaScript (Vanilla JS)**. Se utiliza para crear interfaces dinámicas que se comunican con el backend sin necesidad de recargar la página, ofreciendo una experiencia de usuario fluida.
* **Base de Datos:** **PostgreSQL** (Versión 14 o superior). Se eligió por ser un sistema gestor de bases de datos relacional robusto que garantiza la integridad y consistencia de la información.
* **Librerías Externas:**
    * **FPDF:** Para la generación de reportes en formato PDF.
    * **PHP-QRCode:** Para la creación de los códigos QR de los estudiantes.
* **Entorno de Desarrollo:** **XAMPP** con **Apache** como servidor web.

## 🚀 Guía de Instalación Local

Para ejecutar este proyecto en un entorno de desarrollo local, sigue estos pasos:

1.  **Software Necesario:**
    * Instala **XAMPP** (con PHP 8.0+) desde [Apache Friends](https://www.apachefriends.org/index.html).
    * Instala **PostgreSQL** (versión 14+) desde su [sitio web oficial](https://www.postgresql.org/download/). Recuerda guardar la contraseña que establezcas para el usuario `postgres`.

2.  **Clonar el Repositorio:**
    * Navega hasta el directorio `htdocs` de tu instalación de XAMPP (ej. `C:\xampp\htdocs`).
    * Clona este repositorio: `git clone https://github.com/rogerrcarvajal/ceia_swga.git`.

3.  **Configurar la Base de Datos:**
    * Abre **pgAdmin** y conéctate a tu servidor de PostgreSQL.
    * Crea una nueva base de datos con el nombre `ceia_db`.
    * Haz clic derecho sobre la nueva base de datos y selecciona la opción **"Restore..."**.
    * En `Filename`, busca y selecciona el archivo de la base de datos (`.sql` o `.backup`) que se encuentra en el repositorio para crear la estructura de tablas y cargar los datos iniciales.

4.  **Conectar la Aplicación:**
    * Dentro del proyecto, navega a la carpeta `src/` y abre el archivo `config.php`.
    * Modifica las credenciales (`$host`, `$port`, `$dbname`, `$user`, `$password`) para que coincidan con tu configuración local de PostgreSQL.

5.  **Ejecutar el Proyecto:**
    * Inicia los servicios de **Apache** y **PostgreSQL**.
    * Abre tu navegador web y ve a la siguiente URL: `http://localhost/ceia_swga/public`
    * ¡Listo! Deberías ver la pantalla de login del sistema.

## 📄 Manual de Usuario Básico

1.  **Crear un Período Escolar:** Antes de cualquier otra operación, ve a `Mantenimiento -> Períodos Escolares` y asegúrate de que exista un período activo.
2.  **Inscribir un Estudiante:** Ve a `Estudiantes -> Planilla de Inscripción` y rellena todos los datos. El sistema te ayudará a vincular padres ya existentes para no duplicar información.
3.  **Registrar Personal:** En la sección `Staff`, puedes añadir a los profesores y personal administrativo, asignándoles su categoría correcta.
4.  **Generar QR:** En el módulo `Late-Pass`, selecciona un estudiante para generar su código QR en PDF, el cual puedes imprimir.
5.  **Control de Acceso:** Utiliza la opción `Control de Acceso` del módulo `Late-Pass` para escanear los QR y registrar las llegadas.
6.  **Obtener Listas:** En el menú `Reportes`, ahora puedes generar PDFs con las listas completas de Estudiantes, Staff o Vehículos.

## 📚 Documentación Técnica

La documentación modular del sistema se encuentra en la carpeta /funcionality/, organizada en:

/funcionality/
├─ modulo_estudiante/
├─ modulo_staff/
├─ modulo_late-pass/
├─ modulo_reportes/
├─ modulo_mantenimiento/
└─ modulo_ayuda/

Cada carpeta contiene archivos .md que describen la lógica de negocio y funcionalidad de los procesos.
Se visualizan en el sistema mediante view_document.php.

## ℹ Contacto

Autor: Roger R. Carvajal
📧 Correo: rogerrcarvajal@gmail.com
