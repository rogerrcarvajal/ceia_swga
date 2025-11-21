# Sistema Web de Gestión Académica (SWGA) - CEIA

<<<<<<< HEAD
Desarrollado como Trabajo Especial de Grado para optar al título de Licenciado en Computación. Este sistema web ofrece una solución integral para automatizar y optimizar los procesos de inscripción, control de asistencia y gestión académica en el Centro Educativo Internacional Anzoátegui (CEIA).
=======
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14%2B-336791?style=for-the-badge&logo=postgresql)
![Status](https://img.shields.io/badge/Estado-En%20Mejora%20Continua-brightgreen?style=for-the-badge)
![License](https://img.shields.io/badge/Licencia-MIT-blue?style=for-the-badge)

Desarrollado como Trabajo Especial de Grado, **SWGA** es una solución web integral diseñada para automatizar y optimizar los procesos de inscripción, control de acceso y gestión académica en el Centro Educativo Internacional Anzoátegui (CEIA).
>>>>>>> f9621219998e6cdc4c0ccbb80751ada5e42aa9e0

**Autor:** Roger R. Carvajal

## ✅ Auditoría y Mejoras Continuas

<<<<<<< HEAD
Este proyecto se encuentra bajo un **proceso de auditoría y mejora continua** para asegurar la calidad del código, reforzar la seguridad y mantener la coherencia entre la funcionalidad y la documentación. Los hallazgos y conclusiones de estas revisiones se almacenan en la carpeta `/Auditorias` para mantener un registro histórico.

La primera auditoría (Octubre 2025) sobre los módulos de **Estudiantes** y **Late-Pass** ha permitido identificar vulnerabilidades de seguridad y áreas de mejora, cuyas correcciones se priorizarán para robustecer el sistema.

## ✨ Características Principales

El sistema está compuesto por una serie de módulos diseñados para cubrir todo el ciclo de gestión académica requerido por la institución:

* **Autenticación y Sistema de Roles Avanzado:**
    * Sistema de `login` seguro para validar a los usuarios.
    * Gestión basada en tres roles: **Superusuario (master)**, **Administrador (admin)** y **Consulta (consulta)**, permitiendo un control de acceso granular.

* **Módulo de Estudiantes:**
    * Formulario de inscripción digital para registrar nuevos estudiantes y sus datos familiares/médicos.
    * Lógica inteligente para buscar y vincular representantes (padres/madres) ya existentes, evitando la duplicidad de datos.
    * Interfaz para consultar y modificar los expedientes completos de los estudiantes en tiempo real.
    * **Gestión de Autorización de Salida:** Permite registrar y controlar las salidas tempranas de los estudiantes, generando un comprobante en PDF para un control físico y formal.

* **Módulo de Staff / Profesores:**
  * Registro de personal por categoría (Administrativo, Docente, Mantenimiento, Vigilancia).
  * Asignación de cargos y períodos escolares.

* **Módulo Late-Pass (Control de Acceso con QR):**
  * Generación de **códigos QR únicos** para estudiantes, staff y vehículos.
  * Interfaz de control de acceso que valida y registra automáticamente movimientos mediante lector QR.
  * Registro en tiempo real con lógica de negocio específica para cada tipo de entidad (llegadas tarde de estudiantes, ciclo de trabajo del personal, etc.).
  * Sistema de conteo de **“strikes” semanales** por llegadas tarde, con alertas visuales en pantalla.

* **Módulo Vehículos:**
  * Registro y control de vehículos autorizados para el retiro de estudiantes.
  * Generación de códigos QR para control de acceso vehicular.

* **Módulo Reportes Ampliado:**
  * Reportes en **PDF** de estudiantes, staff y vehículos.
  * Reporte “Roster” del período activo.
  * Estadísticas de movimientos y control de asistencia.

* **Módulo Mantenimiento:**
  * Administración y activación de períodos escolares.
  * Gestión de usuarios y roles del sistema.

* **Módulo de Ayuda:**
  * Documentación interna del sistema accesible desde el menú principal, generada a partir de archivos Markdown.

## 🛠️ Arquitectura y Tecnologías

* **Arquitectura:** Tres capas (Presentación, Lógica de Negocio, Datos) con un único punto de acceso (`/public`) para proteger el código fuente.
* **Backend:** **PHP 8**.
* **Frontend:** **HTML5, CSS3, y JavaScript (Vanilla JS)**.
* **Base de Datos:** **PostgreSQL** (Versión 14 o superior).
* **Librerías Externas:**
    * **FPDF:** Para la generación de reportes en formato PDF.
    * **PHP-QRCode:** Para la creación de los códigos QR.

## 🚀 Guía de Instalación Local

Para ejecutar este proyecto en un entorno de desarrollo local, sigue estos pasos:

1.  **Software Necesario:**
    * Instala **XAMPP** (con PHP 8.0+) y **PostgreSQL** (versión 14+).

2.  **Clonar el Repositorio:**
    * Navega hasta el directorio `htdocs` de tu instalación de XAMPP.
    * Clona este repositorio: `git clone https://github.com/rogerrcarvajal/ceia_swga.git`.

3.  **Configurar la Base de Datos:**
    * En **pgAdmin**, crea una nueva base de datos (`ceia_db`).
    * Restaura la base de datos usando uno de los archivos de respaldo (`.sql` o `.backup`) del repositorio.

4.  **Conectar la Aplicación:**
    * En `src/config.php`, modifica las credenciales de conexión para que coincidan con tu configuración local de PostgreSQL.

5.  **Ejecutar el Proyecto:**
    * Inicia los servicios de **Apache** y **PostgreSQL**.
    * Abre tu navegador y ve a: `http://localhost/ceia_swga/public`

## 📚 Documentación Técnica

La documentación modular del sistema se encuentra en la carpeta `/Funcionality/`, organizada por cada módulo principal. Estos archivos `.md` describen la lógica de negocio y son visualizables desde el Módulo de Ayuda del sistema.

## ℹ Contacto

Autor: Roger R. Carvajal
📧 Correo: rogerrcarvajal@gmail.com
=======
Este proyecto se encuentra bajo un **proceso de auditoría y mejora continua** para asegurar la calidad del código, reforzar la seguridad y mantener la coherencia entre la funcionalidad y la documentación. Los hallazgos y conclusiones se registran en la carpeta `/Auditorias`.

La primera auditoría (Octubre 2025) sobre los módulos de **Estudiantes** y **Late-Pass** permitió identificar áreas de mejora y vulnerabilidades de seguridad, cuyas correcciones se están implementando para robustecer el sistema.

## ✨ Características Principales

El sistema está compuesto por una serie de módulos interconectados para cubrir el ciclo de gestión académica:

- **🔐 Autenticación y Roles:**
  - Sistema de `login` seguro.
  - Gestión granular de permisos basada en roles: **Superusuario (master)**, **Administrador (admin)** y **Consulta (consulta)**.

- **🎓 Módulo de Estudiantes:**
  - Formulario de inscripción digital con datos familiares y médicos.
  - Lógica inteligente para vincular representantes existentes y evitar duplicidad.
  - Gestión de expedientes en tiempo real.
  - **Autorización de Salida:** Registro y control de salidas tempranas con generación de comprobantes en PDF.

- **👥 Módulo de Staff / Profesores:**
  - Registro de personal por categorías (Administrativo, Docente, etc.).
  - Asignación de cargos y períodos escolares.
  - Menú de gestión con submenús desplegables para una navegación más limpia.

- **🛂 Módulo Late-Pass (Control de Acceso QR):**
  - Generación de **códigos QR únicos** para estudiantes, staff y vehículos.
  - Interfaz de control de acceso para registrar movimientos escaneando el QR.
  - Lógica de negocio para llegadas tarde, ciclo de trabajo del personal y más.
  - Conteo de **“strikes” semanales** por impuntualidad con alertas visuales.

- **🚗 Módulo Vehículos:**
  - Registro de vehículos autorizados para el retiro de estudiantes.
  - Generación de QR para un control de acceso vehicular eficiente.

- **📊 Módulo Reportes:**
  - Generación de reportes **PDF** (listados de estudiantes, personal, "Roster" del período).
  - Estadísticas de movimientos y control de asistencia.

- **🔧 Módulo Mantenimiento:**
  - Administración y activación de períodos escolares.
  - Gestión de usuarios y roles del sistema.

- **❓ Módulo de Ayuda:**
  - Visualizador de documentación interna del sistema (archivos Markdown) integrado en la interfaz.

## 🛠️ Arquitectura y Tecnologías

- **Arquitectura:** Tres capas (Presentación, Lógica de Negocio, Datos) con un único punto de acceso (`/public`) para proteger el código fuente.
- **Backend:** **PHP 8**
- **Frontend:** **HTML5, CSS3, y JavaScript (Vanilla JS)**
- **Base de Datos:** **PostgreSQL** (Versión 14 o superior)
- **Librerías Externas:**
  - **FPDF:** Para la generación de reportes en PDF.
  - **PHP-QRCode:** Para la creación de códigos QR.

## 🚀 Guía de Instalación Local

1.  **Software Necesario:** Instala **XAMPP** (con PHP 8.0+) y **PostgreSQL** (versión 14+).
2.  **Clonar Repositorio:** En el directorio `htdocs` de XAMPP, clona el proyecto:
    ```bash
    git clone https://github.com/rogerrcarvajal/ceia_swga.git
    ```
3.  **Base de Datos:** En **pgAdmin**, crea una base de datos (ej. `ceia_db`) y restaura la estructura y datos desde el archivo `.sql` o `.backup` más reciente ubicado en `/PostgreSQL-DB`.
4.  **Conexión:** En `src/config.php`, ajusta las credenciales de conexión a tu configuración local de PostgreSQL.
5.  **Ejecutar:** Inicia los servicios de Apache y PostgreSQL. Accede desde tu navegador a:
    `http://localhost/ceia_swga/public`

## 📚 Documentación Técnica

La documentación detallada de cada módulo se encuentra en la carpeta `/Funcionality/`. Estos archivos `.md` describen la lógica de negocio y son renderizados en el Módulo de Ayuda del sistema.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Si deseas mejorar el proyecto, por favor sigue estos pasos:
1.  Haz un **Fork** de este repositorio.
2.  Crea una nueva rama para tu mejora (`git checkout -b feature/MejoraIncreible`).
3.  Realiza tus cambios y haz **Commit** (`git commit -m 'Añade una mejora increíble'`).
4.  Haz **Push** a tu rama (`git push origin feature/MejoraIncreible`).
5.  Abre un **Pull Request**.

## 📜 Licencia

Este proyecto está bajo la **Licencia MIT**.

## ℹ️ Contacto

**Autor:** Roger R. Carvajal
- **Correo:** rogerrcarvajal@gmail.com
- **GitHub:** [rogerrcarvajal](https://github.com/rogerrcarvajal)
>>>>>>> f9621219998e6cdc4c0ccbb80751ada5e42aa9e0
