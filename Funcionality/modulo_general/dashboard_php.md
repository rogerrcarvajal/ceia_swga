# Funcionalidad del Dashboard (`dashboard.php`)

El `dashboard.php` es la página principal o de bienvenida que ven los usuarios inmediatamente después de iniciar sesión en el sistema.

## Lógica de Negocio

1.  **Control de Acceso y Sesión:**
    *   Lo primero que hace el script es iniciar o reanudar la sesión del usuario con `session_start()`.
    *   Verifica si existe una variable de sesión `$_SESSION['usuario']`. Si no existe, significa que el usuario no está autenticado.
    *   En caso de no estar autenticado, el script redirige inmediatamente al usuario a la página de login pública (`/ceia_swga/public/index.php`) para prevenir cualquier acceso no autorizado al panel.

2.  **Identificación del Período Escolar Activo:**
    *   El script se conecta a la base de datos y ejecuta una consulta sobre la tabla `periodos_escolares`.
    *   El objetivo de la consulta es encontrar el único período que tiene la columna `activo` establecida en `TRUE`.
    *   **Si se encuentra un período activo:** Su nombre se guarda para mostrarlo en la interfaz, permitiendo que el usuario sepa siempre en qué contexto de trabajo (período) se encuentra.
    *   **Si no se encuentra un período activo:** Se crea una variable de sesión de error (`$_SESSION['error_periodo_inactivo']`). Este mensaje no se muestra en el dashboard, pero está disponible para ser mostrado en otras secciones del sistema (como en la gestión de estudiantes) donde tener un período activo es un requisito indispensable para operar.

3.  **Renderizado de la Interfaz:**
    *   Es una página HTML que presenta la bienvenida al sistema.
    *   Incluye la barra de navegación principal (`navbar.php`), que es un componente común en todas las páginas internas.
    *   Muestra el logo del colegio, un mensaje de bienvenida y el nombre de la institución.
    *   Destaca de forma visible el nombre del período escolar activo, si existe.
    *   Incluye un pie de página con información de copyright.
