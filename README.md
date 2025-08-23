# Sistema Web de Gestión Académica de Inscripción y Late-Pass (SWGA) - CEIA

Desarrollado como Trabajo Especial de Grado para optar al título de Licenciado en Computación. Este sistema web ofrece una solución integral para automatizar y optimizar los procesos de inscripción y control de llegadas tarde (Late-Pass) en el Centro Educativo Internacional Anzoátegui (CEIA), solucionando las ineficiencias, problemas de seguridad y difícil acceso a la información asociados a los sistemas manuales.

**Autor:** Roger R. Carvajal

## ✨ Características Principales

El sistema está compuesto por una serie de módulos diseñados para cubrir todo el ciclo de gestión académica requerido por la institución, con las últimas actualizaciones y mejoras:

*   **Autenticación y Sistema de Roles Avanzado:**
    *   Sistema de `login` seguro para validar a los usuarios.
    *   Gestión basada en tres roles: **Superusuario (master)**, **Administrador (admin)** y **Consulta (consulta)**, permitiendo un control de acceso granular y específico según el tipo de personal registrado.

*   **Gestión y Clasificación por Categorías:**
    *   El sistema clasifica las entidades principales en tres categorías: **Estudiantes**, **Staff** (con sub-categorías para Administrativo, Docente, Mantenimiento y Vigilancia) y **Vehículos**.
    *   Este enfoque permite una organización superior y sienta las bases para futuros reportes y controles de acceso específicos.

*   **Módulo de Estudiantes:**
    *   Formulario de inscripción digital para registrar nuevos estudiantes.
    *   Lógica inteligente para buscar y vincular representantes (padres/madres) ya existentes, evitando la duplicidad de datos.
    *   Interfaz para consultar y modificar los expedientes completos de los estudiantes en tiempo real.
    *   Asignación de estudiantes a períodos escolares y grados.

*   **Módulo de Staff/Profesores:**
    *   Permite el registro del personal, asignándolo a su categoría y sub-categoría correspondiente.
    *   Interfaz para asignar a cada miembro del personal a un período escolar con un rol o posición específica.

*   **Módulo de Late-Pass con QR:**
    *   Generación de un **Código QR** único para cada estudiante, que sirve como su identificación para el control de acceso.
    *   Interfaz de **Control de Acceso Automatizado** que utiliza un lector de códigos QR para registrar las llegadas tarde de forma rápida y precisa.
    *   Sistema de conteo de **"strikes"** semanales por llegadas tarde, con mensajes de alerta configurables.

*   **Módulo de Reportes Ampliado:**
    *   Generación de reportes clave en formato **PDF**.
    *   Reporte **"Roster"** del período activo, listando personal y estudiantes por grado.
    *   Generación de la **"Planilla de Inscripción"** individual de cada estudiante.
    *   **Nuevas listas en PDF** para consultar de forma independiente a **Estudiantes**, **todo el Staff** (clasificado por su área) y **Vehículos registrados**.

*   **Módulo de Respaldo de Base de Datos (¡Nuevo!):**
    *   Permite a los usuarios con rol 'master' generar respaldos completos de la base de datos PostgreSQL (`ceia_db`) de forma manual.
    *   Ofrece un historial de respaldos con la opción de descargar cualquier archivo `.sql` generado.
    *   Incluye instrucciones detalladas para configurar respaldos automáticos diarios utilizando el Programador de Tareas de Windows.

*   **Módulo de Mantenimiento:**
    *   Panel de administrador para crear, activar y desactivar los períodos escolares (ej. "2024-2025", "2025-2026").
    *   Integración con el nuevo módulo de respaldo de base de datos.
    *   Diseñado para futuras expansiones, incluyendo restauración de respaldos, visor de logs y herramientas de optimización de base de datos.

*   **Manual de Usuario Integrado (¡Nuevo!):**
    *   Acceso directo a un manual de usuario básico desde la interfaz del sistema, proporcionando guía y ayuda a los colaboradores e interesados sobre el funcionamiento de las principales funcionalidades.

## 🛠️ Arquitectura y Tecnologías

El sistema fue construido siguiendo una arquitectura de tres capas y prácticas de seguridad modernas para garantizar su escalabilidad y mantenibilidad.

*   **Arquitectura:** Tres capas (Presentación, Lógica de Negocio, Datos) con un único punto de acceso (`/public`) para proteger el código fuente.
*   **Backend:** **PHP 8**. Se encarga de procesar todas las solicitudes, aplicar las reglas de negocio y comunicarse con la base de datos a través de una serie de APIs que responden en formato JSON.
*   **Frontend:** **HTML5, CSS3, y JavaScript (Vanilla JS)**. Se utiliza para crear interfaces dinámicas que se comunican con el backend sin necesidad de recargar la página, ofreciendo una experiencia de usuario fluida.
*   **Base de Datos:** **PostgreSQL** (Versión 14 o superior). Se eligió por ser un sistema gestor de bases de datos relacional robusto que garantiza la integridad y consistencia de la información.
*   **Librerías Externas:**
    *   **FPDF:** Para la generación de reportes en formato PDF.
    *   **PHP-QRCode:** Para la creación de los códigos QR de los estudiantes.
*   **Entorno de Desarrollo:** **XAMPP** con **Apache** como servidor web.

## 🚀 Guía de Instalación Local

Para ejecutar este proyecto en un entorno de desarrollo local, sigue estos pasos:

1.  **Software Necesario:**
    *   Instala **XAMPP** (con PHP 8.0+) desde [Apache Friends](https://www.apachefriends.org/index.html).
    *   Instala **PostgreSQL** (versión 14+) desde su [sitio web oficial](https://www.postgresql.org/download/). Recuerda guardar la contraseña que establezcas para el usuario `postgres`.

2.  **Clonar el Repositorio:**
    *   Navega hasta el directorio `htdocs` de tu instalación de XAMPP (ej. `C:\xampp\htdocs`).
    *   Clona este repositorio: `git clone https://github.com/rogerrcarvajal/ceia_swga.git`.

3.  **Configurar la Base de Datos:**
    *   Abre **pgAdmin** y conéctate a tu servidor de PostgreSQL.
    *   Crea una nueva base de datos con el nombre `ceia_db`.
    *   Haz clic derecho sobre la nueva base de datos y selecciona la opción **"Restore..."**.
    *   En `Filename`, busca y selecciona el archivo de la base de datos (`.sql` o `.backup`) que se encuentra en el repositorio para crear la estructura de tablas y cargar los datos iniciales.

4.  **Conectar la Aplicación:**
    *   Dentro del proyecto, navega a la carpeta `src/` y abre el archivo `config.php`.
    *   Modifica las credenciales (`$host`, `$port`, `$dbname`, `$user`, `$password`) para que coincidan con tu configuración local de PostgreSQL.

5.  **Ejecutar el Proyecto:**
    *   Inicia los servicios de **Apache** y **PostgreSQL**.
    *   Abre tu navegador web y ve a la siguiente URL: `http://localhost/ceia_swga/public`
    *   ¡Listo! Deberías ver la pantalla de login del sistema.

## 📄 Manual de Usuario Básico

1.  **Crear un Período Escolar:** Antes de cualquier otra operación, ve a `Mantenimiento -> Períodos Escolares` y asegúrate de que exista un período activo.
2.  **Inscribir un Estudiante:** Ve a `Estudiantes -> Planilla de Inscripción` y rellena todos los datos. El sistema te ayudará a vincular padres ya existentes para no duplicar información.
3.  **Registrar Personal:** En la sección `Staff`, puedes añadir a los profesores y personal administrativo, asignándoles su categoría correcta.
4.  **Generar QR:** En el módulo `Late-Pass`, selecciona un estudiante para generar su código QR en PDF, el cual puedes imprimir.
5.  **Control de Acceso:** Utiliza la opción `Control de Acceso` del módulo `Late-Pass` para escanear los QR y registrar las llegadas.
6.  **Obtener Listas:** En el menú `Reportes`, ahora puedes generar PDFs con las listas completas de Estudiantes, Staff o Vehículos.
7.  **Realizar Respaldo de Base de Datos:** Desde el módulo de `Mantenimiento`, selecciona la opción de respaldo para generar una copia de seguridad de la base de datos y acceder al historial de respaldos.