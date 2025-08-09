# Sistema Web para la Gestión Académica de Inscripción y Late-Pass (CEIA)

[cite_start]Este proyecto es un sistema web integral desarrollado como requisito para optar al título de Licenciado en Computación[cite: 7, 8]. [cite_start]Su objetivo principal es automatizar y optimizar los procesos de inscripción y gestión de pases de llegada tarde (Late-Pass) en el Centro Educativo Internacional Anzoátegui (CEIA) [cite: 37][cite_start], solucionando las ineficiencias, problemas de seguridad y difícil acceso a la información asociados a los sistemas manuales[cite: 36].

[cite_start]**Autor:** Roger R. Carvajal [cite: 9]

## ✨ Características Principales

El sistema está compuesto por una serie de módulos diseñados para cubrir todo el ciclo de gestión académica requerido por la institución:

* **Módulo de Autenticación y Seguridad:**
    * [cite_start]Sistema de `login` seguro para validar a los usuarios[cite: 277].
    * [cite_start]Gestión basada en dos roles: **"Administrador"** (acceso total) y **"Consulta"** (acceso limitado a reportes y al módulo Late-Pass)[cite: 278].
    * [cite_start]Permite la creación y gestión de cuentas de usuario vinculadas al personal del colegio[cite: 377].

* **Módulo de Estudiantes:**
    * [cite_start]Formulario de inscripción digital para registrar nuevos estudiantes[cite: 280].
    * [cite_start]Lógica inteligente para buscar y vincular representantes (padres/madres) ya existentes, evitando la duplicidad de datos[cite: 281, 345].
    * [cite_start]Interfaz para consultar y modificar los expedientes completos de los estudiantes en tiempo real (datos personales, de padres y ficha médica)[cite: 282, 347].
    * [cite_start]Funcionalidad para asignar estudiantes al período escolar activo y al grado que cursarán[cite: 349].

* **Módulo de Staff/Profesores:**
    * [cite_start]Permite el registro del personal docente y administrativo de la institución[cite: 283].
    * [cite_start]Interfaz para asignar a cada miembro del personal a un período escolar con un rol o posición específica (ej. "Director", "Science Teacher")[cite: 283, 355].

* **Módulo de Late-Pass:**
    * [cite_start]Generación de un **Código QR** único para cada estudiante, que sirve como su identificación para el control de acceso[cite: 284, 360].
    * [cite_start]Interfaz de **Control de Acceso Automatizado** que utiliza un lector de códigos QR para registrar la llegada de los estudiantes de forma rápida y precisa[cite: 285, 362].
    * [cite_start]Sistema de conteo de **"strikes"** semanales por llegadas tarde, con mensajes de alerta configurables[cite: 286, 444, 445, 446, 447].
    * [cite_start]Panel para la gestión y consulta del historial de llegadas tarde, filtrado por semana y por grado[cite: 364].

* **Módulo de Reportes:**
    * [cite_start]Generación de reportes clave en formato **PDF**[cite: 287].
    * [cite_start]Reporte **"Roster"** del período activo, listando personal y estudiantes por grado[cite: 371].
    * [cite_start]Generación de la **"Planilla de Inscripción"** individual y completa de cada estudiante[cite: 369].

* **Módulo de Mantenimiento:**
    * [cite_start]Panel de administrador para crear, activar y desactivar los períodos escolares (ej. "2024-2025", "2025-2026")[cite: 279, 375].

## 🛠️ Arquitectura y Tecnologías

[cite_start]El sistema fue construido siguiendo una arquitectura de tres capas y prácticas de seguridad modernas para garantizar su escalabilidad y mantenibilidad[cite: 295, 273].

* [cite_start]**Arquitectura:** Tres capas (Presentación, Lógica de Negocio, Datos) con un único punto de acceso (`/public`) para proteger el código fuente[cite: 295, 300].
* [cite_start]**Backend:** **PHP 8**[cite: 298]. [cite_start]Se encarga de procesar todas las solicitudes, aplicar las reglas de negocio y comunicarse con la base de datos a través de una serie de APIs que responden en formato JSON[cite: 298, 566, 567].
* [cite_start]**Frontend:** **HTML5, CSS3, y JavaScript (Vanilla JS)**[cite: 296]. [cite_start]Se utiliza para crear interfaces dinámicas que se comunican con el backend sin necesidad de recargar la página, ofreciendo una experiencia de usuario fluida[cite: 297].
* [cite_start]**Base de Datos:** **PostgreSQL** (Versión 14 o superior)[cite: 299, 472]. [cite_start]Se eligió por ser un sistema gestor de bases de datos relacional robusto que garantiza la integridad y consistencia de la información[cite: 299].
* **Librerías Externas:**
    * **FPDF:** Para la generación de reportes en formato PDF.
    * **PHP-QRCode:** Para la creación de los códigos QR de los estudiantes.
* [cite_start]**Entorno de Desarrollo:** **XAMPP** con **Apache** como servidor web[cite: 470].

## 🚀 Guía de Instalación Local

Para ejecutar este proyecto en un entorno de desarrollo local, sigue estos pasos:

1.  **Software Necesario:**
    * [cite_start]Instala **XAMPP** (con PHP 8.0+) desde [Apache Friends](https://www.apachefriends.org/index.html)[cite: 481, 482].
    * [cite_start]Instala **PostgreSQL** (versión 14+) desde su [sitio web oficial](https://www.postgresql.org/download/)[cite: 486, 487]. [cite_start]Recuerda guardar la contraseña que establezcas para el usuario `postgres`[cite: 488, 489].

2.  **Clonar el Repositorio:**
    * [cite_start]Navega hasta el directorio `htdocs` de tu instalación de XAMPP (ej. `C:\xampp\htdocs`)[cite: 507].
    * [cite_start]Clona este repositorio: `git clone https://github.com/rogerrcarvajal/ceia_swga.git`[cite: 508].

3.  **Configurar la Base de Datos:**
    * [cite_start]Abre **pgAdmin** y conéctate a tu servidor de PostgreSQL[cite: 512].
    * [cite_start]Crea una nueva base de datos con el nombre `ceia_db`[cite: 515].
    * [cite_start]Haz clic derecho sobre la nueva base de datos y selecciona la opción **"Restore..."**[cite: 517].
    * [cite_start]En `Filename`, busca y selecciona el archivo de la base de datos (`.sql` o `.backup`) que se encuentra en el repositorio para crear la estructura de tablas y cargar los datos iniciales[cite: 518].

4.  **Conectar la Aplicación:**
    * [cite_start]Dentro del proyecto, navega a la carpeta `src/` y abre el archivo `config.php`[cite: 521, 522].
    * [cite_start]Modifica las credenciales (`$host`, `$port`, `$dbname`, `$user`, `$password`) para que coincidan con tu configuración local de PostgreSQL[cite: 523].

5.  **Ejecutar el Proyecto:**
    * Inicia los servicios de **Apache** y **PostgreSQL**.
    * Abre tu navegador web y ve a la siguiente URL: `http://localhost/ceia_swga/public`
    * ¡Listo! Deberías ver la pantalla de login del sistema.

## 📄 Manual de Usuario Básico

1.  [cite_start]**Crear un Período Escolar:** Antes de cualquier otra operación, ve a `Mantenimiento -> Períodos Escolares` y asegúrate de que exista un período activo[cite: 573].
2.  **Inscribir un Estudiante:** Ve a `Estudiantes -> Planilla de Inscripción` y rellena todos los datos. [cite_start]El sistema te ayudará a vincular padres ya existentes para no duplicar información[cite: 572, 577].
3.  [cite_start]**Registrar Personal:** En la sección `Staff`, puedes añadir a los profesores y personal administrativo[cite: 599, 600, 601, 602].
4.  [cite_start]**Generar QR:** En el módulo `Late-Pass`, selecciona un estudiante para generar su código QR en PDF, el cual puedes imprimir[cite: 609, 610, 611, 612, 613].
5.  [cite_start]**Control de Acceso:** Utiliza la opción `Control de Acceso` del módulo `Late-Pass` para escanear los QR y registrar las llegadas[cite: 615, 616, 617].
