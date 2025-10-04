# Mi Aprendizaje con Gemini

Este archivo almacena el conocimiento y las directrices que adquiero durante nuestras interacciones.

## Directrices Iniciales

1.  **Idioma de Comunicación:** Todas nuestras interacciones se realizarán en español para garantizar una comprensión clara y efectiva.
2.  **Contexto Activo:** Mantendré siempre activo el perfil de "Asistente Vibe Code" a través del archivo `Gemini Code Assist.md` para potenciar mis capacidades y asegurar que mis respuestas estén alineadas con tus expectativas.
3.  **Disposición y Asistencia:** Estoy aquí para asistirte en cualquier solicitud relacionada con programación, creación, edición y manejo de archivos. Mi objetivo es ser proactivo y eficiente para ayudarte a alcanzar tus metas.
4.  **Aprendizaje Continuo:** Registraré en este archivo los aprendizajes más relevantes que obtenga de nuestro trabajo conjunto. Esto me permitirá mejorar constantemente mis habilidades en tecnología y otras áreas de conocimiento.
5.  **Control de Versiones:** Para subir archivos nuevos o modificados al repositorio remoto, utilizaré la instrucción "sube los cambios al repositorio remoto en github".
6.  **Memoria de Chat:** Revisare el ultimo chat, a fin de recordar la ultima iteraccion mas reciente, a fin de poder continuar nuestra conversacion.
7.  **Pruebas Unitarias:** Para confirmar que todo este correcto, y que no existan errores, ejecutare los test unitarios, a fin de validar la calidad del codigo.
8.  **Compilación de Código:** Al finalizar cualquier desarrollo, usare el comando 'npm run build', para asegurar que todo el codigo compile de forma correcta, y no existan errores de compilacion.
9.  **Documentación Inicial (README):** Al iniciar un nuevo proyecto, creare un archivo README.md, donde documentare el proyecto, sus caracteristicas, tecnologias, y requerimientos, a fin de que cualquier programador, pueda entender el codigo y su funcionamiento.
10. **Código Limpio:** Para mantener un código limpio, legible y mantenible, evitaré el uso de código comentado, a menos que el usuario así lo indique.
11. **Optimización de Base de Datos:** Para mantener un performance óptimo en las consultas a la base de datos, crearé índices en las tablas, en aquellas columnas que más se usen en las consultas.
12. **Seguridad de Entrada de Datos:** Para mantener la seguridad de la aplicación, validaré y sanitizaré toda la información que provenga del usuario, a fin de mitigar ataques maliciosos.
13. **Variables de Entorno:** Para mantener la consistencia en el código y evitar el hardcoding, crearé un archivo de variables de entorno donde definiré todas las variables globales que necesite la aplicación.
14. **Comentarios Explicativos:** Para que el usuario pueda entender el código y la solución implementada, agregaré comentarios explicativos en aquellas partes del código que sean muy complejas.
15. **Reutilización de Código:** Para evitar la redundancia en el código, crearé funciones genéricas que se puedan reutilizar en aquellas partes del código que sean repetitivas.

## Aprendizajes Recientes

-   **Flujo de Trabajo del Usuario:** Después de subir los cambios al repositorio remoto, el usuario los descarga en su entorno local para probarlos y confirmar que todo funciona correctamente.
-   **Configuración de Git del Usuario:** La configuración de Git del usuario presenta problemas con la firma GGPG. Para evitar errores, realizaré los commits con la bandera `--no-gpg-sign`.
-   **Protección de Repositorio:** El repositorio en GitHub tiene reglas de protección que bloquean la subida de secretos (como claves de API). Debo asegurarme de no incluir información sensible en los commits.
-   **Reescritura de Historial de Git:** He aprendido a reescribir el historial de commits de una rama para eliminar completamente información sensible que se haya subido por error, utilizando `git reset` y `git push --force`.
-   **Auditoría Continua del Proyecto:** Periódicamente, debo realizar un análisis completo del proyecto SWGA. Esta tarea implica revisar la funcionalidad y lógica de negocio, compararla con la documentación existente y guardar un resumen conclusivo (observaciones, vulnerabilidades, recomendaciones) en un archivo `.md` dentro de la carpeta `/Auditorias`, utilizando un nombre que incluya la fecha de la revisión.
