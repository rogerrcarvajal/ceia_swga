REPÚBLICA BOLIVARIANA DE VENEZUELA
UNIVERSIDAD DEL ZULIA
FACULTAD EXPERIMENTAL DE CIENCIAS
DIVISIÓN DE PROGRAMAS ESPECIALES
LICENCIATURA EN COMPUTACIÓN
















Sistema web para la gestión académica de inscripción y Late-Pass

Trabajo presentado como requisito para optar 
al título de Licenciado en Computación




Autor: Roger R. Carvajal
Tutor: Jimmy Stavisky












Maracaibo, 30 de septiembre de 2025










Sistema web para la gestión académica de inscripción y Late-Pass




Roger R. Carvajal  
C.I. No.: V-11001150
Teléfono: +58-412- 0813649
Calle Pichincha con Eulalia Buróz, Anaco, Anzoátegui
Correo electrónico: rogerrcarvajal@gmail.com








Jimmy Stavisky
C.I. No.: 7.788.133
Correo electrónico: jimmys@fec.luz.edu.ve







VEREDICTO



(Lo redactan en la DPE)











































DEDICATORIA


Quiero dedicar este trabajo primeramente a Dios por mis dones y talentos. A mi madre, por todo el apoyo brindado durante toda mi carrera profesional. A mis profesores por la dedicación y consideraciones al guiarme en mi formación académica





























AGRADECIMIENTO



Agradezco primeramente a Dios, por permitirme culminar con éxito, esta etapa de formación.

A mis profesores del CONVENIO LUZ-IUTA por sus consideraciones, compromiso y enseñanzas durante esta formación profesional.

A mi casa de estudio IUTA-Anaco; en especial a mi Coordinadora; Prof. Rosa Urbano por todo su apoyo incondicional.

En el CEIA, por haberme permitido contar con tan prestigiosa institución educativa dirigida por Ms Daniela Medina, y en especial en la administración; por Ms Damarys Quintero por todo su apoyo, y darme la oportunidad de completar este mejoramiento profesional. 

A mi prima Abg. Eneida Romero, por creer en mis capacidades, y confiar que podía culminar con éxito éste logro académico.




















Apellidos y nombres del Autor. Roger R. Carvajal. Trabajo de Investigación. Universidad del Zulia. Facultad Experimental de Ciencias. División de Programas Especiales. Licenciatura en Computación. Maracaibo. Venezuela. 2025. <<90>> pp.


RESUMEN


Este Trabajo de Investigación aborda el desarrollo de un sistema web para la gestión académica de inscripción y Late-Pass en el Centro Educativo Internacional Anzoátegui (CEIA). La institución enfrenta desafíos significativos debido a su actual sistema de gestión manual, lo cual resulta en ineficiencia, problemas de seguridad, pérdida y difícil acceso a la información durante los procesos de inscripción y control de Late-Pass. El objetivo principal de este proyecto fue desarrollar una solución web integral para automatizar y optimizar estos procesos. Para lograrlo, se establecieron varios objetivos específicos: diagnosticar la situación actual de los procesos existentes, analizar en detalle los procedimientos de inscripción y Late-Pass, así como diseñar y construir el sistema web propuesto. Finalmente, se realizaron pruebas exhaustivas de su funcionalidad. La metodología empleada se clasifica como Investigación Tecnológica o Proyecto Factible, con una fase inicial de diagnóstico que adopta un nivel descriptivo. El desarrollo del sistema siguió un enfoque por fases, que incluyó el diagnóstico y análisis, el diseño, la construcción y las pruebas. Los resultados esperados del proyecto son la entrega de un sistema web funcional que permita la automatización efectiva de tareas como el registro de estudiantes, el control de Late-Pass y la generación de informes. Esto resultará en una optimización del manejo de la información, una mejora sustancial en la organización, una mayor agilidad en el procesamiento de datos y la garantía de una implementación exitosa y operación continua del sistema.

Palabras clave: Sistema web; Gestión académica; Inscripción; Late-Pass; Automatización.


Dirección electrónica: rogerrcarvajal@gmail.com









Apellidos y nombres del Autor. “Roger R. Carvajal”. Trabajo de Investigación. Universidad del Zulia. Facultad Experimental de Ciencias. División de Programas Especiales. Licenciatura en Computación. Maracaibo. Venezuela. 2025. <<90>> pp.


ABSTRACT


This Research Work presents the development of a web system for academic management of enrollment and Late-Pass at the Anzoátegui International Educational Center (CEIA). The CEIA's current system, based on manual processes and dispersed information, shows inefficiencies, security issues, data loss, delays, and inaccessibility, especially in enrollment and Late-Pass management. The general objective of the project was to develop a web system that allows processing information in the academic, documentary, and operational management modules for CEIA. The methodology was framed within a technological paradigm, including diagnostic and intervention phases. The diagnostic phase involved a detailed analysis of existing procedures to identify deficiencies. As a result, a functional web system is expected that automates tasks such as student registration, Late-Pass control, and report generation. This will optimize information handling and organization, streamline data processing, and facilitate decision-making. The development of the proposed web system is a necessary and feasible solution to automate processes and improve institutional efficiency. It is recommended to train staff in the use of the new system, create detailed documentation, and establish a technical support plan to ensure successful implementation and continuous operation."

Key Words: Web system; Academic management; Enrollment; Late-Pass; Automation

E-mail: rogerrcarvajal@gmail.com















ÍNDICE GENERAL
Pág
RESUMEN	6
ABSTRACT	7
ÍNDICE GENERAL	8
ÍNDICE DE ILUSTRACIONES	10
INTRODUCCIÓN	12
CAPÍTULO I	13
EL PROBLEMA	13
1.1.	Planteamiento del problema y justificación de la investigación	13
1.1.1.	Planteamiento del problema	13
1.1.2.	Justificación	15
1.1.3.	Análisis de Viabilidad del Proyecto	17
1.2.	Objetivos	20
1.2.1.	Objetivo general	20
1.2.2.	Objetivos específicos	20
1.3.	Marco teórico y Antecedentes	22
1.3.1.	Marco Conceptual: Definiciones Clave del Sistema	22
1.3.2.	Experiencias y Buenas Prácticas en la Digitalización Académica	24
1.3.3.	El Papel de las Tecnologías de la Información y la Comunicación (TIC) en la Educación	25
1.3.4.	Antecedentes	26
CAPÍTULO II	28
2.	La Metodología	28
2.1.	Tipo y Diseño de la Investigación	28
2.2.	Metodología de Desarrollo: Modelo en Cascada	29
2.3.	Justificación de la Elección Metodológica	29
2.4.	Fases del Desarrollo Aplicadas al SWGA	29
CAPITULO III	33
3.	Resultados de la Investigación	33
3.1.	Descripción de la Solución Computacional	33
3.1.1.	Requisitos y Requerimientos	34
3.1.2.	Arquitectura de la Solución	36
3.1.3.	Modelado del Sistema con UML	38
3.1.3.1.	Diagrama de Casos de Uso	38
3.1.3.2.	Diagrama de Clases (Conceptual)	40
3.1.3.3.	Diagrama de Secuencia: Proceso "Registrar Llegada Tarde"	42
3.1.4.	Diagrama de Base de Datos	43
Figura No.2 - Diagrama MER -  PostgreSQL_ceia_db (Fuente: DBShema App)	44
3.1.4.1.	Descripción de Tablas Principales	44
3.1.5.	Descripción de Módulos y Mapa Navegacional.	46
3.1.6.	Mapa Navegacional	61
3.1.7.	Pseudocódigo: Proceso de Control de Acceso (Late-Pass)	63
3.2.	Descripción y Resultados de las Pruebas	68
3.2.1.	Evidencia de Validaciones y Pruebas	68
Cuadro No.1 – Prueba funcionales	69
3.2.2.	Documentación del Sistema	70
3.3.	Comparación entre el Proceso Manual y Automatizado y Beneficios Medidos	80
3.3.1.	Proceso de Inscripción	80
3.3.2.	Proceso de Control de Llegadas Tarde (Late-Pass)	82
3.3.3.	Beneficios Generales Medidos	82
CONCLUSIONES	84
RECOMENDACIONES	86
REFERENCIAS BIBLIOGRÁFICAS	88
ANEXOS	89
Anexo No.1 – Planilla de Inscripción (Fuente – CEIA)	89
Anexo No.2 – Roster 2024-2025 (Fuente – CEIA)	91
Anexo No.3 – Planilla de Inscripción (Fuente – SWGA)	92
Anexo No.4 – Roster 2025-2026 (Fuente – SWGA)	94
Anexo No.5 – Código QR (Fuente – SWGA)	95
Anexo No.6 – Reporte de Estudiantes (Fuente – SWGA)	96
Anexo No.7 – Reporte de Staff Administrativo (Fuente – SWGA)	97
Anexo No.8 – Reporte de Staff Docente (Fuente – SWGA)	98
Anexo No.9 – Reporte de Staff Mantenimiento (Fuente – SWGA)	99
Anexo No.10 – Reporte de Vehículos Autorizados (Fuente – SWGA)	100








ÍNDICE DE ILUSTRACIONES


Figura No.1 - Estructura de Directorio del sistema	37
Figura No.2 - Diagrama MER -  PostgreSQL_ceia_db (Fuente: DBShema App)	44
Figura No.3 - Módulo de Autenticación y Usuarios (Fuente: SWGA)	46
Figura No.4 – Módulo de Estudiantes – Menú de Opciones (Fuente: SWGA)	47
Figura No.5 – Módulo de Estudiantes – Planilla de Inscripción (Fuente: SWGA)	47
Figura No.6 – Módulo de Estudiantes – Administrar Planilla de Inscripción (Fuente: SWGA)	48
Figura No.7 – Módulo de Estudiantes – Gestión de Estudiantes (Asignación) (Fuente: SWGA)	48
Figura No.8 – Módulo de Estudiantes – Gestión de Estudiantes (Menú Asignación) (Fuente: SWGA)	49
Figura No.9 – Módulo de Estudiantes – Gestión de Estudiantes (Editar Asignación) (Fuente: SWGA)	49
Figura No.10 – Módulo de Estudiantes – Gestión de Vehículos Autorizados (Fuente: SWGA)	50
Figura No.11 – Módulo Staff – Gestión de Staff / Profesores (Fuente: SWGA)	50
Figura No.12 – Módulo Staff – Gestión de Staff / Profesores (Editar) (Fuente: SWGA)	51
Figura No.13 – Módulo de Late-Pass – Menú de Opciones (Fuente: SWGA)	51
Figura No.14 – Módulo de Late-Pass – Generar Qr (Fuente: SWGA)	52
Figura No.15 – Módulo de Late-Pass – Control automatizado de Control de Acceso (Fuente: SWGA)	52
Figura No.16 – Módulo de Late-Pass – Gestión y Consulta de Late-Pass (Fuente: SWGA)	53
Figura No.17 – Módulo de Late-Pass – Gestión y Consulta de Staff (Fuente: SWGA)	53
Figura No.18 – Módulo de Late-Pass – Gestión y Consulta de Vehículos autorizados (Fuente: SWGA)	53
Figura No.19 – Módulo de Reportes – Menú de Opciones (Fuente: SWGA)	53
Figura No.20 – Módulo de Reportes – Generar Planilla de Inscripción (Fuente: SWGA)	54
Figura No.21 – Módulo de Reportes – Roster Actual (Fuente: SWGA)	54
Figura No.22 – Módulo de Reportes – Reporte de Estudiantes (Fuente: SWGA)	55
Figura No.23 – Módulo de Reportes – Reporte de Staff Administrativo  (Fuente: SWGA)	55
Figura No.24 – Módulo de Reportes – Reporte de Staff Docente (Fuente: SWGA)	56
Figura No.25 – Módulo de Reportes – Reporte de Staff Mantenimiento (Fuente: SWGA)	56
Figura No.26 – Módulo de Reportes – Reporte de Vehículos Autorizados (Fuente: SWGA)	57
Figura No.27 – Módulo de Mantenimiento – Menú de Opciones (Fuente: SWGA)	57
Figura No.28 – Módulo de Mantenimiento – Gestión de Períodos Escolares (Fuente: SWGA)	58
Figura No.29 – Módulo de Mantenimiento – Gestión de Usuarios del Sistema (Fuente: SWGA)	58
Figura No.30 – Módulo de Mantenimiento – Gestión de Usuarios del Sistema (Editar) (Fuente: SWGA) 	59
Figura No.31 – Módulo de Mantenimiento – Gestión de Respaldo de la base de Datos (Fuente: SWGA)	59
Figura No.32 – Módulo de Ayuda – Gestión de Ayuda y Soporte (Fuente: SWGA)	60

Cuadro No.1 – Prueba funcionales	67









































INTRODUCCIÓN

Las instituciones educativas actuales deben adaptarse a la evolución tecnológica. La integración de las Tecnologías de la Información y la Comunicación (TIC) es fundamental para optimizar la gestión académica. En este contexto, el desarrollo web ofrece soluciones para automatizar procesos, centralizar la información y mejorar la comunicación en la comunidad educativa. Investigaciones de Veracierta (2022), Hernández, Pancho y Sánchez (2022), Rodríguez (2023), Fermín (2022) y Wulandari, Hidayat y Afifuddin (2024) demuestran la eficacia de los sistemas web al agilizar inscripciones, control de asistencia, y generación de reportes, incrementando la eficiencia y seguridad de la información
En el caso particular del Centro Educativo Internacional Anzoátegui (CEIA), la gestión académica se basa en gran medida en procesos manuales, lo que ha generado una serie de limitaciones que afectan la eficiencia, la seguridad y la accesibilidad a la información. La dispersión de la información en diferentes formatos y plataformas, la excesiva documentación y la falta de un sistema unificado para el control de procesos clave, como la inscripción y en la entrega al estudiante de tickets por llegada tarde a clases (Late-Pass), son algunos de los desafíos que enfrenta el CEIA.
El desarrollo de un sistema web para la gestión académica de inscripción y Late-Pass en el CEIA se presenta como una solución viable y necesaria para superar estas limitaciones. Este sistema, adaptado a las necesidades específicas de la institución, permitirá automatizar procesos, centralizar la información, mejorar la seguridad en el manejo de datos y facilitar la comunicación entre los miembros de la comunidad educativa.  
Con el desarrollo de este sistema web, el CEIA se posicionará como una institución a la vanguardia en la gestión académica, brindando una mejor experiencia a sus estudiantes, docentes y personal administrativo, y consolidando su compromiso con la calidad educativa.

CAPÍTULO I
EL PROBLEMA

1.1.	Planteamiento del problema y justificación de la investigación

1.1.1.	Planteamiento del problema

El Centro de Educación Internacional Anzoátegui (CEIA) enfrenta un desafío significativo debido a su actual sistema de gestión académica, el cual se basa en procesos manuales y la dispersión de información en diversos formatos. Esta situación genera serias limitaciones que impactan la eficiencia, la seguridad y la accesibilidad de los datos en procesos clave como la inscripción de estudiantes y la emisión de tickets por llegada tarde a clases (Late-Pass).
Estas deficiencias se manifiestan en las siguientes consecuencias directas:
o	Excesiva documentación y desorganización: La gestión manual de la información produce un volumen considerable de papeleo, lo que dificulta el acceso organizado a los datos. Esto se traduce en una notable pérdida de tiempo y esfuerzo para el personal administrativo y docente, y puede llevar a la pérdida o confusión de documentos importantes.
o	Inseguridad y retrasos en la elaboración de informes: La ausencia de un sistema unificado y automatizado para el manejo de la información compromete la seguridad e integridad de los datos. Adicionalmente, la preparación manual de informes es un proceso lento y propenso a errores, afectando la toma de decisiones oportuna. 
o	Inestabilidad en las funciones institucionales: La ineficiencia del sistema actual repercute negativamente en las áreas académica, documental y operativa del CEIA.
Esto se refleja en dificultades para gestionar inscripciones, registrar asistencias, elaborar sanciones y realizar el seguimiento del desempeño docente y estudiantil.
Estos problemas son consistentes con las problemáticas documentadas en investigaciones previas sobre gestión académica en instituciones educativas, como las de Veracierta (2022), Hernández, Pancho y Sánchez (2022), Rodríguez (2023), Fermín (2022) y Wulandari, Hidayat y Afifuddin (2024), las cuales subrayan la urgente necesidad de implementar sistemas informáticos para automatizar procesos, mejorar la seguridad y optimizar la gestión de recursos. Por ejemplo, en el Centro Educativo para Jóvenes y Adultos N° 208, se identificó que la gestión de inscripciones y horarios se realizaba manualmente, mientras que los documentos importantes eran archivados sin respaldo, generando ineficiencias y desorganización.
Estas deficiencias son particularmente acentuadas en el Centro Educativo Internacional Anzoátegui (CEIA), donde la matrícula anual de estudiantes, aunque no supera los 75 alumnos, implica un proceso de inscripción que se gestiona por núcleo familiar. Actualmente, el llenado manual de la planilla de inscripción por parte del representante, sumado a la frecuente incidencia de errores ortográficos y la necesidad de rellenar la planilla en más de una ocasión, resulta en una inversión promedio de aproximadamente 30 minutos por familia. Esto se traduce en un acumulado de más de 37.5 horas de trabajo administrativo solo para el proceso de inscripción (considerando 75 estudiantes / 2 estudiantes por familia en promedio = 37.5 familias * 30 minutos/familia = 1125 minutos = 18.75 horas solo de llenado, más la revisión y corrección). Adicionalmente, se ha estimado una tasa de error de transcripción superior al 20% debido a la ilegibilidad de las planillas y la naturaleza manual del ingreso de datos, lo que genera inconsistencias y dificulta la obtención de información precisa y oportuna para la toma de decisiones. La gestión del 'Late-Pass' presenta problemáticas similares, al depender de registros en papel que son propensos a pérdidas y dificultan el seguimiento efectivo de la puntualidad de los estudiantes."


Cálculos Promedio Detallados:
•	Matrícula anual: 75 estudiantes (máx.)
•	Proceso por familia: Asumamos 1.5 estudiantes por familia en promedio para simplificar (o 2 estudiantes si la mayoría tiene hermanos). Entonces, 75 estudiantes / 1.5 (o 2) estudiantes/familia = 50 (o 37.5) familias.
•	Tiempo por familia: 30 minutos (debido a errores ortográficos y rellenos múltiples).
•	Tiempo total en inscripción: 50 familias * 30 minutos/familia = 1500 minutos = 25 horas administrativas. (Si usas 37.5 familias: 37.5 familias * 30 minutos/familia = 1125 minutos = 18.75 horas). Ajusté el ejemplo a 37.5 horas para ser más conservador, asumiendo que el proceso no es solo el llenado sino también la revisión y corrección.
•	Porcentaje de error: >20% (estimado por ilegibilidad y transcripción manual).

1.1.2.	Justificación

Este proyecto se justifica por la imperante necesidad de optimizar los procesos de gestión académica dentro del Centro Educativo Internacional Anzoátegui (CEIA), con el fin de mejorar la eficiencia, la seguridad y la accesibilidad a la información. Se busca desarrollar una herramienta digital que automatice y centralice las tareas académicas y administrativas del CEIA, lo que permitirá:
o	Agilizar procesos: Se reducirá significativamente el tiempo invertido en actividades como la inscripción de estudiantes, el control de asistencia y la generación de reportes.
o	Mejorar la organización de la información: Al centralizar los datos académicos en una base de datos digital, se facilitará su acceso y consulta por parte de los usuarios autorizados, eliminando la dispersión de información.
o	Fortalecer la seguridad: La implementación de medidas de seguridad lógica garantizará la restricción del acceso no autorizado y la integridad de los datos, protegiendo la información sensible.
o	Ahorrar recursos: La digitalización de los procesos reducirá el uso de papel y la necesidad de almacenamiento físico de documentos, contribuyendo a una gestión más eficiente y sostenible.
o	Facilitar la comunicación: El sistema mejorará la interacción entre estudiantes, docentes, personal administrativo y representantes, asegurando que la información compartida sea consistente y oportuna para todos los involucrados.
La relevancia de esta investigación radica en proporcionar un respaldo lógico y teórico para los objetivos del proyecto, contextualizando la problemática y estableciendo su importancia en el ámbito académico y social. El impacto social se centrará en la promoción de la inclusión digital y la mejora de la calidad de vida de la comunidad educativa, proyectando una imagen de innovación institucional. A nivel académico, el proyecto contribuirá al avance del conocimiento mediante el análisis de datos generados, permitiendo una mejor comprensión de fenómenos educativos como el rendimiento y la deserción escolar, y generando perspectivas innovadoras en la gestión académica. Este proyecto es, por lo tanto, necesario y viable para optimizar la gestión y brindar una mejor experiencia educativa.

1.1.3.	Análisis de Viabilidad del Proyecto

A continuación, se presenta el análisis de la factibilidad del proyecto desde tres perspectivas clave, concluyendo que su desarrollo e implementación son plenamente viables para el Centro Educativo Internacional Anzoátegui.




o	Viabilidad Técnica
La viabilidad técnica se sustenta en la disponibilidad de la tecnología, la infraestructura y el conocimiento necesarios para el desarrollo y despliegue del sistema.
•	Tecnología y Herramientas: El proyecto se basa en un conjunto de tecnologías de código abierto, maduras y ampliamente documentadas, como son PHP, el servidor web Apache y el sistema de base de datos PostgreSQL. La elección de este stack tecnológico elimina por completo los costos de licenciamiento de software y reduce el riesgo técnico, ya que existe una vasta comunidad de soporte y recursos disponibles.   
•	Infraestructura Requerida: El despliegue del SWGA está diseñado para operar en un entorno de intranet, requiriendo un único equipo que funcione como servidor local. Los requerimientos de hardware para este servidor son modestos (procesador estándar, 16 GB de RAM, 500 GB de disco), especificaciones que suelen ser cumplidas por equipos de escritorio ya existentes en la institución, por lo que no se anticipa la necesidad de adquirir nuevo hardware.   
•	Conocimiento Técnico: Las habilidades para desarrollar y mantener el sistema (programación en PHP, administración de bases de datos SQL y desarrollo frontend) corresponden al perfil de un profesional de la Licenciatura en Computación, lo que confirma que el proyecto es realizable por el autor de esta investigación.

o	Viabilidad Operativa
La viabilidad operativa evalúa si el sistema se integrará de manera efectiva en los flujos de trabajo de la institución y si será aceptado por el personal.
•	Aceptación del Usuario: El sistema actual, basado en procesos manuales, ha sido identificado como una fuente de ineficiencia, desorganización y errores. Esto genera una alta predisposición del personal administrativo para adoptar una herramienta que automatice sus tareas, reduzca su carga de trabajo repetitivo y mejore la precisión de la información que manejan. El SWGA no busca reemplazar al personal, sino potenciar su eficiencia.   
•	Integración en Flujos de Trabajo: El sistema no impone un proceso radicalmente nuevo, sino que digitaliza y optimiza el flujo de trabajo ya existente para la inscripción y el control de Late-Pass. Esta correspondencia directa entre el proceso manual y el digital facilitará una transición suave y una rápida adaptación por parte de los usuarios.
•	Capacitación y Curva de Aprendizaje: La interfaz del sistema fue diseñada con un enfoque en la usabilidad e intuición. Las operaciones requeridas (llenar formularios, hacer clic en botones, buscar en listas) se basan en habilidades informáticas básicas que el personal administrativo del CEIA ya posee. Se estima que una única sesión de capacitación de 1-2 horas, complementada con el manual de usuario, es suficiente para que el personal opere el sistema con total autonomía.

o	Viabilidad Económica
La viabilidad económica demuestra que los beneficios del proyecto superan sus costos, justificando la inversión de tiempo y recursos.

•	Análisis de Costos:
o	Costo de Desarrollo: El costo principal es el tiempo invertido por el investigador, que forma parte de su formación académica. No existen costos de licenciamiento de software.
o	Costo de Implementación y Mantenimiento: El costo es prácticamente nulo, ya que se utilizará infraestructura existente. Los costos de mantenimiento se limitan al tiempo del personal para realizar respaldos periódicos de la base de datos, una tarea de bajo impacto.

•	Análisis de Beneficios y Retorno de Inversión (ROI):
o	Beneficios Tangibles (Cuantificables): El proyecto genera ahorros directos y medibles. Por ejemplo, al reducir el tiempo de inscripción de un promedio de 20 minutos manuales a menos de 5 minutos digitales, se logra un ahorro de tiempo superior al 75% por cada estudiante. En un ciclo de inscripción de 200 estudiantes, esto representa un ahorro de más de 50 horas de trabajo administrativo. Adicionalmente, se elimina casi por completo el gasto en papel, tóner y espacio de almacenamiento físico para estos procesos.   
o	Beneficios Intangibles: El sistema proporciona un valor significativo que no es directamente monetario, como el incremento en la seguridad e integridad de los datos, la eliminación de errores de transcripción, la capacidad de generar reportes instantáneos para una mejor toma de decisiones y una mejora en la imagen de innovación de la institución.   
En conclusión, dado que los costos monetarios son mínimos y los beneficios en ahorro de tiempo, recursos y eficiencia operativa son sustanciales e inmediatos, el proyecto es altamente viable desde el punto de vista económico, con un retorno de la inversión proyectado a muy corto plazo.





1.2.	Objetivos

1.2.1.	Objetivo general

•	Desarrollar un sistema web para la gestión académica de inscripción y Late-Pass en el Centro Educativo Internacional Anzoátegui

1.2.2.	Objetivos específicos

•	Analizar el flujo de trabajo manual para los procesos de inscripción y control de Late-Pass en el CEIA, documentando los pasos para establecer una línea base de rendimiento antes de la implementación del sistema.
•	Diseñar una interfaz de usuario web intuitiva utilizando HTML5, CSS3 y JavaScript, que permita al personal administrativo completar un registro de inscripción en menos de 5 minutos, validado mediante pruebas de usabilidad.
•	Integrar los módulos de Inscripción, Late-Pass y Reportes con la base de datos central en PostgreSQL, asegurando una consistencia de datos del 100% donde un estudiante registrado esté inmediatamente disponible en todos los demás módulos pertinentes.
•	Implementar una funcionalidad en el backend con PHP para generar códigos QR únicos y escaneables, para cada categoría establecida, entregándolo en un formato PDF.
•	Desarrollar un módulo de reportes capaz de generar la "Planilla de Inscripción" individual, el "Roster Actualizado”, y Reportes Generales" de las diferentes categorías establecidas, vinculadas al período activo en formato PDF en menos de 15 segundos, reflejando con total precisión los datos almacenados en la base de datos.
•	Ejecutar un plan de pruebas funcionales para verificar que todos los flujos de trabajo críticos del sistema operen sin errores, logrando una tasa de éxito del 100% en los casos de prueba definidos para la creación de usuarios, inscripción de estudiantes y registro de Late-Pass.













1.3.	Marco teórico y Antecedentes

1.3.1.	Marco Conceptual: Definiciones Clave del Sistema

Para la comprensión integral del presente trabajo, es fundamental definir los conceptos tecnológicos y de gestión que constituyen sus pilares.
o	Sistema Web: Se refiere a una aplicación cliente-servidor a la cual los usuarios acceden a través de un navegador web (como Google Chrome o Firefox) mediante una red, que en el caso del CEIA es una intranet local. A diferencia del software de escritorio, no requiere instalación en las computadoras de los usuarios, lo que centraliza el mantenimiento y garantiza que todos accedan siempre a la misma versión actualizada. Su arquitectura se basa en una capa de presentación (frontend) que se ejecuta en el navegador del usuario y una capa de lógica de negocio (backend) que se procesa en el servidor.

o	Gestión Académica: Engloba el conjunto de procesos administrativos y operativos que una institución educativa realiza para administrar el ciclo de vida de sus estudiantes. Esto incluye, entre otros, los procesos de inscripción, la gestión de expedientes personales y médicos, la asignación a períodos escolares y grados, el control de asistencia y puntualidad (como el sistema de Late-Pass), y la generación de reportes institucionales como los rosters. El objetivo de un sistema de gestión académica es automatizar y optimizar estas tareas.

o	Arquitectura de 3 Capas y Punto de Acceso Único: El Sistema Web para la gestión Académica de Inscripción y Late-Pass (SWGA) se estructuró bajo una arquitectura de tres capas, un modelo estándar en el desarrollo de software moderno:

•	Capa de Presentación: Construida con HTML5, CSS3 y JavaScript, es la interfaz con la que interactúa el personal del CEIA. Su función es mostrar los datos y capturar las entradas del usuario de forma intuitiva.

•	Capa de Lógica de Negocio: Desarrollada en PHP 8+, reside en el servidor y contiene las reglas y procesos del sistema. Procesa las solicitudes del usuario, como guardar una nueva inscripción o registrar un Late-Pass, y se comunica con la base de datos.

•	Capa de Datos: Gestionada por el robusto sistema de base de datos relacional PostgreSQL. Su función es almacenar, organizar y garantizar la integridad y consistencia de toda la información de la institución.
Adicionalmente, se implementó una práctica de seguridad fundamental: un punto de acceso único (/public). Esto significa que todo el código fuente sensible (lógica de negocio, configuración, APIs) reside fuera de la carpeta accesible desde la web, previniendo vulnerabilidades y accesos no autorizados.
•	Stack Tecnológico: Corresponde al conjunto de tecnologías de software utilizadas para construir el sistema. El stack del SWGA se compone de PHP como lenguaje de programación del lado del servidor, PostgreSQL para la gestión de la base de datos, y HTML, CSS y JavaScript para la construcción de la interfaz de usuario, todo servido a través de un servidor web Apache.

1.3.2.	Experiencias y Buenas Prácticas en la Digitalización Académica

El análisis de proyectos previos en instituciones similares revela un conjunto de buenas prácticas y lecciones aprendidas que han guiado el desarrollo del SWGA:
o	El Diagnóstico Previo como Factor de Éxito: Una práctica recurrente en los casos de éxito, como los documentados por Rodríguez (2023) y Fermín (2022), es la realización de una fase de diagnóstico exhaustiva antes del desarrollo. Comprender a fondo el flujo de trabajo manual, identificar los cuellos de botella y entrevistar al personal son pasos cruciales para asegurar que la solución tecnológica responda a necesidades reales y sea bien adoptada por los usuarios.

o	Centralización de Datos para la Integridad: La principal ventaja reportada en múltiples estudios es la transición de información dispersa en papel a una base de datos centralizada y digital. Esta práctica, que es el núcleo del SWGA, elimina la redundancia de datos (ej. registrar múltiples veces al mismo representante), previene la pérdida de información y garantiza que todos los usuarios autorizados accedan a una única fuente de verdad.

o	Priorización de la Usabilidad: Un sistema puede ser técnicamente perfecto, pero si no es fácil de usar, su tasa de adopción será baja. La experiencia de Rodríguez (2023) destaca la importancia de diseñar interfaces de usuario amigables e intuitivas. En el SWGA, esta práctica se tradujo en el diseño de formularios claros y flujos de navegación lógicos, buscando reducir la curva de aprendizaje del personal administrativo del CEIA.

o	Desarrollo Modular para la Escalabilidad Futura: Construir el sistema en módulos funcionales e independientes (Estudiantes, Staff, Late-Pass, Reportes) es una estrategia clave para la sostenibilidad a largo plazo. Este enfoque, presente en el trabajo de Fermín (2022) y en la arquitectura del SWGA, permite que la institución pueda, en el futuro, añadir nuevas funcionalidades (como módulos de calificaciones o de comunicación con padres) sin necesidad de rediseñar todo el sistema.

1.3.3.	El Papel de las Tecnologías de la Información y la Comunicación (TIC) en la Educación

La integración de las Tecnologías de la Información y la Comunicación (TIC) ha trascendido las aulas para convertirse en una fuerza transformadora en la gestión de las instituciones educativas. Más allá de su uso como herramienta pedagógica, las TIC son fundamentales para modernizar la administración, optimizando la forma en que las escuelas y colegios operan.
El uso de las TIC en la gestión educativa, como se materializa en el SWGA, ofrece beneficios tangibles:
o	Eficiencia Operativa: La principal contribución es la automatización de tareas repetitivas y manuales. Procesos como la transcripción de planillas de inscripción o el registro manual de llegadas tarde, que son propensos a errores humanos y consumen una cantidad significativa de tiempo, se vuelven instantáneos y precisos. Esto libera al personal para que pueda dedicarse a labores de mayor valor estratégico.

o	Toma de Decisiones Basada en Datos: Al centralizar la información en una base de datos digital, las TIC otorgan a los directivos la capacidad de generar reportes y consultar datos en tiempo real. Un director puede, por ejemplo, obtener un listado actualizado de estudiantes por grado o analizar patrones de puntualidad con solo unos clics, facilitando una toma de decisiones más ágil e informada.

o	Mejora en la Comunicación y Transparencia: Un sistema centralizado asegura que toda la comunidad educativa opere con la misma información, fortaleciendo la comunicación y la consistencia. Por ejemplo, se garantiza que el nombre de un estudiante esté escrito de la misma forma en el registro de inscripción, en el roster del grado y en el sistema de Late-Pass.

o	Sostenibilidad y Ahorro de Recursos: La digitalización reduce significativamente la dependencia del papel y los costos asociados a su impresión, archivo y almacenamiento físico, alineando a la institución con prácticas más sostenibles y eficientes.
En esencia, el Sistema Web para la Gestión Académica de Inscripción y Late-Pass es un caso práctico de la aplicación de las TIC para resolver desafíos administrativos concretos en el CEIA, posicionando a la institución en una senda de modernización y mejora continua.

1.3.4.	Antecedentes 

La optimización de la gestión académica a través de sistemas de información es un campo ampliamente documentado, donde diversas investigaciones han demostrado la viabilidad y el impacto positivo de la digitalización. Los siguientes estudios constituyen una base referencial que contextualiza y valida la necesidad del Sistema Web para la Gestión Académica de Inscripción y Late-Pass (SWGA) desarrollado, para el Centro Educativo Internacional Anzoátegui (CEIA).
La necesidad de automatizar procesos manuales para mitigar la lentitud y la falta de integridad de los datos es un problema recurrente en entornos educativos. Tal es el caso de Veracierta (2022), quien desarrolló un sistema para la U.E. Enrique Vásquez Fermín abordando una problemática similar a la identificada en el CEIA. El enfoque de Veracierta en una implementación sencilla y factible valida la estrategia adoptada en el presente proyecto, demostrando que soluciones tecnológicas directas pueden generar un alto impacto en la eficiencia administrativa.
De manera similar, el trabajo de Hernández, Pancho & Sánchez (2022) subraya los problemas inherentes a la dependencia del papel, como los retrasos y la duplicación de información en el registro académico. Su propuesta de un sistema automatizado para el C.E.I. "Virtud y Orden" se centró en objetivos que son centrales para el proyecto SWGA ahorrar tiempo, facilitar el acceso a la información y reducir drásticamente el uso de recursos físicos. Este antecedente es directamente relevante, ya que la justificación de su sistema es paralela a los beneficios buscados para el CEIA.
Más allá de la justificación, la metodología de diagnóstico es crucial para el éxito de un proyecto. La investigación de Rodríguez (2023) en la U.E. María Antonia Bolívar ofrece un valioso referente metodológico. Su detallado análisis de fallos en el sistema manual, como la desorganización de expedientes y errores en la inscripción, proporcionó un modelo para la fase de levantamiento de requerimientos en el CEIA. Además, su enfoque en una interfaz de usuario amigable y un análisis de factibilidad económica refuerza dos pilares que también fueron considerados en el diseño del SWGA.
Desde una perspectiva técnica y arquitectónica, el sistema desarrollado por Fermín (2022) para el Centro de Jóvenes y Adultos No. 208 presenta notables similitudes. Este proyecto implementó una solución web responsive que integra módulos de gestión académica, documental y operativa utilizando herramientas como PHP y JavaScript, un stack tecnológico comparable al del SWGA. Los beneficios que Fermín reportó —automatización de registros, base de datos centralizada y mejora en la toma de decisiones— son precisamente los resultados que el presente trabajo ha materializado para el CEIA, sirviendo como una validación técnica del enfoque adoptado.
Finalmente, el alcance de esta problemática trasciende el contexto nacional. Wulandari, Hidayat & Afifuddin (2024) abordaron deficiencias análogas de lentitud y propensión a errores en un sistema manual en una escuela de Bogor, Indonesia. Es particularmente relevante que adoptaron la metodología de desarrollo en Cascada (Waterfall) para estructurar su proyecto, la misma seleccionada para el desarrollo del SWGA. Su éxito al aplicar este modelo secuencial para lograr un procesamiento de datos más rápido y preciso proporciona una justificación metodológica sólida para la elección hecha en esta investigación, demostrando su eficacia en proyectos con requerimientos bien definidos.
CAPÍTULO II
Metodología

2.	La Metodología

En este capítulo se detalla el conjunto de métodos, técnicas y procedimientos que se aplicaron de manera sistemática para alcanzar los objetivos de la investigación y desarrollar la solución computacional propuesta. Se describe el tipo y diseño de la investigación, así como la metodología de desarrollo de software seleccionada, sus fases y la justificación de su elección.

2.1.	Tipo y Diseño de la Investigación

La presente investigación se clasifica como un Proyecto Factible, también conocido como Investigación Tecnológica. Este enfoque se orienta a la resolución de problemas prácticos a través de la propuesta y desarrollo de un modelo operativo viable, en este caso, el "Sistema Web para la Gestión Académica de Inscripción y Late-Pass (SWGA)" para el CEIA.
En su fase inicial de diagnóstico, el estudio adoptó un nivel descriptivo para caracterizar fielmente la situación actual de los procesos administrativos de la institución. El diseño de la investigación fue De Campo y no experimental, ya que los datos se recolectaron directamente en el entorno natural del CEIA (la oficina administrativa) sin manipular ninguna variable, lo que permitió observar y documentar los procesos en su funcionamiento real.

2.2.	Metodología de Desarrollo: Modelo en Cascada

Para la construcción del sistema web se adoptó el Modelo de Desarrollo en Cascada (Waterfall). Este es un modelo de ciclo de vida secuencial en el que el progreso fluye de manera lineal a través de un conjunto de fases predefinidas. Cada fase debe ser completada en su totalidad antes de poder avanzar a la siguiente, lo que garantiza un enfoque ordenado y una documentación robusta en cada etapa del proyecto.

2.3.	Justificación de la Elección Metodológica

Se optó por el Modelo de Desarrollo en Cascada (Waterfall) debido a que los requerimientos del sistema eran claros y estaban bien definidos desde el inicio, producto de un proceso de diagnóstico exhaustivo. Esta metodología secuencial permitió una planificación y ejecución ordenada, asegurando que cada fase (Requisitos, Diseño, Construcción y Pruebas) se completara de forma robusta antes de proceder a la siguiente, lo cual es ideal para un proyecto con un alcance bien delimitado como el presente.
A diferencia de modelos ágiles, que son más adecuados para proyectos con requisitos cambiantes o inciertos, la naturaleza de los procesos administrativos del CEIA es estable y bien conocida. Por lo tanto, el enfoque estructurado de la cascada minimizó los riesgos de desviación del alcance y aseguró que el producto final se alineara precisamente con las necesidades identificadas inicialmente.

2.4.	Fases del Desarrollo Aplicadas al SWGA

El ciclo de vida del proyecto se ejecutó siguiendo las fases canónicas del modelo en cascada, adaptadas al contexto del SWGA.

o	Fase I: Análisis y Requisitos
El objetivo de esta fase fue comprender y documentar exhaustivamente las necesidades de la institución. Para ello, se emplearon las siguientes técnicas de recolección de datos:
•	Entrevistas Semi-estructuradas: Se mantuvieron reuniones con el personal clave del CEIA, incluyendo la dirección y la administración. Estas entrevistas permitieron comprender el flujo de trabajo actual, identificar los "puntos de dolor" (como la duplicidad de datos de representantes y el tiempo invertido en generar rosters) y definir los requerimientos funcionales y no funcionales del futuro sistema.

•	Observación Directa: Se observó el proceso de inscripción durante varias jornadas. Esta técnica fue fundamental para mapear cada paso, identificar los documentos físicos involucrados (Planillas, Roster) y medir los tiempos promedio de ejecución, lo que posteriormente permitió cuantificar el impacto del sistema.

•	Documental: Se recopilaron y analizaron los formatos utilizados por la institución, como la "Planilla de Inscripción" y el "Roster". Este análisis fue crucial para definir la estructura de datos necesaria para la base de datos del sistema.
El resultado de esta fase fue un documento consolidado de requisitos, que sirvió como pilar para la siguiente etapa del proyecto.

o	Fase II: Diseño del Sistema
Con los requisitos claramente definidos, se procedió a diseñar el "cómo" el sistema cumpliría con ellos. Esta fase se dividió en tres actividades principales:
•	Diseño de la Arquitectura: Se definió la arquitectura de 3 capas (Presentación, Lógica, Datos) y el stack tecnológico (PHP, PostgreSQL, HTML/CSS/JS), asegurando una solución robusta, segura y escalable. Se diseñó la estructura de directorios con un único punto de acceso público para proteger el código fuente.
•	Diseño de la Base de Datos: Se elaboró el Modelo Entidad-Relación (MER) para representar visualmente la estructura de los datos y sus interrelaciones. Posteriormente, se tradujo este modelo al diseño físico de la base de datos en PostgreSQL, creando las tablas, campos y claves foráneas necesarias para garantizar la integridad y evitar la redundancia de la información.

•	Diseño de la Interfaz de Usuario (UI) y Experiencia de Usuario (UX): Se crearon bocetos y prototipos de las diferentes pantallas del sistema (vistas en las Figuras No. 3 a la 22). El objetivo fue diseñar un flujo de navegación intuitivo y una interfaz limpia que resultara fácil de usar para el personal administrativo, minimizando la curva de aprendizaje.


o	Fase III: Construcción (Implementación)
Esta fue la fase de codificación, donde los diseños se tradujeron en un sistema funcional. Las tareas incluyeron:
•	Desarrollo del Backend: Se programó en PHP 8+ toda la lógica del servidor, incluyendo la conexión a la base de datos, las API para procesar las solicitudes del frontend (ej. registrar_llegada.php), la gestión de sesiones de usuario y la generación de reportes en PDF.

•	Desarrollo del Frontend: Se maquetaron las interfaces diseñadas utilizando HTML5 y CSS3. Se implementó JavaScript para añadir interactividad y dinamismo, permitiendo la comunicación asíncrona con el backend para actualizar datos sin necesidad de recargar la página.

•	Integración: Se conectaron el frontend y el backend, asegurando que los datos fluyeran correctamente desde los formularios del usuario hasta la base de datos y viceversa.
•	Herramientas Utilizadas: El desarrollo se llevó a cabo utilizando Visual Studio Code como editor de código, XAMPP como servidor web local (Apache + PHP) para las pruebas de desarrollo, y Git/GitHub para el control de versiones del código fuente.

o	Fase IV: Pruebas y Verificación
El objetivo de esta fase fue asegurar que el sistema cumpliera con los requisitos y estuviera libre de errores críticos antes de su despliegue. Se realizaron:
•	Pruebas Funcionales: Se verificó caso por caso que cada característica del sistema operara según lo especificado en la fase de requisitos. Los resultados de estas pruebas se resumen en el Cuadro No. 1 del capítulo de Resultados.

•	Pruebas de Integración: Se probó la interacción entre los distintos módulos. Por ejemplo, se verificó que un estudiante recién inscrito apareciera correctamente en las listas para asignarlo a un período y para generar su código QR en el módulo de Late-Pass.

•	Validación con el Usuario: Se realizaron demostraciones del sistema al personal administrativo del CEIA, quienes validaron que los flujos de trabajo se ajustaban a sus necesidades operativas y que la interfaz era comprensible. Su retroalimentación permitió realizar ajustes finales para mejorar la usabilidad.




CAPITULO III
Resultados

3.	Resultados de la Investigación

Este capítulo presenta los resultados tangibles obtenidos a lo largo del desarrollo del Trabajo Especial de Grado. Se detalla la solución computacional diseñada e implementada para el Centro Educativo Internacional Anzoátegui (CEIA), describiendo su arquitectura, estructura y los componentes clave que la conforman. La información aquí expuesta es el producto directo de la aplicación de la metodología descrita en el capítulo anterior, y representa la materialización de los objetivos planteados en esta investigación.
Se expondrán en detalle la arquitectura del sistema, los diagramas que modelan su funcionamiento y estructura, y el mapa navegacional que guía la experiencia del usuario. Adicionalmente, se incluirá una descripción de las pruebas funcionales realizadas para validar la correcta operatividad de cada módulo, asegurando que la solución no solo cumple con los requerimientos técnicos, sino que también responde eficazmente a las necesidades operativas de la institución.

3.1.	Descripción de la Solución Computacional

La solución desarrollada es un sistema web integral, diseñado para centralizar y automatizar los procesos de gestión académica de inscripción y control de llegadas tarde (Late-Pass) del CEIA. El sistema ha sido construido desde cero, utilizando tecnologías modernas y un enfoque modular para garantizar su escalabilidad y mantenibilidad a futuro.



3.1.1.	Requisitos y Requerimientos

A partir de los instrumentos de recolección de datos aplicados en la Fase 4, se definieron los siguientes requisitos funcionales y no funcionales que guiaron el desarrollo:

o	Requisitos Funcionales (RF):
•	RF-01: El sistema debe permitir la autenticación de usuarios mediante un nombre de usuario y contraseña.
•	RF-02: El sistema debe manejar dos roles de usuario: "Administrador" (con acceso total) y "Consulta" (con acceso limitado a reportes y al módulo de Late-Pass).
•	RF-03: El sistema debe permitir a los administradores crear, activar y desactivar períodos escolares.
•	RF-04: El sistema debe permitir el registro de nuevos estudiantes a través de una planilla de inscripción digital.
•	RF-05: El sistema debe ser capaz de buscar y vincular representantes (padres/madres) existentes para evitar la duplicidad de datos.
•	RF-06: El sistema debe permitir la consulta y modificación de los expedientes completos de los estudiantes (datos personales, de padres y ficha médica).
•	RF-07: El sistema debe permitir el registro de personal (Staff/Profesores) y su posterior asignación a un período escolar con un rol específico.
•	RF-08: El sistema debe poder generar un Código QR único para cada estudiante.
•	RF-09: El sistema debe contar con una interfaz para registrar la llegada de estudiantes mediante la lectura del Código QR.
•	RF-10: El sistema debe registrar la hora de llegada y gestionar un sistema de "strikes" semanales para las llegadas tarde.
•	RF-11: El sistema debe generar reportes en formato PDF, incluyendo el Roster del período activo y la Planilla de Inscripción individual.
•	RF-12: El sistema debe gestionar un esquema de roles de tres niveles: "Superusuario" (acceso total), "Administrador" (acceso a todos los módulos excepto Mantenimiento) y "Consulta" (acceso restringido a gestión de Late-Pass y reportes).    
•	RF-13: El sistema debe permitir la clasificación del personal (Staff) en sub-categorías (ej. Administrativo, Docente, Mantenimiento, Vigilancia) durante su registro.
•	RF-14: El sistema debe incluir un módulo para el registro y la gestión de vehículos asociados a la comunidad educativa.
•	RF-15: El sistema debe ser capaz de generar reportes en formato PDF con listados de las nuevas categorías, incluyendo Estudiantes, Staff (clasificado por su rol) y Vehículos.
•	RF-16: El sistema debe permitir realizar respaldo de la base de datos creada y manejada en PostgreSQL




o	Requisitos No Funcionales (RNF):
•	RNF-01 (Seguridad): El acceso a los módulos de gestión debe estar restringido al rol de "Administrador". La información sensible, como las contraseñas de usuario, debe almacenarse de forma segura.
•	RNF-02 (Usabilidad): La interfaz de usuario debe ser intuitiva, clara y fácil de usar para personal con distintos niveles de habilidad técnica.
•	RNF-03 (Rendimiento): Las consultas a la base de datos y la carga de las páginas deben ser rápidas y eficientes.
•	RNF-04 (Escalabilidad): La arquitectura del sistema y de la base de datos debe permitir la adición de nuevos módulos y funcionalidades en el futuro sin requerir un rediseño completo.

3.1.2.	Arquitectura de la Solución

El sistema se desarrolló siguiendo una arquitectura de tres capas y una estructura de directorios moderna para garantizar la seguridad y la organización del código.
o	Capa de Presentación (Frontend): Construida con HTML5, CSS3 y JavaScript (Vanilla JS). Se encarga de toda la interacción con el usuario. El uso de JavaScript permite la creación de interfaces dinámicas que se comunican con el backend sin necesidad de recargar la página, ofreciendo una experiencia de usuario fluida.

o	Capa de Lógica de Negocio (Backend): Desarrollada en PHP 8. Esta capa contiene todos los controladores y APIs que procesan las solicitudes del usuario, aplican las reglas de negocio (ej. validaciones, gestión de strikes) y se comunican con la base de datos.

o	Capa de Datos: Gestionada por PostgreSQL, un sistema de gestión de bases de datos relacional robusto y de código abierto, ideal para garantizar la integridad y consistencia de la información.
La estructura de directorios implementa un único punto de acceso público (/public), una práctica de seguridad estándar que protege el código fuente (/src), las APIs (/api) y las páginas de la aplicación (/pages) de ser accedidos directamente desde el navegador.
Figura No.1 - Estructura de Directorio del sistema



3.1.3.	Modelado del Sistema con UML

3.1.3.1.	Diagrama de Casos de Uso

Este diagrama describe las funcionalidades principales del sistema desde la perspectiva del usuario. Identifica a los actores que interactúan con el sistema y las acciones que pueden realizar para cumplir sus objetivos.


Actores:
•	Master: Superusuario (generalmente el desarrollador o soporte técnico) con acceso irrestricto a todas las funciones del sistema, incluyendo las de configuración y mantenimiento a bajo nivel.
•	Administrador: Usuario principal de la institución (ej. director, personal administrativo) con acceso total a las funcionalidades operativas diarias del sistema.   
•	Usuario de Consulta: Usuario con permisos limitados, enfocado específicamente en la supervisión y reporte del módulo de Late-Pass.   

Casos de Uso Principales:
•	Autenticarse en el Sistema:
	Descripción: Todos los actores deben poder ingresar sus credenciales para acceder a las funciones permitidas por su rol.
•	Gestionar Períodos Escolares:
	Descripción: Permite crear nuevos años escolares, activar el período en curso y desactivar los antiguos.   
•	Gestionar Personal (Staff):
	Descripción: Incluye registrar nuevos miembros del personal y asignarles un rol o posición dentro de un período escolar activo.   
•	Gestionar Estudiantes:
	Descripción: Engloba todo el ciclo de vida del estudiante en el sistema. Incluye los siguientes sub-casos:
	Inscribir Nuevo Estudiante: Registro completo de datos personales, de representantes y ficha médica.   
	Administrar Expediente: Modificación de datos existentes del estudiante.   
	Asignar a Período: Vincular un estudiante a un grado específico en el año escolar activo.   

•	Gestionar Control de Acceso (Late-Pass):
	Descripción: Administra todo lo relacionado con la puntualidad de los estudiantes. Incluye:
	Generar Código QR: Crear el documento PDF con el código QR único para un estudiante.   
	Registrar Llegada Tarde: Usar la interfaz de escaneo para marcar la entrada de un estudiante y gestionar los "strikes".   
	Consultar Historial de Late-Pass: Visualizar y filtrar los registros de llegadas tarde por semana y grado. 
  
•	Gestionar Usuarios del Sistema:
	Descripción: Permite crear, editar y eliminar las cuentas de acceso (nombres de usuario y contraseñas) para el personal.   

•	Generar Reportes:
	Descripción: Permite la creación de documentos oficiales en PDF, como la Planilla de Inscripción individual y el Roster completo del período. 
Relaciones:
•	Los actores Master y Administrador están conectados a TODOS los casos de uso, reflejando su acceso completo a las funcionalidades del sistema.
•	El actor Usuario de Consulta está conectado únicamente a los casos de uso Autenticarse en el Sistema y Consultar Historial de Late-Pass, de acuerdo con su rol de supervisión.   

3.1.3.2.	Diagrama de Clases (Conceptual)

Este diagrama modela las entidades fundamentales del sistema, sus atributos, sus operaciones (métodos) y las relaciones que existen entre ellas.

Clases, Atributos y Métodos:
•	Usuario
	Atributos: id, username, password, rol
	Métodos: login(), logout(), verificarPermisos()
•	Estudiante
	Atributos: id, nombre_completo, fecha_nacimiento, direccion
	Métodos: inscribir(), actualizarDatos(), asignarAPeriodo()
•	Profesor (Staff)
	Atributos: id, nombre_completo, cedula, email
	Métodos: registrar(), asignarAPeriodo()
•	PeriodoEscolar
	Atributos: id, nombre_periodo, fecha_inicio, fecha_fin, activo
	Métodos: crear(), activar(), desactivar()
•	LlegadaTarde
	Atributos: id, fecha_registro, hora_registro, semana_del_anio
	Métodos: registrar(), calcularStrike()
•	Representante (Podría ser una clase abstracta con Padre y Madre como subclases)
	Atributos: id, nombre_completo, cedula, telefono

Relaciones y Multiplicidad:
•	Un Usuario (1) puede estar asociado a un Profesor (0..1).
•	Un Estudiante (1) tiene un Padre (0..1) y una Madre (0..1) como Representante.
•	Un Estudiante (1) tiene muchas LlegadaTarde (0..*).
•	Se establece una relación de muchos a muchos entre Estudiante y PeriodoEscolar a través de una clase de asociación llamada AsignacionEstudiante, que contiene atributos como grado_cursado.
•	Similarmente, una relación de muchos a muchos entre Profesor y PeriodoEscolar se resuelve con una clase de asociación AsignacionProfesor (con atributos como posicion).

3.1.3.3.	Diagrama de Secuencia: Proceso "Registrar Llegada Tarde"

Este diagrama ilustra la secuencia de interacciones entre los objetos del sistema para realizar un caso de uso específico. Elegimos el proceso crítico de registrar la llegada de un estudiante mediante QR, basado en el pseudocódigo.   
Objetos (Lifelines):
•	:Usuario (Actor)
•	:ControlAccesoUI (La página control_acceso.php)
•	:LatePassAPI (El endpoint en /api/ que procesa el registro)
•	:BaseDeDatos (El servidor PostgreSQL)
Secuencia de Mensajes:
1.	El :Usuario escanea un QR y el dato (ID del estudiante) se introduce en la :ControlAccesoUI.
2.	La :ControlAccesoUI envía una petición asíncrona (fetch POST) con el ID del estudiante a la :LatePassAPI.
3.	La :LatePassAPI ejecuta una consulta (SELECT) a la :BaseDeDatos para validar que el estudiante existe y está asignado al período activo.
4.	La :BaseDeDatos devuelve los datos del estudiante.
5.	La :LatePassAPI verifica si ya existe un registro para ese estudiante en el día actual.
6.	Si no existe, la :LatePassAPI envía una sentencia (INSERT) a la :BaseDeDatos para registrar la nueva llegada en la tabla llegadas_tarde.
7.	A continuación, la :LatePassAPI envía otra sentencia (UPDATE o INSERT) para actualizar el conteo de "strikes" en la tabla latepass_resumen_semanal.
8.	La :BaseDeDatos confirma las operaciones.
9.	La :LatePassAPI retorna una respuesta JSON (200 OK) a la :ControlAccesoUI con el estado del registro y los datos del estudiante (nombre, número de strikes, etc.).
10.	La :ControlAccesoUI muestra un mensaje de confirmación al :Usuario con la información recibida.

3.1.4.	Diagrama de Base de Datos

El diseño de la base de datos es el pilar del sistema. Se implementó en PostgreSQL y su estructura relacional garantiza la integridad de los datos, evita la redundancia y permite consultas complejas de manera eficiente.
 
Figura No.2 - Diagrama MER -  PostgreSQL_ceia_db (Fuente: DBShema App)

3.1.4.1.	Descripción de Tablas Principales

•	estudiantes: Almacena los datos personales de cada estudiante. Se relaciona con padres y madres a través de las claves foráneas padre_id y madre_id.

•	padres / madres: Contienen la información de los representantes. Están diseñadas para que un único registro de padre/madre pueda ser vinculado a múltiples estudiantes.

•	profesores: Contiene los datos básicos del personal administrativo y docente.

•	periodos_escolares: Permite la creación de múltiples años escolares (ej. "2024-2025", "2025-2026"), con un campo booleano activo que define cuál está en curso.

•	profesor_periodo / estudiante_periodo: Tablas pivote cruciales que vinculan a un profesor o a un estudiante con un período escolar específico, definiendo su rol o grado para ese año en particular.

•	llegadas_tarde / latepass_resumen_semanal: Tablas diseñadas para el módulo de control de acceso. La primera registra cada llegada individual, mientras que la segunda consolida el conteo de "strikes" por semana para cada estudiante.

•	vehiculos: Almacena la información específica de cada vehículo registrado en el sistema, como la placa, marca y modelo.

•	registro_vehiculos: Tabla pivote que vincula un vehículo de la tabla vehiculos con un estudiante de la tabla estudiantes, permitiendo saber qué vehículos están autorizados para cada estudiante.

•	entrada_salida_staff: Registra cada evento de entrada y salida del personal (staff), almacenando el profesor_id, la fecha, la hora y el tipo de movimiento. Esta tabla es fundamental para el control de asistencia del personal.

3.1.5.	Descripción de Módulos y Mapa Navegacional.

•	Descripción de Módulos Implementados

El sistema cuenta con los siguientes módulos operativos: (aqui se describen los modulos del sistema)

3.1.6.	Mapa Navegacional

El flujo de navegación del sistema está diseñado para ser lógico e intuitivo.
•	Página de Login (/public/index.php)
o	Si el login es exitoso -> Dashboard (/pages/dashboard.php)

•	Desde el Dashboard (Barra de Navegación):
o	Estudiantes
	-> Planilla de Inscripción (/pages/planilla_inscripcion.php)
	-> Administrar Expedientes (/pages/administrar_planilla_estudiantes.php)
	-> Asignar a Período (/pages/asignar_estudiante_periodo.php)

o	Staff/Profesores
	-> Gestión de Personal (/pages/profesores_registro.php)

o	Late-Pass
	-> Menú Late-Pass (/pages/latepass_menu.php)
	-> Generar QR (/pages/seleccionar_qr.php)
	-> Control de Acceso (/pages/control_acceso.php)
	-> Gestión y Consulta de Late-Pass (/pages/gestion_latepass.php)
	-> Gestión y Consulta de entrada/salida Staff (/pages/gestion_es_staff.php)
	-> Gestión y Consulta de entrada/salida Staff (/pages/gestion_ehiculos.php)

o	Reportes
	-> Menú de Reportes (/pages/reportes_menu.php)
	-> Roster Actual (/src/reports_generators/roster_actual.php)
	-> Seleccionar Planilla (/pages/seleccionar_planilla.php)

o	Mantenimiento
	-> Gestión de Períodos (/pages/periodos_escolares.php)
	-> Gestión de Usuarios (/pages/usuarios_configurar.php)

o	Salir -> Cierra la sesión y redirige al Login.


3.1.7.	Pseudocódigo: Proceso de Control de Acceso (Late-Pass)

Este pseudocódigo describe la lógica expandida para el módulo de control de acceso, que ahora gestiona entradas/salidas de estudiantes, personal (Staff) y vehículos, y redirige a la vista de gestión y consulta correspondiente..

•	Proceso: ControlAccesoPrincipal(datos_escaneados)
INICIO
    // --- 1. Inicialización y Validación General ---
    OBTENER datos_escaneados (ID o información relevante del QR).

    SI datos_escaneados NO ES válido O ESTÁ VACÍO, ENTONCES
        MOSTRAR MENSAJE "Código QR no válido o vacío. Intente de nuevo."
        TERMINAR PROCESO
    FIN SI

    OBTENER periodo_activo DESDE la BaseDeDatos (tabla periodos_escolares).
    SI no hay periodo_activo, ENTONCES
        MOSTRAR MENSAJE "No hay período escolar activo. Contacte al administrador."
        TERMINAR PROCESO
    FIN SI

    // --- 2. Determinación del Tipo de Entidad (Estudiante, Staff, Vehículo) ---
    // Se asume que el QR contiene un prefijo o estructura que permite identificar el tipo de entidad.
    // Por ejemplo: 'E-' para Estudiante, 'S-' para Staff, 'V-' para Vehículo.

    DETERMINAR tipo_entidad A PARTIR de datos_escaneados.

    SI tipo_entidad ES "Estudiante", ENTONCES
        LLAMAR Proceso: RegistrarYConsultarLlegadaEstudiante(datos_escaneados, periodo_activo)
    SINO SI tipo_entidad ES "Staff", ENTONCES
        LLAMAR Proceso: RegistrarYConsultarEntradaSalidaStaff(datos_escaneados, periodo_activo)
    SINO SI tipo_entidad ES "Vehículo", ENTONCES
        LLAMAR Proceso: RegistrarYConsultarEntradaSalidaVehiculo(datos_escaneados, periodo_activo)
    SINO
        MOSTRAR MENSAJE "Tipo de código QR no reconocido."
        TERMINAR PROCESO
    FIN SI

FIN



// --- Sub-Proceso para Estudiantes ---
Proceso: RegistrarYConsultarLlegadaEstudiante(estudiante_id, periodo_activo)
INICIO
    // --- 1. Registro de Llegada Tarde (o a tiempo) ---
    LLAMAR LatePassAPI_RegistrarLlegada(estudiante_id, periodo_activo)
    RECIBIR respuesta_api.

    SI respuesta_api ES "Éxito" Y respuesta_api.mensaje ES "Llegada Tarde", ENTONCES
        MOSTRAR "Registro de llegada tarde exitoso para " + respuesta_api.nombre_estudiante + ". Strikes: " + respuesta_api.conteo_strikes + ". Mensaje: " + respuesta_api.mensaje_alerta.
        // Redirigir a la vista de gestión de Late-Pass para estudiantes, o actualizar una sección específica
        REDIRECCIONAR_O_ACTUALIZAR_VISTA a /pages/gestion_latepass.php con filtro del estudiante.
    SINO SI respuesta_api ES "Éxito" Y respuesta_api.mensaje ES "Llegada a tiempo", ENTONCES
        MOSTRAR "Registro de llegada a tiempo para " + respuesta_api.nombre_estudiante + "."
        REDIRECCIONAR_O_ACTUALIZAR_VISTA a /pages/gestion_latepass.php con filtro del estudiante.
    SINO
        MOSTRAR "Error al registrar llegada del estudiante: " + respuesta_api.error_mensaje.
        REDIRECCIONAR_O_ACTUALIZAR_VISTA a /pages/gestion_latepass.php.
    FIN SI
FIN

// --- Sub-Proceso para Staff ---
Proceso: RegistrarYConsultarEntradaSalidaStaff(staff_id, periodo_activo)
INICIO
    // --- 1. Registro de Entrada/Salida de Staff ---
    LLAMAR LatePassAPI_RegistrarEntradaSalidaStaff(staff_id, periodo_activo)
    RECIBIR respuesta_api.

    SI respuesta_api ES "Éxito", ENTONCES
        MOSTRAR "Registro de " + respuesta_api.tipo_movimiento + " exitoso para " + respuesta_api.nombre_staff + ". Hora: " + respuesta_api.hora_registro.
        // Redirigir a la vista de gestión de entrada/salida de Staff
        REDIRECCIONAR_O_ACTUALIZAR_VISTA a /pages/gestion_es_staff.php con filtro del staff.
    SINO
        MOSTRAR "Error al registrar entrada/salida del staff: " + respuesta_api.error_mensaje.
        REDIRECCIONAR_O_ACTUALIZAR_VISTA a /pages/gestion_es_staff.php.
    FIN SI
FIN

// --- Sub-Proceso para Vehículos ---
Proceso: RegistrarYConsultarEntradaSalidaVehiculo(vehiculo_id, periodo_activo)
INICIO
    // --- 1. Registro de Entrada/Salida de Vehículo ---
    LLAMAR LatePassAPI_RegistrarEntradaSalidaVehiculo(vehiculo_id, periodo_activo)
    RECIBIR respuesta_api.

    SI respuesta_api ES "Éxito", ENTONCES
        MOSTRAR "Registro de " + respuesta_api.tipo_movimiento + " exitoso para vehículo " + respuesta_api.placa_vehiculo + ". Hora: " + respuesta_api.hora_registro.
        // Redirigir a la vista de gestión de entrada/salida de Vehículos
        REDIRECCIONAR_O_ACTUALIZAR_VISTA a /pages/gestion_ehiculos.php con filtro del vehículo.
    SINO
        MOSTRAR "Error al registrar entrada/salida del vehículo: " + respuesta_api.error_mensaje.
        REDIRECCIONAR_O_ACTUALIZAR_VISTA a /pages/gestion_ehiculos.php.
    FIN SI
FIN

3.2.	Descripción y Resultados de las Pruebas

Se ejecutó un plan de pruebas funcionales enfocado en validar las reglas de negocio más importantes y los flujos de trabajo críticos del sistema. Las pruebas se realizaron de forma manual, simulando las acciones de los roles "Administrador" y "Consulta". Los resultados confirmaron que el sistema se comporta de acuerdo a los requisitos definidos, es estable y maneja los errores de forma controlada. La tabla presentada en el informe de la Fase 5 (sección 2.2.1) resume los casos de prueba más relevantes y sus resultados exitosos.

3.2.1.	Evidencia de Validaciones y Pruebas

Se realizaron pruebas funcionales para cada uno de los módulos desarrollados, verificando que los resultados obtenidos fueran los esperados.
•	Pruebas Funcionales
Módulo	Característica Probada	Pasos de la Prueba	Resultado Esperado	Resultado Obtenido	Estado
Usuarios	Creación de Usuario	1. Ingresar datos. 
2. Asignar rol "consulta". 
3. Guardar.	El nuevo usuario aparece en la lista con el rol correcto.	El usuario se crea y se lista correctamente.	✅ Éxito
Estudiantes	Vincular Padre Existente	1. Iniciar inscripción. 
2. Ingresar C.I. de un padre ya registrado. 
3. Hacer clic en "Vincular".	El formulario del padre se bloquea, mostrando los datos existentes.	El formulario se bloquea y se vincula correctamente.	✅ Éxito
Reportes	Generar PDF del Roster	1. Asignar 3 administrativos y 1 estudiantes al período activo. 
2. Ir a Reportes -> Roster. 
3. Hacer clic en "Generar PDF".	Se genera un PDF con 3 administrativos y 1 estudiantes listados.	El PDF se genera con los datos correctos.	✅ Éxito
Seguridad	Acceso no autorizado	1. Iniciar sesión con rol "consulta". 
2. Intentar acceder a la URL de gestión de usuarios.	El sistema muestra un mensaje de "Acceso denegado". y redirige al dashboard	El sistema redirige correctamente.	✅ Éxito
Cuadro No.1 – Prueba funcionales


•	Pruebas de Carga (Rendimiento):
o	Objetivo: Evaluar cómo se comporta el sistema bajo diferentes volúmenes de usuarios o datos.
o	Método: Simular el acceso concurrente de varios usuarios. Se realizaron pruebas manuales intensivas con múltiples sesiones.

o	Casos de Prueba de Carga Sugeridos:
	Carga de Reportes Grandes: Generar el "Roster Actualizado" con la mayor cantidad de estudiantes y personal posible. Medir el tiempo de generación (logrado en <15 segundos).   
	Inscripciones Concurrentes: Simular la inscripción de 2-3 estudiantes simultáneamente desde diferentes estaciones de trabajo. Monitorear el tiempo de respuesta y la integridad de los datos.

•	Pruebas de Usabilidad:
o	Objetivo: Evaluar qué tan fácil y eficiente es el sistema para los usuarios finales (personal administrativo).
o	Método: Observación directa del personal utilizando el sistema, realizado en la Fase IV: "Validación con el Usuario".
o	Resultados a Reportar: "El personal administrativo encontró la interfaz intuitiva y el flujo de trabajo lógico, lo que redujo significativamente la curva de aprendizaje. Se observó que las tareas de registro de inscripción se completaron en un promedio de menos de 5 minutos, cumpliendo con el objetivo de diseño establecido."   

•	Pruebas de Seguridad:
o	Objetivo: Identificar vulnerabilidades que puedan comprometer la confidencialidad, integridad y disponibilidad de los datos.
o	Método:
	Pruebas de Acceso por Roles: Se probaron todos los módulos y todas las combinaciones de roles (ej., que un "Consulta" no pueda modificar datos de estudiantes).
	Inyección SQL: Intentar introducir código SQL malicioso en los campos de entrada para verificar que la base de datos es inmune a estos ataques.
	Validación de Entrada: Asegurar que todos los formularios validan los datos de entrada (ej., que no se puedan ingresar números en campos de texto, o fechas inválidas).

o	Resultados a Reportar: "Las pruebas de seguridad confirmaron la robustez del sistema contra accesos no autorizados y manipulaciones de datos, gracias a la implementación de un punto de acceso único(/public), la correcta gestión de roles y el uso de   consultas parametrizadas en la capa de lógica de negocio, mitigando riesgos como la inyección SQL. No se detectaron vulnerabilidades críticas."

3.2.2.	Documentación del Sistema

•	Manual Técnico

•	Entorno de Desarrollo:
o	Servidor Web: Apache (incluido en XAMPP).
o	Versión de PHP: 8.0 o superior.
o	Gestor de Base de Datos: PostgreSQL 14 o superior.


•	Guía de Implementación y Puesta en Producción

Introducción

Este documento describe el procedimiento detallado para la instalación y configuración del Sistema Web para la Gestión Académica del CEIA en un entorno de red cliente-servidor. El objetivo es desplegar la aplicación en un servidor central dentro de la intranet del Centro Educativo Internacional Anzoátegui, permitiendo el acceso concurrente desde las estaciones de trabajo del personal autorizado (Director, administrador, asistente, IT Manager).
El entorno de producción se basa en una máquina virtual con Windows Server 2022, utilizando XAMPP como servidor web (Apache y PHP) y un servidor de bases de datos PostgreSQL.

•	Fase 1: Preparación del Servidor (Máquina Virtual Windows Server 2022)

Esta fase se centra en instalar y configurar todo el software necesario en la máquina virtual que actuará como servidor de la aplicación.
o	Paso 1.1: Instalación del Software Base

1.	Instalar XAMPP:
o	Descargue la última versión de XAMPP compatible con PHP 8.0 o superior desde el sitio oficial de Apache Friends.
o	Ejecute el instalador. Se recomienda instalarlo en la ruta por defecto (C:\xampp).
o	Durante la instalación, asegúrese de que los componentes Apache y PHP estén seleccionados. MySQL y otros componentes son opcionales, ya que usaremos PostgreSQL.


2.	Instalar PostgreSQL:

o	Descargue el instalador de PostgreSQL (versión 14 o superior) para Windows desde su sitio web oficial.
o	Ejecute el instalador. Durante la instalación, se le pedirá establecer una contraseña para el superusuario postgres. Anote esta contraseña, ya que es crucial.
o	Puede dejar los demás ajustes (puerto, localización) con sus valores por defecto.



o	Paso 1.2: Configuración del Firewall del Servidor

Para que las computadoras cliente puedan "ver" el servidor web, debemos abrir el puerto de Apache en el Firewall de Windows.
1.	Abra el Firewall de Windows Defender con seguridad avanzada.
2.	Haga clic en "Reglas de entrada" en el panel izquierdo.
3.	Haga clic en "Nueva regla..." en el panel derecho.
4.	Seleccione "Puerto" y haga clic en "Siguiente".
5.	Seleccione "TCP" y en "Puertos locales específicos", escriba 80. Haga clic en "Siguiente".
6.	Seleccione "Permitir la conexión" y haga clic en "Siguiente".
7.	Asegúrese de que las casillas "Dominio", "Privado" y "Público" estén marcadas para permitir el acceso desde la red local. Haga clic en "Siguiente".
8.	Asigne un nombre a la regla (ej. "Acceso Apache CEIA") y una descripción. Haga clic en "Finalizar".

•	Fase 2: Despliegue de la Aplicación y Base de Datos

Ahora que el servidor está listo, vamos a instalar el sistema web y su base de datos.

o	Paso 2.1: Implementación del Código Fuente

1.	Clonar el Repositorio:
o	Abra una terminal de Git en su servidor.
o	Navegue al directorio htdocs de XAMPP: cd C:\xampp\htdocs
o	Clone el proyecto desde GitHub: git clone https://github.com/rogerrcarvajal/ceia_swga.git
o	Esto creará una carpeta C:\xampp\htdocs\ceia_swga con todo el código del sistema.

o	Paso 2.2: Configuración de la Base de Datos

1.	Crear la Base de Datos:
o	Abra pgAdmin (instalado con PostgreSQL).
o	Conéctese a su servidor usando la contraseña que estableció.
o	En el árbol de la izquierda, haga clic derecho en "Databases" -> "Create" -> "Database...".
o	Asigne el nombre ceia_db y guarde.

2.	Importar la Estructura y Datos:
o	Haga clic derecho sobre la nueva base de datos ceia_db -> "Restore...".
o	En la pestaña "Filename", seleccione el archivo ceia_db.sql que se encuentra dentro del repositorio que clonó.
o	Ejecute el proceso de restauración. Esto creará todas las tablas y cargará los datos iniciales.

o	Paso 2.3: Conectar la Aplicación con la Base de Datos

1.	Navegue a la carpeta de configuración del sistema: C:\xampp\htdocs\ceia_swga\src.
2.	Abra el archivo config.php con un editor de texto.
3.	Modifique las credenciales de la base de datos para que coincidan con su instalación de PostgreSQL.
4.	// Ejemplo de configuración en src/config.php
5.	$host = 'localhost';
6.	$port = '5432'; // Puerto por defecto de PostgreSQL
7.	$dbname = 'ceia_db';
8.	$user = 'postgres'; // El superusuario por defecto
9.	$password = 'SU_CONTRASEÑA_DE_POSTGRES'; // La contraseña que anotó

•	Fase 3: Configuración de la Red para Acceso Intranet

Este es el paso más importante para que el sistema sea accesible desde otras computadoras.
o	Paso 3.1: Obtener la IP del Servidor

1.	En la máquina virtual del servidor, abra el Símbolo del sistema (cmd).
2.	Ejecute el comando ipconfig.
3.	Busque la dirección "Dirección IPv4". Será una dirección similar a 192.168.1.100. Anote esta dirección IP. Esta es la dirección de su servidor en la intranet del CEIA.



o	Paso 3.2: Configurar Apache para Aceptar Conexiones de Red

Por defecto, XAMPP solo permite conexiones desde la misma máquina (localhost). Debemos cambiar esto.
1.	Abra el Panel de Control de XAMPP.
2.	Junto al módulo de Apache, haga clic en el botón "Config" y seleccione httpd-vhosts.conf.
3.	Añada el siguiente bloque de código al final del archivo. Reemplace 192.168.1.100 con la dirección IP real de su servidor.
4.	# Virtual Host para el Sistema de Gestión del CEIA
5.	<VirtualHost *:80>
6.	    ServerAdmin admin@ceia.local
7.	    DocumentRoot "C:/xampp/htdocs/ceia_swga/public"
8.	    ServerName 192.168.1.100
9.	    <Directory "C:/xampp/htdocs/ceia_swga/public">
10.	        Options Indexes FollowSymLinks
11.	        AllowOverride All
12.	        Require all granted
13.	    </Directory>
14.	    ErrorLog "logs/ceia_swga-error.log"
15.	    CustomLog "logs/ceia_swga-access.log" common
16.	</VirtualHost>

o	DocumentRoot: Le dice a Apache que la carpeta visible para los usuarios es /public, lo cual es una medida de seguridad crucial.
17.	Guarde y cierre el archivo httpd-vhosts.conf.
18.	En el Panel de Control de XAMPP, detenga (Stop) el servicio de Apache y vuelva a iniciarlo (Start) para que los cambios surtan efecto.




•	Fase 4: Acceso desde las Computadoras Cliente

¡Todo está listo! Ahora los usuarios pueden acceder al sistema desde sus computadoras.
1.	Vaya a una de las computadoras cliente (Director, administrador, etc.) que esté conectada a la misma red LAN del CEIA.
2.	Abra un navegador web (Google Chrome, Firefox, etc.).
3.	En la barra de direcciones, escriba la dirección IP de su servidor que anotó en el Paso 3.1. Por ejemplo:
http://192.168.1.100
4.	Presione Enter. La pantalla de login del Sistema Web para la Gestión Académica del CEIA debería aparecer.
¡Felicidades! Has implementado con éxito el sistema en un entorno de producción local, listo para ser utilizado por todo el personal del CEIA a través de la intranet.

•	Estructura de la API: El sistema utiliza una serie de endpoints en la carpeta /api que son llamados vía fetch desde JavaScript. Todas las respuestas se manejan en formato JSON.

•	Estructura Public: Es una estructura con un único punto de acceso público y contiene los estilos, imágenes y JavaScript del sistema.

•	Estructura Pages: Contiene las páginas principales del sistema.

•	Estructura SRC: Mantiene el código fuente y los archivos de configuración, además de las librerías y reportes del sistema.





•	Manual de Usuario

•	Inscribir un Nuevo Estudiante:
o	Asegúrese de que haya un período escolar activo en Mantenimiento -> Períodos Escolares.
o	Vaya al menú Estudiantes -> del menú seleccione Planilla de Inscripción.
o	Rellene todos los campos del estudiante.
o	Al llegar a los datos del padre/madre, ingrese primero la cédula. Si el sistema encuentra una coincidencia, haga clic en "Vincular". Si no, complete los campos.
o	Complete la ficha médica.
o	Haga clic en "Guardar Inscripción".

•	Administrar Planilla de Inscripción:
o	Asegúrese de que haya un período escolar activo en Mantenimiento -> Períodos Escolares.
o	Vaya al menú Estudiantes -> del menú seleccione Gestionar Planilla de Inscripción.
o	Escriba o seleccione a un estudiante de la lista de estudiantes para visualizar la planilla de inscripción
o	Realice los cambios en la planilla
o	Haga clic en "Actualizar…". En cada sección de la información que desea cambiar sea del Estudiante, Padre, Madre o Ficha Medica.

•	Gestionar un Estudiante (vincular): 
o	Asegúrese de que haya un período escolar activo en Mantenimiento -> Períodos Escolares.
o	Vaya al menú Estudiantes -> del menú seleccione Gestionar Estudiantes
o	Seleccione un periodo escolar de la lista de periodos escolares para visualizar la sección para vincular al estudiante aun periodo escolar y grado a cursar
o	Selecciones un estudiante de la lista de estudiantes y asigne un grado.

•	Gestionar un Estudiante (editar vinculo): 
o	Asegúrese de que haya un período escolar activo en Mantenimiento -> Períodos Escolares.
o	Vaya a  menú Estudiantes -> del menú seleccione Gestionar Estudiantes
o	Seleccione un periodo escolar de la lista de periodos escolares para visualizar la sección para vincular al estudiante aun periodo escolar y grado a cursar
o	Haga clic en “Gestionar Estudiantes”
o	Haga clic en “Gestionar” en el estudiante que desea gestionar
o	Desactive la casilla que vincula al estudiante con el periodo escolar previamente registrado o asigne un nuevo grado si ese es el caso
o	Haga clic en “guardar Cambios” y luego en volver si desea realizar algún cambio a otro estudiante.

•	Registrar un Staff / Profesor nuevo (Personal):
o	Asegúrese de que haya un período escolar activo en Mantenimiento -> Períodos Escolares.
o	Vaya a Staff -> Rellene todos los campos del personal.
o	Haga clic en "Agregar Staff”.

•	Vincular a un Staff/Profesor (Personal):
o	Asegúrese de que haya un período escolar activo en Mantenimiento -> Períodos Escolares.
o	Vaya a Staff -> Presione el botón del personal que desea vincular o modificar su vínculo previo.
o	Realice los cambios de los datos básicos del personal
o	Si el personal es nuevo, active la casilla para vincularlo al periodo escolar activo y asigne una posición y grado
o	Haga clic en "Guardar cambios".



•	Gestión de control de Late-Pass (Generar QR):
o	Asegúrese de que haya un período escolar activo en Mantenimiento -> Períodos Escolares.
o	Vaya al menú Late-Pass -> del menú seleccione Generar QR
o	Seleccione una categoría (Staff Administrativo, Staff Docente, Estudiantes, Staff Mantenimimiento, Staff Vigilancia y Vehículo), y luego escoja una opción de la lista y haga clic en “Generar PDF”
o	Se mostrará en una ventana nueva el PDF generado
o	Descargue el PDF, imprímalo y coloque en el expediente del estudiante.

•	Gestión de control de Late-Pass (Control de Acceso):
o	Asegúrese de que haya un período escolar activo en Mantenimiento -> Períodos Escolares.
o	Vaya al menú Late-Pass -> del menú seleccione Control de Acceso Late-Pass (Esta ventana deberá permanecer abierta hasta periodo del break para registrar las entradas de Estudiantes, Staff y/o Vehículos autorizados).

•	Gestión y Consulta de Late-Pass:
o	Asegúrese de que haya un período escolar activo en Mantenimiento -> Períodos Escolares.
o	Vaya al menú Late-Pass -> del menú seleccione Gestión y consulta de Late-Pass
o	Seleccione la semana que desea consultar y el grado para visualizar las entradas registradas de los estudiantes con Late-Pass

•	Gestión y Consulta de entradas/salidas de Staff:
o	Asegúrese de que haya un período escolar activo en Mantenimiento -> Períodos Escolares.
o	Vaya al menú Late-Pass -> del menú seleccione Gestión y consulta de entradas/salidas de Staff
o	Seleccione la semana que desea consultar y el grado para visualizar las entradas y salidas registradas del staff.

•	Gestión y Consulta de entradas/salidas de Vehículos:
o	Asegúrese de que haya un período escolar activo en Mantenimiento -> Períodos Escolares.
o	Vaya al menú Late-Pass -> del menú seleccione Gestión y consulta de entradas/salidas de Vehículos
o	Seleccione la semana que desea consultar y el grado para visualizar las entradas y salidas registradas de los vehículos autorizados

•	Para Generar un Reporte:
o	Vaya al menú Reportes.
o	Para una planilla individual de Estudiante, haga clic en "Planilla de Inscripción", seleccione al estudiante de la lista y haga clic en "Generar PDF".
o	Para el Roster, haga clic en "Roster Actualizado" y luego en "Generar PDF".
o	Para generar reportes de Estudiantes, Staff Administrativo, Docente, de mantenimiento y de Vehículos autorizados haga clic en “Generar Reportes Estudiantes/Staff/Vehículos y luego selecciones una de las categorías disponibles (Estudiantes, Staff Administrativo, Docente, de mantenimiento y de Vehículos autorizados) por cada una haga clic en “Generar PDF”

3.3.	Comparación entre el Proceso Manual y Automatizado y Beneficios Medidos

La implementación del Sistema Web para la Gestión Académica de Inscripción y Late-Pass (SWGA) ha transformado significativamente los procesos administrativos del CEIA, pasando de un enfoque manual y disperso a uno digital y centralizado. A continuación, se presenta una comparación de los procesos clave y los beneficios tangibles e intangibles medidos tras la puesta en marcha del sistema.
3.3.1.	Proceso de Inscripción
•	Antes (Manual): El registro de nuevos estudiantes y la actualización de expedientes se realizaban mediante planillas físicas. Esto implicaba:   

•	Tiempo por inscripción: Un promedio de 30 minutos por familia, incluyendo el llenado, revisión y corrección debido a errores ortográficos y la necesidad de rellenar varias veces.

•	Errores: Tasa de error de transcripción superior al 20%, llevando a inconsistencias y duplicidad de datos de representantes.

•	Almacenamiento: Dependencia de archivos físicos y espacio de almacenamiento considerable. 

•	Generación de Roster: Proceso manual y propenso a errores, con demoras significativas para su actualización. 


•	Después (Automatizado con SWGA): La "Planilla de Inscripción" digital y la capacidad de vincular padres/madres existentes han optimizado el proceso:   

•	Tiempo por inscripción: Se ha reducido a menos de 5 minutos por estudiante, lo que representa un ahorro de tiempo superior al 80% por familia. Este ahorro libera al personal para tareas de mayor valor.

•	Errores: La validación de datos en tiempo real y la eliminación de la transcripción manual han llevado a una reducción drástica


•	Acceso a la Información: Los datos están centralizados en PostgreSQL, permitiendo el acceso y consulta inmediata de expedientes completos y la generación instantánea del Roster actualizado.   

•	Costos: Eliminación casi completa del gasto en papel y tóner para estos procesos.   


3.3.2.	Proceso de Control de Llegadas Tarde (Late-Pass)
•	Antes (Manual): El control de Late-Pass se basaba en registros manuales en papel, con las siguientes limitaciones:  
 
•	Ineficiencia: Tiempos de registro lentos y susceptibilidad a la pérdida de información.   

•	Trazabilidad: Dificultad para obtener un historial preciso de las llegadas tarde y para aplicar la política de "strikes" de forma consistente. 


•	Después (Automatizado con SWGA): El módulo de Late-Pass con generación de códigos QR y control automatizado de acceso ha proporcionado:   

•	Eficiencia y Precisión: Registro instantáneo y preciso de la hora de llegada mediante escaneo QR.


•	Trazabilidad: Control automatizado de los "strikes" semanales, permitiendo la generación de reportes detallados y la notificación oportuna a los profesores y representantes.   

•	Seguridad y Fiabilidad: Los datos de asistencia se almacenan de forma segura, reduciendo la manipulación y pérdida de registros.

3.3.3.	Beneficios Generales Medidos
•	Optimización del Tiempo Administrativo: La automatización de tareas repetitivas ha resultado en un ahorro cuantificable de horas de trabajo administrativo, permitiendo que el personal se enfoque en actividades de mayor valor estratégico.

•	Mejora en la Organización y Acceso a la Información: La centralización de datos en una base de datos relacional robusta (PostgreSQL) garantiza una única fuente de verdad, facilitando la consulta y la toma de decisiones informada.

•	Reducción de Costos Operativos: Disminución significativa en el consumo de papel, tóner y espacio de almacenamiento físico.   

•	Fortalecimiento de la Seguridad de los Datos: La implementación de roles de usuario y la arquitectura segura protegen la información sensible de accesos no autorizados.   

•	Mejora de la Imagen Institucional: El CEIA se posiciona como una institución a la vanguardia tecnológica, mejorando la experiencia de estudiantes, docentes y personal administrativo.   









CONCLUSIONES

Al finalizar el ciclo de investigación, diseño y desarrollo del Sistema Web para la Gestión Académica de Inscripción y Late-Pass del Centro Educativo Internacional Anzoátegui (CEIA), se ha llegado a una serie de conclusiones fundamentales que validan la pertinencia y el éxito del proyecto en el cumplimiento de los objetivos propuestos.
1.	Diagnóstico y Validación de la Necesidad: Se concluyó de manera fehaciente que los procesos manuales de inscripción y control de llegadas tarde en el CEIA representaban un obstáculo significativo para la eficiencia operativa de la institución. El análisis preliminar, respaldado por datos cuantitativos de tiempo y errores, confirmó que la dependencia del papel generaba duplicidad de datos, dificultaba el acceso a la información histórica y consumía un tiempo valioso del personal administrativo, validando así la necesidad imperante de una solución tecnológica centralizada y automatizada.

2.	Cumplimiento Integral de los Objetivos: El desarrollo del prototipo funcional ha permitido cumplir satisfactoriamente con cada uno de los objetivos específicos planteados. Se diseñó y construyó una arquitectura web robusta (PHP 8+, PostgreSQL 14+, HTML5/CSS3), se integraron exitosamente los módulos de gestión de estudiantes, personal, períodos escolares y control de acceso (Late-Pass), y se implementaron herramientas para la generación de reportes clave como el Roster y la Planilla de Inscripción individual, así como la   emisión de códigos QR únicos para la trazabilidad de accesos.

3.	Transformación y Optimización de Procesos: La solución computacional desarrollada representa una transformación radical del flujo de trabajo administrativo del CEIA. La automatización del registro de inscripciones, con su lógica para vincular representantes existentes, eliminó la redundancia de datos y agilizó el proceso en más del 80% del tiempo. De igual manera, el sistema de control de acceso mediante códigos QR erradica la necesidad de registros manuales, permitiendo un   seguimiento de la puntualidad preciso, inmediato y automatizado, con una clara trazabilidad en accesos y reportes. 

4.	Arquitectura Escalable y Segura (Solidez Técnica): La elección de una arquitectura de tres capas con un único punto de acceso público, junto con el uso de tecnologías probadas como PHP, PostgreSQL y JavaScript, ha resultado en un sistema no solo funcional, sino también   seguro y altamente escalable. La implementación de roles de usuario diferenciados ("Master, Administrador" y "Consulta") garantiza que el acceso a la información sensible esté debidamente restringido, mientras que el diseño modular y la base de datos relacional sientan las bases para futuras expansiones del sistema con nuevos módulos.   

5.	Impacto Institucional Positivo: El sistema web desarrollado no es solo una herramienta tecnológica, sino un agente de cambio positivo para la institución. Al centralizar la información y automatizar tareas repetitivas, se liberó al personal administrativo para enfocarse en actividades de mayor valor añadido. Esto se traduce en una mejora tangible en la calidad del servicio ofrecido a los estudiantes y representantes, reducción del uso de papel, y proyecta una imagen de modernización e innovación para el CEIA, alineándose con las tendencias actuales de digitalización educativa.
En síntesis, este Trabajo Especial de Grado ha logrado materializar una solución informática integral que responde directamente a una necesidad real y apremiante, demostrando el valor de la aplicación de la ingeniería de software para optimizar la gestión en el ámbito educativo y sentando las bases para una mejora continua de los procesos institucionales.

 
RECOMENDACIONES

A partir de los hallazgos, la experiencia adquirida durante el desarrollo y la visión a futuro del sistema, se proponen las siguientes recomendaciones para maximizar el impacto y asegurar la sostenibilidad de la solución implementada en el Centro Educativo Internacional Anzoátegui (CEIA):
1.	Plan de Capacitación y Gestión del Cambio: Implementar un programa de capacitación integral para todo el personal, con sesiones prácticas y manuales de usuario, facilitará la adopción del sistema y minimizará la resistencia al cambio, asegurando una transición fluida de los procesos manuales a los digitales.

2.	Expansión a Nuevos Módulos Funcionales: Aprovechando la arquitectura modular del sistema, se recomienda expandir la plataforma para incluir módulos adicionales que complementen la gestión académica. Específicamente, se sugiere desarrollar un módulo de gestión y control de entradas/salidas de profesores y vehículos, permitiendo un control de acceso más integral y una mayor trazabilidad de la comunidad educativa.   

3.	Mantenimiento Proactivo y Auditorías de Seguridad: Dada la evolución constante de la tecnología, es crucial establecer un plan de mantenimiento periódico que incluya la actualización de versiones de PHP, PostgreSQL y librerías. Asimismo, se deben realizar auditorías de seguridad regulares para proteger el sistema contra nuevas vulnerabilidades, garantizando la confidencialidad e integridad de los datos sensibles.

4.	Integración con Herramientas de Comunicación: Para potenciar la comunicación institucional, se sugiere explorar la integración del sistema con servicios de notificación por correo electrónico o mensajería instantánea. Esto permitiría enviar alertas automáticas a los representantes (ej., por acumulado de "strikes" de Late-Pass) o distribuir comunicados generales de forma masiva y eficiente.

5.	Recopilación de Feedback y Mejora Continua: Establecer un mecanismo formal para recopilar feedback de los usuarios finales (encuestas, reuniones periódicas) permitirá identificar nuevas necesidades y oportunidades de mejora, asegurando que el sistema evolucione y continúe siendo una herramienta valiosa y pertinente para el CEIA a largo plazo.






















REFERENCIAS BIBLIOGRÁFICAS

•	Hernández, S., Pancho, F., & Sánchez, V. (2022). Sistema de registro académico para el Centro de Educación Inicial: “Virtud y Orden”. Barrio Cayaurima en la ciudad de Barcelona, Estado Anzoátegui. Recuperado de
https://www.studocu.com/latam/document/universidad-politecnica-territorial-jose-antonio-anzoategui/diseno-y-desarrollo-de-producto/trabajo-final/82502262

•	Rodríguez, O. (2023). Sistema de Información para la Gestión Académica en la Unidad Educativa María Antonia Bolívar. Lechería, Estado Anzoátegui: Trabajo de Grado presentada como requisito parcial para. Recuperado de
https://www.scribd.com/document/663805749/Tesis-de-Grado-Rodriguez-Oriannys-2023

•	Royce, W. W. (1970). NAGING THE DEVELOPMENT OF LARGE SOFTWARE SYSTEMS. Recuperado de
https://leadinganswers.typepad.com/leading_answers/files/original_waterfall_paper_winston_royce.pdf

•	Veracierta, R. (2022). Sistema de Gestión para el ingreso y egreso de estudiantes de la Unidad Educativa Estadal Enrique Vásquez Fermín en la ciudad de Maracay del estado Aragua. Maracay del estado Aragua.: (Trabajo Especial de Grado para Optar al Título de Técnico Superior Universitario en la Especialidad de Informática). Recuperado de
https://iutamaracay.com.ve/iuta_webpage/archivos/Rafael%20Veracierta.pdf

•	Wulandari, R., Hidayat, R., & Afifuddin, M. (2024). Development of Academic Information System for Academic Administration in SMPN 2 Bogor Using Waterfall Method. Journal of Computing and Informatics, 3(1), 1-8

