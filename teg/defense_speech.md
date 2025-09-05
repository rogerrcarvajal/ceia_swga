## Discurso para la Defensa de Tesis - Sistema Web para la Gestión Académica

---

### Introducción (Diapositiva 1 y 2)

"Buenos días/tardes, honorable jurado, profesores y público presente. Mi nombre es Roger R. Carvajal, estudiante de la Licenciatura en Computación de la Universidad del Zulia. Hoy tengo el agrado de presentarles mi Trabajo Especial de Grado, titulado: 'Sistema web para la gestión académica de inscripción y Late-Pass'.

A lo largo de esta presentación, los guiaré a través del contexto que motivó este proyecto, los objetivos que nos propusimos, la metodología que seguimos para su construcción, y los resultados concretos que hemos logrado. Mi propósito es demostrar cómo este sistema web no solo resuelve problemáticas actuales, sino que también posiciona al CEIA a la vanguardia de la gestión educativa digital."

---

### Contexto y Justificación (Diapositiva 3, 4 y 5)

"El CEIA, como muchas instituciones, enfrentaba desafíos significativos debido a su dependencia de procesos manuales para la gestión académica. Esto se traducía en una serie de ineficiencias: la inscripción de estudiantes, por ejemplo, consumía un promedio de 30 minutos por familia, con una alta tasa de error debido a la transcripción manual. Esta situación generaba desorganización, retrasos en la información y comprometía la seguridad de los datos, afectando directamente la operatividad del colegio.

Este proyecto se justifica por la necesidad crítica de transformar estos procesos manuales en un sistema digital eficiente. Buscamos agilizar tareas, centralizar la información para una mejor organización, fortalecer la seguridad de los datos sensibles y reducir el consumo de recursos. Más allá de la eficiencia operativa, este sistema tiene un impacto social al promover la inclusión digital y mejorar la experiencia de toda la comunidad educativa.

Para abordar esta problemática, nos planteamos un objetivo general claro: desarrollar un sistema web integral para la gestión académica de inscripción y Late-Pass en el CEIA. Este objetivo se desglosó en una serie de objetivos específicos, que guiaron cada fase del proyecto, desde el análisis de los flujos manuales hasta la implementación de funcionalidades clave como la generación de QR y reportes, y la ejecución de pruebas rigurosas."

---

### Marco Teórico (Diapositiva 6 y 7)

"Nuestro sistema se fundamenta en conceptos clave de la ingeniería de software. Es un sistema web, lo que significa accesibilidad desde cualquier navegador en la intranet, sin instalaciones complejas. Se enfoca en la gestión académica, automatizando procesos como la inscripción y el control de Late-Pass.

La arquitectura es de tres capas: una capa de presentación intuitiva, una capa de lógica de negocio robusta en PHP, y una capa de datos gestionada por PostgreSQL, garantizando la integridad. Hemos implementado un punto de acceso único para fortalecer la seguridad, protegiendo el código fuente.

Este proyecto no surge en el vacío. Nos hemos apoyado en el análisis de experiencias previas y buenas prácticas en la digitalización académica. Estudios como los de Veracierta y Rodríguez resaltan la importancia de un diagnóstico exhaustivo y la priorización de la usabilidad. La centralización de datos, como proponen Hernández y Fermín, es fundamental para la integridad de la información. En esencia, este sistema es una aplicación práctica de las TIC para resolver desafíos administrativos, posicionando al CEIA en la modernización educativa."

---

### Metodología (Diapositiva 8 y 9)

"La construcción de este sistema siguió una metodología rigurosa. Nos enmarcamos en un Proyecto Factible, con una fase inicial de diagnóstico descriptivo, recolectando datos directamente en el CEIA. Adoptamos el Modelo en Cascada, una elección justificada por la claridad y estabilidad de los requisitos del sistema desde el inicio. Cada fase, desde el análisis hasta las pruebas, se completó de forma secuencial y robusta, asegurando que el producto final se alineara con las necesidades identificadas.

En la fase de Análisis, comprendimos a fondo las necesidades del CEIA a través de entrevistas y observación directa. El Diseño se centró en la arquitectura de 3 capas, la base de datos en PostgreSQL y una interfaz de usuario intuitiva. La Construcción fue la fase de codificación, donde PHP, HTML, CSS y JavaScript dieron vida al sistema. Finalmente, las Pruebas rigurosas aseguraron que el sistema cumpliera con todos los requisitos y fuera robusto y seguro."

---

### Desarrollo del Trabajo y Resultados (Diapositiva 10, 11, 12, 13, 14 y 15)

"La solución desarrollada es el Sistema Web para la Gestión Académica (SWGA), diseñado para centralizar y automatizar los procesos de inscripción y Late-Pass del CEIA. Este sistema cumple con requisitos funcionales clave como la autenticación de usuarios con roles diferenciados, el registro digital de estudiantes, la generación de códigos QR y la emisión de reportes en PDF. Desde el punto de vista no funcional, el SWGA es seguro, usable y eficiente, garantizando la integridad de los datos y una experiencia fluida para el usuario.

El SWGA se construyó sobre una sólida arquitectura de tres capas, que separa claramente la presentación, la lógica de negocio y la capa de datos. Utilizamos PHP para el backend, PostgreSQL para la base de datos y tecnologías web estándar para el frontend, garantizando robustez y escalabilidad. La estructura de directorios, con un único punto de acceso público, es una medida de seguridad fundamental que protege el código fuente del sistema.

Para modelar el sistema, utilizamos el Lenguaje Unificado de Modelado (UML). El Diagrama de Casos de Uso nos permitió definir las funcionalidades desde la perspectiva de los actores, como el Master, Administrador y Usuario de Consulta, con sus accesos diferenciados. El Diagrama de Clases conceptualizó las entidades del sistema y sus relaciones, como Estudiante, Profesor y Representante. Y el Diagrama de Secuencia del proceso 'Registrar Llegada Tarde' ilustra la interacción dinámica entre los componentes del sistema, desde el escaneo del QR hasta el registro en la base de datos.

El corazón del sistema es su base de datos, implementada en PostgreSQL. El Diagrama MER que ven en pantalla representa visualmente la estructura relacional, garantizando la integridad y eficiencia de los datos. Contamos con tablas clave como 'estudiantes', 'padres', 'madres', 'profesores', y tablas pivote que gestionan las relaciones complejas, como la vinculación de estudiantes a períodos escolares o el registro de llegadas tarde.

El sistema se organiza en módulos operativos clave, cada uno diseñado para una función específica. Desde el módulo de Estudiantes, que abarca desde la inscripción hasta la administración de expedientes, hasta el módulo de Late-Pass para el control de puntualidad. La navegación es intuitiva, con un punto de entrada central en el Dashboard que permite acceder a todas las funcionalidades de manera lógica.

Los resultados de la implementación del SWGA son contundentes y medibles. En el proceso de inscripción, logramos una reducción del tiempo de más del 80% y una disminución drástica en la tasa de errores. El control de Late-Pass pasó de ser un proceso manual ineficiente a uno automatizado, preciso y con trazabilidad completa. En general, el sistema ha optimizado el tiempo administrativo, mejorado la organización, reducido costos y fortalecido la seguridad de los datos, proyectando una imagen de innovación para el CEIA."

---

### Conclusiones (Diapositiva 16 y 17)

"En resumen, este Trabajo Especial de Grado ha validado la necesidad de automatizar los procesos manuales del CEIA, que representaban un obstáculo significativo. Hemos logrado cumplir integralmente con todos los objetivos planteados, transformando y optimizando radicalmente los flujos de trabajo administrativos. La arquitectura del sistema es robusta, segura y escalable, sentando las bases para futuras expansiones. El impacto institucional es claramente positivo, mejorando la eficiencia, reduciendo costos y modernizando la imagen del CEIA.

Este trabajo no solo resuelve un problema específico, sino que demuestra el valor de la ingeniería de software en el ámbito educativo, sentando las bases para la mejora continua en el CEIA. Como toda investigación, existen consideraciones para el futuro. Recomendamos enfáticamente la capacitación continua del personal, la expansión a nuevos módulos como el control de acceso de profesores y vehículos, y un mantenimiento proactivo para asegurar la sostenibilidad y seguridad del sistema a largo plazo."

---

### Recomendaciones (Diapositiva 18)

"Para maximizar el impacto y asegurar la sostenibilidad del SWGA, proponemos las siguientes recomendaciones. Es crucial un plan de capacitación continuo para el personal, así como la expansión del sistema a nuevos módulos, aprovechando su diseño modular. El mantenimiento proactivo y las auditorías de seguridad son esenciales para proteger la inversión y los datos sensibles. Finalmente, la integración con herramientas de comunicación y la recopilación constante de feedback garantizarán que el sistema siga siendo una herramienta valiosa y evolucione con las necesidades del CEIA."

---

### Agradecimiento (Diapositiva 19)

"Con esto, concluyo la presentación de mi Trabajo Especial de Grado. Agradezco sinceramente su atención y estoy a su disposición para cualquier pregunta o comentario que puedan tener. Mi correo electrónico es rogerrcarvajal@gmail.com."
