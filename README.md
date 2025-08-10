# CEIA SWGA - Sistema Web de Gestión Académica

Sistema de gestión para el Centro Educativo Internacional Anzoátegui (CEIA). Esta aplicación web permite administrar información de estudiantes, padres, vehículos y generar reportes en formato PDF, como planillas de inscripción y carnets vehiculares con códigos QR.

## ✨ Características Principales

- **Gestión de Estudiantes:** Almacenamiento de datos personales, académicos y de contacto.
- **Gestión de Familiares:** Registro de información detallada de padres y madres.
- **Ficha Médica:** Módulo para registrar información de salud relevante del estudiante.
- **Control Vehicular:** Asociación de vehículos a estudiantes para control de acceso.
- **Generación de Reportes en PDF:**
  - Planilla de Inscripción completa.
  - Carnet de identificación vehicular con código QR.
- **Seguridad:** Acceso a funcionalidades restringido por sesión de usuario.

## 🚀 Tecnologías Utilizadas

- **Backend:** PHP 7.x / 8.x
- **Base de Datos:** Conexión a través de PDO, compatible con MySQL/MariaDB.
- **Servidor Web:** Apache (desplegado en un entorno XAMPP).
- **Librerías PHP:**
  - **FPDF:** Para la generación dinámica de documentos PDF.
  - **PHP QR Code:** Para la creación de códigos QR.

## 📋 Prerrequisitos

- Un entorno de desarrollo web como XAMPP, WAMP o similar.
- PHP (versión 7.4 o superior recomendada).
- Servidor de base de datos (MySQL/MariaDB).

## ⚙️ Instalación y Configuración

1.  Clona o descarga este repositorio en el directorio `htdocs` de tu instalación de XAMPP. La carpeta del proyecto debería ser `ceia_swga`.
2.  Crea una base de datos (ej. `ceia_db`) en phpMyAdmin o el gestor de tu preferencia.
3.  Importa la estructura y los datos de la base de datos desde el archivo `.sql` del proyecto (si existe).
4.  Configura la conexión a la base de datos en el archivo `src/config.php`. Asegúrate de que las credenciales (host, usuario, contraseña, nombre de la base de datos) sean correctas.
5.  Inicia los servicios de Apache y MySQL desde el panel de control de XAMPP.
6.  Accede a la aplicación desde tu navegador, usualmente en `http://localhost/ceia_swga/`.

## 📁 Estructura del Proyecto (Parcial)

```
ceia_swga/
├── public/
│   └── img/            # Recursos gráficos (logos, etc.)
└── src/
    ├── lib/
    │   ├── fpdf.php
    │   └── php-qrcode/
    ├── reports_generators/ # Scripts para generar PDFs
    │   ├── generar_planilla_pdf.php
    │   └── generar_qr_vehiculo_pdf.php
    └── config.php      # Configuración de la BD
```

## 📄 Módulos de Reportes

El sistema cuenta con los siguientes generadores de reportes, accesibles a través de su URL y requiriendo un ID como parámetro.

### 1. Planilla de Inscripción (`generar_planilla_pdf.php`)

- **Propósito:** Genera un documento PDF con la información completa de un estudiante, incluyendo datos personales, de sus padres y ficha médica.
- **Uso:** Se accede pasando el ID del estudiante como parámetro GET.
  ```
  http://localhost/ceia_swga/src/reports_generators/generar_planilla_pdf.php?id=123
  ```

### 2. Carnet Vehicular QR (`generar_qr_vehiculo_pdf.php`)

- **Propósito:** Genera un carnet en PDF para un vehículo asociado a un estudiante. Incluye placa, modelo, propietario y un código QR que contiene el ID del vehículo para una rápida identificación.
- **Uso:** Se accede pasando el ID del vehículo como parámetro GET.
  ```
  http://localhost/ceia_swga/src/reports_generators/generar_qr_vehiculo_pdf.php?id=45
  ```