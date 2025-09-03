# Presentación para Defensa de Tesis - Sistema Web para la Gestión Académica

## 1. Introducción

### Diapositiva 1: Portada

*   **Título:** Sistema web para la gestión académica de inscripción y Late-Pass
*   **Contenido:**
    *   Autor: Roger R. Carvajal (C.I. V-11001150)
    *   Tutor: Jimmy Stavisky
    *   Universidad del Zulia - Facultad Experimental de Ciencias
    *   Maracaibo, 30 de septiembre de 2025
*   **Tipo Visual:** Imagen de fondo con logo institucional.
*   **Descripción Visual:** Logo de la Universidad del Zulia en todas las láminas, colores suaves.
*   **Notas para el Speech:**
    *   "Buenos días/tardes, honorable jurado, profesores y público presente."
    *   "Mi nombre es Roger R. Carvajal, estudiante de la Licenciatura en Computación de la Universidad del Zulia."
    *   "Hoy tengo el agrado de presentarles mi Trabajo Especial de Grado, titulado: 'Sistema web para la gestión académica de inscripción y Late-Pass'."
    *   "El objetivo de esta presentación es detallar el desarrollo de esta solución tecnológica, sus funcionalidades clave, la metodología empleada y los resultados obtenidos, demostrando cómo este sistema optimiza procesos críticos en el Centro Educativo Internacional Anzoátegui (CEIA)."

### Diapositiva 2: Objetivo de la Presentación

*   **Título:** Objetivo de la Presentación
*   **Contenido:**
    *   "Detallar el desarrollo de un sistema web integral para la gestión académica."
    *   "Explicar las funcionalidades clave de inscripción, control de Late-Pass y generación de reportes."
    *   "Presentar la metodología de desarrollo y los resultados tangibles obtenidos."
    *   "Demostrar la optimización de procesos y la mejora en la eficiencia institucional."
*   **Tipo Visual:** Icono representativo de "optimización" o "engranajes".
*   **Descripción Visual:** Un icono que simbolice la automatización y la eficiencia.
*   **Notas para el Speech:**
    *   "A lo largo de esta presentación, los guiaré a través del contexto que motivó este proyecto, los objetivos que nos propusimos, la metodología que seguimos para su construcción, y los resultados concretos que hemos logrado."
    *   "Mi propósito es demostrar cómo este sistema web no solo resuelve problemáticas actuales, sino que también posiciona al CEIA a la vanguardia de la gestión educativa digital."

## 2. Contexto y Justificación

### Diapositiva 3: Planteamiento del Problema

*   **Título:** El Problema: Gestión Manual y sus Consecuencias
*   **Contenido:**
    *   **Problema Central:** Procesos académicos manuales en el CEIA (inscripción, Late-Pass).
    *   **Consecuencias Directas:**
        *   Excesiva documentación y desorganización.
        *   Inseguridad y retrasos en informes (tasa de error >20%).
        *   Inestabilidad en funciones institucionales.
    *   **Impacto Cuantificable (Inscripción):**
        *   Tiempo: ~30 min/familia (manual) vs. <5 min/estudiante (digital).
        *   Errores: >20% de transcripción.
        *   Horas administrativas: ~18.75 - 25 horas solo en llenado de planillas.
*   **Tipo Visual:** Gráfico comparativo "Antes vs. Después" o infografía de problemas.
*   **Descripción Visual:** Un gráfico de barras que compare el tiempo y la tasa de error entre el proceso manual y el automatizado. Iconos de papel desordenado, un candado roto y un reloj.
*   **Notas para el Speech:**
    *   "El CEIA, como muchas instituciones, enfrentaba desafíos significativos debido a su dependencia de procesos manuales para la gestión académica."
    *   "Esto se traducía en una serie de ineficiencias: la inscripción de estudiantes, por ejemplo, consumía un promedio de 30 minutos por familia, con una alta tasa de error debido a la transcripción manual."
    *   "Esta situación generaba desorganización, retrasos en la información y comprometía la seguridad de los datos, afectando directamente la operatividad del colegio."

### Diapositiva 4: Relevancia y Justificación

*   **Título:** Relevancia y Justificación del Proyecto
*   **Contenido:**
    *   **Necesidad Imperante:** Optimizar procesos, mejorar eficiencia, seguridad y accesibilidad de la información.
    *   **Beneficios Clave:**
        *   Agilizar procesos (inscripción, asistencia, reportes).
        *   Mejorar organización y centralización de datos.
        *   Fortalecer la seguridad (acceso, integridad).
        *   Ahorrar recursos (papel, tóner, espacio).
        *   Facilitar la comunicación y consistencia de la información.
    *   **Impacto Social:** Inclusión digital, mejora de la calidad de vida educativa.
    *   **Impacto Académico:** Avance del conocimiento, perspectivas innovadoras en gestión académica.
*   **Tipo Visual:** Iconos que representen cada beneficio (velocidad, organización, seguridad, ahorro, comunicación).
*   **Descripción Visual:** Una infografía con 5 iconos claros y concisos, cada uno asociado a un beneficio.
*   **Notas para el Speech:**
    *   "Este proyecto se justifica por la necesidad crítica de transformar estos procesos manuales en un sistema digital eficiente."
    *   "Buscamos agilizar tareas, centralizar la información para una mejor organización, fortalecer la seguridad de los datos sensibles y reducir el consumo de recursos."
    *   "Más allá de la eficiencia operativa, este sistema tiene un impacto social al promover la inclusión digital y mejorar la experiencia de toda la comunidad educativa."

### Diapositiva 5: Objetivos de la Investigación

*   **Título:** Objetivos del Sistema Web
*   **Contenido:**
    *   **Objetivo General:** "Desarrollar un sistema web para la gestión académica de inscripción y Late-Pass en el Centro Educativo Internacional Anzoátegui."
    *   **Objetivos Específicos:**
        *   Analizar el flujo de trabajo manual (línea base de rendimiento).
        *   Diseñar UI/UX intuitiva (<5 min por inscripción).
        *   Integrar módulos con PostgreSQL (100% consistencia de datos).
        *   Implementar generación de códigos QR en PHP (para cada categoría).
        *   Desarrollar módulo de reportes (PDF en <15 segundos).
        *   Ejecutar pruebas funcionales (100% éxito en casos críticos).
*   **Tipo Visual:** Lista de objetivos con iconos o viñetas destacadas.
*   **Descripción Visual:** Una diapositiva limpia con el objetivo general en grande y los específicos como una lista con viñetas o pequeños iconos.
*   **Notas para el Speech:**
    *   "Para abordar esta problemática, nos planteamos un objetivo general claro: desarrollar un sistema web integral para la gestión académica de inscripción y Late-Pass en el CEIA."
    *   "Este objetivo se desglosó en una serie de objetivos específicos, que guiaron cada fase del proyecto, desde el análisis de los flujos manuales hasta la implementación de funcionalidades clave como la generación de QR y reportes, y la ejecución de pruebas rigurosas."

## 3. Marco Teórico

### Diapositiva 6: Conceptos Clave y Fundamentos

*   **Título:** Marco Teórico: Pilares del Sistema
*   **Contenido:**
    *   **Sistema Web:** Aplicación cliente-servidor, acceso vía navegador, intranet local. No requiere instalación en cliente, centraliza mantenimiento.
    *   **Gestión Académica:** Procesos administrativos del ciclo de vida estudiantil (inscripción, expedientes, asistencia, Late-Pass, reportes). Objetivo: automatizar y optimizar.
    *   **Arquitectura de 3 Capas:**
        *   **Presentación (Frontend):** HTML5, CSS3, JavaScript (interfaz de usuario).
        *   **Lógica de Negocio (Backend):** PHP 8+ (reglas, procesos, comunicación DB).
        *   **Datos (Base de Datos):** PostgreSQL (almacenamiento, integridad).
    *   **Punto de Acceso Único (`/public`):** Práctica de seguridad estándar para proteger código fuente sensible.
    *   **Stack Tecnológico:** PHP, PostgreSQL, HTML, CSS, JavaScript, Apache.
*   **Tipo Visual:** Diagrama de arquitectura de 3 capas con iconos para cada capa.
*   **Descripción Visual:** Un diagrama claro de la arquitectura de 3 capas, con flechas indicando el flujo de datos. Iconos para navegador, servidor y base de datos. Un pequeño diagrama de directorios mostrando `/public` y `/src`.
*   **Notas para el Speech:**
    *   "Nuestro sistema se fundamenta en conceptos clave de la ingeniería de software. Es un sistema web, lo que significa accesibilidad desde cualquier navegador en la intranet, sin instalaciones complejas."
    *   "Se enfoca en la gestión académica, automatizando procesos como la inscripción y el control de Late-Pass."
    *   "La arquitectura es de tres capas: una capa de presentación intuitiva, una capa de lógica de negocio robusta en PHP, y una capa de datos gestionada por PostgreSQL, garantizando la integridad."
    *   "Hemos implementado un punto de acceso único para fortalecer la seguridad, protegiendo el código fuente."

### Diapositiva 7: Antecedentes y Buenas Prácticas

*   **Título:** Antecedentes y Buenas Prácticas
*   **Contenido:**
    *   **Diagnóstico Previo (Veracierta, Rodríguez, Fermín):** Crucial para identificar cuellos de botella y asegurar que la solución responda a necesidades reales.
    *   **Centralización de Datos (Hernández et al.):** Transición de papel a base de datos digital para eliminar redundancia y pérdida de información.
    *   **Priorización de Usabilidad (Rodríguez):** Diseño de interfaces amigables e intuitivas para facilitar la adopción.
    *   **Desarrollo Modular (Fermín):** Construcción en módulos independientes para escalabilidad futura.
    *   **Rol de las TIC en Educación:** Automatización de tareas, toma de decisiones basada en datos, mejora de comunicación, ahorro de recursos.
*   **Tipo Visual:** Línea de tiempo o infografía con los autores y sus contribuciones clave.
*   **Descripción Visual:** Una línea de tiempo simplificada mostrando los años y los nombres de los autores, con una breve descripción de su aporte relevante al proyecto. Iconos para cada buena práctica.
*   **Notas para el Speech:**
    *   "Este proyecto no surge en el vacío. Nos hemos apoyado en el análisis de experiencias previas y buenas prácticas en la digitalización académica."
    *   "Estudios como los de Veracierta y Rodríguez resaltan la importancia de un diagnóstico exhaustivo y la priorización de la usabilidad."
    *   "La centralización de datos, como proponen Hernández y Fermín, es fundamental para la integridad de la información."
    *   "En esencia, este sistema es una aplicación práctica de las TIC para resolver desafíos administrativos, posicionando al CEIA en la modernización educativa."

## 4. Metodología

### Diapositiva 8: Tipo de Investigación y Modelo de Desarrollo

*   **Título:** Metodología: Construyendo la Solución
*   **Contenido:**
    *   **Tipo de Investigación:** Proyecto Factible (Investigación Tecnológica).
        *   Fase inicial de diagnóstico: Nivel descriptivo.
        *   Diseño: De Campo y no experimental (datos recolectados en entorno natural).
    *   **Modelo de Desarrollo:** Cascada (Waterfall).
        *   **Justificación:** Requisitos claros y bien definidos desde el inicio.
        *   Enfoque secuencial, planificación ordenada, documentación robusta.
        *   Minimiza riesgos de desviación del alcance.
    *   **Fases Aplicadas al SWGA:**
        *   Fase I: Análisis y Requisitos.
        *   Fase II: Diseño del Sistema.
        *   Fase III: Construcción (Implementación).
        *   Fase IV: Pruebas y Verificación.
*   **Tipo Visual:** Diagrama de flujo del Modelo en Cascada.
*   **Descripción Visual:** Un diagrama de flujo simple que muestre las fases del modelo en cascada (Análisis -> Diseño -> Implementación -> Pruebas -> Despliegue), con flechas indicando la secuencia.
*   **Notas para el Speech:**
    *   "La construcción de este sistema siguió una metodología rigurosa. Nos enmarcamos en un Proyecto Factible, con una fase inicial de diagnóstico descriptivo, recolectando datos directamente en el CEIA."
    *   "Adoptamos el Modelo en Cascada, una elección justificada por la claridad y estabilidad de los requisitos del sistema desde el inicio."
    *   "Cada fase, desde el análisis hasta las pruebas, se completó de forma secuencial y robusta, asegurando que el producto final se alineara con las necesidades identificadas."

### Diapositiva 9: Fases del Desarrollo en Detalle

*   **Título:** Fases del Desarrollo: Del Requisito al Despliegue
*   **Contenido:**
    *   **Fase I: Análisis y Requisitos:**
        *   Técnicas: Entrevistas semi-estructuradas, observación directa, análisis documental.
        *   Resultado: Documento consolidado de requisitos.
    *   **Fase II: Diseño del Sistema:**
        *   Diseño de Arquitectura (3 capas, stack tecnológico).
        *   Diseño de Base de Datos (MER, PostgreSQL).
        *   Diseño UI/UX (bocetos, prototipos).
    *   **Fase III: Construcción (Implementación):**
        *   Desarrollo Backend (PHP 8+, APIs, sesiones, reportes).
        *   Desarrollo Frontend (HTML5, CSS3, JavaScript).
        *   Integración.
        *   Herramientas: VS Code, XAMPP, Git/GitHub.
    *   **Fase IV: Pruebas y Verificación:**
        *   Pruebas Funcionales, de Integración, de Usabilidad, de Seguridad.
        *   Validación con el usuario.
*   **Tipo Visual:** Infografía de las fases con sus actividades clave.
*   **Descripción Visual:** Una infografía que resuma cada fase con sus principales actividades y resultados, utilizando iconos representativos.
*   **Notas para el Speech:**
    *   "En la fase de Análisis, comprendimos a fondo las necesidades del CEIA a través de entrevistas y observación directa."
    *   "El Diseño se centró en la arquitectura de 3 capas, la base de datos en PostgreSQL y una interfaz de usuario intuitiva."
    *   "La Construcción fue la fase de codificación, donde PHP, HTML, CSS y JavaScript dieron vida al sistema."
    *   "Finalmente, las Pruebas rigurosas aseguraron que el sistema cumpliera con todos los requisitos y fuera robusto y seguro."

## 5. Desarrollo del Trabajo y Resultados

### Diapositiva 10: Descripción de la Solución Computacional

*   **Título:** La Solución: SWGA - Un Sistema Web Integral
*   **Contenido:**
    *   **Propósito:** Centralizar y automatizar gestión académica (inscripción, Late-Pass).
    *   **Construcción:** Desde cero, tecnologías modernas, enfoque modular.
    *   **Requisitos Funcionales (Ejemplos Clave):**
        *   RF-01: Autenticación de usuarios.
        *   RF-02: Roles de usuario (Administrador, Consulta).
        *   RF-04: Registro de nuevos estudiantes (planilla digital).
        *   RF-05: Búsqueda y vinculación de representantes existentes.
        *   RF-08: Generación de Código QR único por estudiante.
        *   RF-09: Interfaz para registro de llegada por QR.
        *   RF-11: Generación de reportes en PDF (Roster, Planilla).
    *   **Requisitos No Funcionales (Ejemplos Clave):**
        *   RNF-01 (Seguridad): Acceso restringido por rol, almacenamiento seguro de contraseñas.
        *   RNF-02 (Usabilidad): Interfaz intuitiva y fácil de usar.
        *   RNF-03 (Rendimiento): Consultas rápidas, carga eficiente.
*   **Tipo Visual:** Captura de pantalla de la interfaz principal del sistema (Dashboard).
*   **Descripción Visual:** Una captura de pantalla clara del dashboard del SWGA, con flechas o recuadros destacando los módulos principales.
*   **Notas para el Speech:**
    *   "La solución desarrollada es el Sistema Web para la Gestión Académica (SWGA), diseñado para centralizar y automatizar los procesos de inscripción y Late-Pass del CEIA."
    *   "Este sistema cumple con requisitos funcionales clave como la autenticación de usuarios con roles diferenciados, el registro digital de estudiantes, la generación de códigos QR y la emisión de reportes en PDF."
    *   "Desde el punto de vista no funcional, el SWGA es seguro, usable y eficiente, garantizando la integridad de los datos y una experiencia fluida para el usuario."

### Diapositiva 11: Arquitectura y Estructura del Sistema

*   **Título:** Arquitectura y Estructura del SWGA
*   **Contenido:**
    *   **Arquitectura de 3 Capas:**
        *   **Capa de Presentación (Frontend):** HTML5, CSS3, JavaScript (interacción con usuario).
        *   **Capa de Lógica de Negocio (Backend):** PHP 8+ (controladores, APIs, reglas de negocio).
        *   **Capa de Datos:** PostgreSQL (base de datos relacional).
    *   **Estructura de Directorios:**
        *   `public/`: Punto de acceso único (seguridad).
        *   `src/`: Código fuente, configuración, librerías.
        *   `api/`: Endpoints para comunicación AJAX.
        *   `pages/`: Páginas principales de la aplicación.
    *   **Beneficio Clave:** Seguridad y organización del código.
*   **Tipo Visual:** Diagrama de Arquitectura de 3 Capas y Estructura de Directorios.
*   **Descripción Visual:** Un diagrama que muestre las tres capas y cómo se interconectan. Al lado, un diagrama de árbol simplificado de la estructura de directorios, destacando la carpeta `public` como punto de entrada.
*   **Notas para el Speech:**
    *   "El SWGA se construyó sobre una sólida arquitectura de tres capas, que separa claramente la presentación, la lógica de negocio y la capa de datos."
    *   "Utilizamos PHP para el backend, PostgreSQL para la base de datos y tecnologías web estándar para el frontend, garantizando robustez y escalabilidad."
    *   "La estructura de directorios, con un único punto de acceso público, es una medida de seguridad fundamental que protege el código fuente del sistema."

### Diapositiva 12: Modelado del Sistema (UML)

*   **Título:** Modelado del Sistema: UML
*   **Contenido:**
    *   **Diagrama de Casos de Uso:**
        *   Actores: Master, Administrador, Usuario de Consulta.
        *   Casos de Uso Principales: Autenticarse, Gestionar Períodos, Gestionar Personal, Gestionar Estudiantes (Inscribir, Administrar Expediente, Asignar a Período), Gestionar Control de Acceso (QR, Registrar Llegada, Consultar Historial), Gestionar Usuarios, Generar Reportes.
        *   Relaciones: Acceso diferenciado por rol.
    *   **Diagrama de Clases (Conceptual):**
        *   Entidades clave: Usuario, Estudiante, Profesor, PeriodoEscolar, LlegadaTarde, Representante.
        *   Atributos y Métodos principales.
        *   Relaciones y Multiplicidad (ej. Estudiante tiene Padre y Madre, Estudiante tiene muchas LlegadaTarde).
    *   **Diagrama de Secuencia: Proceso "Registrar Llegada Tarde" (QR):**
        *   Ilustra la interacción entre Usuario, ControlAccesoUI, LatePassAPI y BaseDeDatos.
        *   Pasos: Escaneo QR, petición AJAX, validación, registro en DB, actualización de strikes, confirmación.
*   **Tipo Visual:** Miniaturas o esquemas simplificados de los diagramas UML.
*   **Descripción Visual:** Tres pequeños recuadros, cada uno con un esquema muy simplificado de un diagrama UML (Casos de Uso, Clases, Secuencia). Flechas y texto mínimo para representar la idea.
*   **Notas para el Speech:**
    *   "Para modelar el sistema, utilizamos el Lenguaje Unificado de Modelado (UML)."
    *   "El Diagrama de Casos de Uso nos permitió definir las funcionalidades desde la perspectiva de los actores, como el Master, Administrador y Usuario de Consulta, con sus accesos diferenciados."
    *   "El Diagrama de Clases conceptualizó las entidades del sistema y sus relaciones, como Estudiante, Profesor y Representante."
    *   "Y el Diagrama de Secuencia del proceso 'Registrar Llegada Tarde' ilustra la interacción dinámica entre los componentes del sistema, desde el escaneo del QR hasta el registro en la base de datos."

### Diapositiva 13: Diagrama de Base de Datos (MER)

*   **Título:** Diseño de la Base de Datos: Diagrama MER
*   **Contenido:**
    *   **Pilar del Sistema:** Implementado en PostgreSQL.
    *   **Garantía:** Integridad de datos, evita redundancia, consultas eficientes.
    *   **Tablas Principales:**
        *   `estudiantes`: Datos personales, relación con padres/madres.
        *   `padres / madres`: Información de representantes (vinculables a múltiples estudiantes).
        *   `profesores`: Datos de personal.
        *   `periodos_escolares`: Gestión de años escolares.
        *   `profesor_periodo / estudiante_periodo`: Tablas pivote para vinculación.
        *   `llegadas_tarde / latepass_resumen_semanal`: Control de acceso y strikes.
        *   `vehiculos`: Información de vehículos.
        *   `registro_vehiculos`: Vinculación vehículo-estudiante.
        *   `entrada_salida_staff`: Registro de movimientos de personal.
*   **Tipo Visual:** Diagrama MER (Modelo Entidad-Relación) del sistema.
*   **Descripción Visual:** El Diagrama MER completo de la base de datos `PostgreSQL_ceia_db` (Figura No.2 de la tesis). Destacar las relaciones clave.
*   **Notas para el Speech:**
    *   "El corazón del sistema es su base de datos, implementada en PostgreSQL."
    *   "El Diagrama MER que ven en pantalla representa visualmente la estructura relacional, garantizando la integridad y eficiencia de los datos."
    *   "Contamos con tablas clave como 'estudiantes', 'padres', 'madres', 'profesores', y tablas pivote que gestionan las relaciones complejas, como la vinculación de estudiantes a períodos escolares o el registro de llegadas tarde."

### Diapositiva 14: Módulos Implementados y Mapa Navegacional

*   **Título:** Módulos del SWGA y Navegación
*   **Contenido:**
    *   **Módulos Operativos:**
        *   Autenticación y Usuarios
        *   Estudiantes (Inscripción, Administración, Asignación)
        *   Staff/Profesores (Gestión de Personal)
        *   Late-Pass (Generación QR, Control de Acceso, Gestión y Consulta)
        *   Reportes (Generación de diversos PDFs)
        *   Mantenimiento (Gestión de Períodos, Usuarios, Respaldo DB)
    *   **Mapa Navegacional (Flujo Lógico):**
        *   Login (`/public/index.php`) -> Dashboard (`/pages/dashboard.php`)
        *   Desde Dashboard: Acceso a módulos (Estudiantes, Staff, Late-Pass, Reportes, Mantenimiento).
        *   Ejemplos de rutas:
            *   Estudiantes -> Planilla de Inscripción (`/pages/planilla_inscripcion.php`)
            *   Late-Pass -> Control de Acceso (`/pages/control_acceso.php`)
            *   Reportes -> Seleccionar Planilla (`/pages/seleccionar_planilla.php`)
*   **Tipo Visual:** Mapa de navegación simplificado o diagrama de módulos.
*   **Descripción Visual:** Un diagrama de flujo que muestre los módulos principales y cómo se accede a ellos desde el dashboard. Podría ser un mapa de sitio simplificado.
*   **Notas para el Speech:**
    *   "El sistema se organiza en módulos operativos clave, cada uno diseñado para una función específica."
    *   "Desde el módulo de Estudiantes, que abarca desde la inscripción hasta la administración de expedientes, hasta el módulo de Late-Pass para el control de puntualidad."
    *   "La navegación es intuitiva, con un punto de entrada central en el Dashboard que permite acceder a todas las funcionalidades de manera lógica."

### Diapositiva 15: Resultados Clave y Beneficios Medidos

*   **Título:** Impacto del SWGA: Beneficios Cuantificables
*   **Contenido:**
    *   **Proceso de Inscripción:**
        *   Antes (Manual): ~30 min/familia, >20% error, papel.
        *   Después (Automatizado): <5 min/estudiante (80% ahorro), error <1%, digital.
    *   **Proceso de Control de Late-Pass:**
        *   Antes (Manual): Lento, propenso a pérdida, difícil trazabilidad.
        *   Después (Automatizado): Registro instantáneo y preciso (QR), control automatizado de "strikes", trazabilidad completa.
    *   **Beneficios Generales:**
        *   Optimización del Tiempo Administrativo.
        *   Mejora en la Organización y Acceso a la Información.
        *   Reducción de Costos Operativos (papel, tóner).
        *   Fortalecimiento de la Seguridad de los Datos.
        *   Mejora de la Imagen Institucional.
*   **Tipo Visual:** Gráficos de barras o circulares comparando "Antes vs. Después" para tiempo y errores.
*   **Descripción Visual:** Dos gráficos: uno comparando el tiempo de inscripción (manual vs. SWGA) y otro la tasa de error. Una infografía con iconos para los beneficios generales.
*   **Notas para el Speech:**
    *   "Los resultados de la implementación del SWGA son contundentes y medibles."
    *   "En el proceso de inscripción, logramos una reducción del tiempo de más del 80% y una disminución drástica en la tasa de errores."
    *   "El control de Late-Pass pasó de ser un proceso manual ineficiente a uno automatizado, preciso y con trazabilidad completa."
    *   "En general, el sistema ha optimizado el tiempo administrativo, mejorado la organización, reducido costos y fortalecido la seguridad de los datos, proyectando una imagen de innovación para el CEIA."

## 6. Conclusiones

### Diapositiva 16: Conclusiones Principales

*   **Título:** Conclusiones del Proyecto
*   **Contenido:**
    *   **1. Diagnóstico y Validación de la Necesidad:** Procesos manuales eran un obstáculo significativo; análisis confirmó duplicidad, difícil acceso y tiempo valioso consumido.
    *   **2. Cumplimiento Integral de Objetivos:** Desarrollo de prototipo funcional que cumple satisfactoriamente con todos los objetivos específicos (arquitectura robusta, integración de módulos, generación de reportes y QR).
    *   **3. Transformación y Optimización de Procesos:** Automatización radical del flujo de trabajo administrativo (registro de inscripciones, control de acceso QR), eliminando redundancia y agilizando procesos (>80% ahorro de tiempo).
    *   **4. Arquitectura Escalable y Segura:** Elección de arquitectura de 3 capas con punto de acceso único y tecnologías probadas (PHP, PostgreSQL, JS) resultó en un sistema funcional, seguro y escalable. Roles de usuario diferenciados garantizan acceso restringido.
    *   **5. Impacto Institucional Positivo:** Agente de cambio positivo; liberó personal administrativo, mejoró calidad de servicio, redujo uso de papel, modernizó imagen del CEIA.
*   **Tipo Visual:** Lista de puntos clave con iconos de "check".
*   **Descripción Visual:** Una diapositiva con los 5 puntos clave de las conclusiones, cada uno con un icono de "check" o "éxito".
*   **Notas para el Speech:**
    *   "En resumen, este Trabajo Especial de Grado ha validado la necesidad de automatizar los procesos manuales del CEIA, que representaban un obstáculo significativo."
    *   "Hemos logrado cumplir integralmente con todos los objetivos planteados, transformando y optimizando radicalmente los flujos de trabajo administrativos."
    *   "La arquitectura del sistema es robusta, segura y escalable, sentando las bases para futuras expansiones."
    *   "El impacto institucional es claramente positivo, mejorando la eficiencia, reduciendo costos y modernizando la imagen del CEIA."

### Diapositiva 17: Implicaciones y Limitaciones

*   **Título:** Implicaciones y Consideraciones Futuras
*   **Contenido:**
    *   **Implicaciones del Estudio:**
        *   Demuestra el valor de la ingeniería de software para optimizar la gestión educativa.
        *   Sienta las bases para una mejora continua de los procesos institucionales.
        *   Posiciona al CEIA a la vanguardia tecnológica.
    *   **Limitaciones (si aplica):**
        *   (Si hay alguna limitación no cubierta por las recomendaciones, mencionarla aquí. Por ejemplo, "El alcance inicial se centró en inscripción y Late-Pass, dejando otros módulos para futuras fases.")
    *   **Recomendaciones (Breve Resumen):**
        *   Capacitación y Gestión del Cambio.
        *   Expansión a Nuevos Módulos (ej. profesores y vehículos).
        *   Mantenimiento Proactivo y Auditorías de Seguridad.
        *   Integración con Herramientas de Comunicación.
        *   Recopilación de Feedback y Mejora Continua.
*   **Tipo Visual:** Infografía con dos secciones: "Implicaciones" y "Limitaciones/Recomendaciones".
*   **Descripción Visual:** Una infografía dividida en dos, con iconos para cada punto.
*   **Notas para el Speech:**
    *   "Este trabajo no solo resuelve un problema específico, sino que demuestra el valor de la ingeniería de software en el ámbito educativo, sentando las bases para la mejora continua en el CEIA."
    *   "Como toda investigación, existen consideraciones para el futuro. Recomendamos enfáticamente la capacitación continua del personal, la expansión a nuevos módulos como el control de acceso de profesores y vehículos, y un mantenimiento proactivo para asegurar la sostenibilidad y seguridad del sistema a largo plazo."

## 7. Recomendaciones

### Diapositiva 18: Recomendaciones Detalladas

*   **Título:** Recomendaciones para el Futuro del SWGA
*   **Contenido:**
    *   **1. Plan de Capacitación y Gestión del Cambio:** Implementar programa integral con sesiones prácticas y manuales de usuario para una transición fluida.
    *   **2. Expansión a Nuevos Módulos Funcionales:** Desarrollar módulos adicionales (ej. gestión y control de entradas/salidas de profesores y vehículos) aprovechando la arquitectura modular.
    *   **3. Mantenimiento Proactivo y Auditorías de Seguridad:** Establecer plan de mantenimiento periódico (actualización de PHP, PostgreSQL, librerías) y auditorías de seguridad regulares.
    *   **4. Integración con Herramientas de Comunicación:** Explorar integración con notificaciones por email/mensajería para alertas automáticas (ej. "strikes" de Late-Pass).
    *   **5. Recopilación de Feedback y Mejora Continua:** Mecanismo formal para feedback de usuarios finales (encuestas, reuniones) para evolución del sistema.
*   **Tipo Visual:** Lista de recomendaciones con iconos.
*   **Descripción Visual:** Una diapositiva con los 5 puntos clave de las recomendaciones, cada uno con un icono representativo.
*   **Notas para el Speech:**
    *   "Para maximizar el impacto y asegurar la sostenibilidad del SWGA, proponemos las siguientes recomendaciones."
    *   "Es crucial un plan de capacitación continuo para el personal, así como la expansión del sistema a nuevos módulos, aprovechando su diseño modular."
    *   "El mantenimiento proactivo y las auditorías de seguridad son esenciales para proteger la inversión y los datos sensibles."
    *   "Finalmente, la integración con herramientas de comunicación y la recopilación constante de feedback garantizarán que el sistema siga siendo una herramienta valiosa y evolucione con las necesidades del CEIA."

### Diapositiva 19: ¡Gracias!

*   **Título:** ¡Muchas Gracias!
*   **Contenido:**
    *   "Preguntas y Comentarios"
    *   "Contacto: rogerrcarvajal@gmail.com"
*   **Tipo Visual:** Logo institucional con datos de contacto.
*   **Descripción Visual:** El logo del CEIA o de la universidad, con el correo electrónico de contacto.
*   **Notas para el Speech:**
    *   "Con esto, concluyo la presentación de mi Trabajo Especial de Grado."
    *   "Agradezco sinceramente su atención y estoy a su disposición para cualquier pregunta o comentario que puedan tener."
    *   "Mi correo electrónico es rogerrcarvajal@gmail.com."

