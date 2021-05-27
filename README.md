# Goi admin

(English below)


Repositorio público de mi sistema de [administración de vocabulario](https://aquinzi.com/es/projects/goi-admin/). 

Notas:

  * Fue **hecho rápido** en base a otro sistema (app) que estaba usando. En este proceso se quiso copiar parte del flujo que se tenía, y agregarle un par de funcionalidades que necesitaba en aquel momento. Con el tiempo, se le fueron agregando otras funcionalidades en base al uso. (Esto quiere decir que le tengo que hacer una reescritura)
  * Esta hecho a medida para mí y nunca se pensó hacerlo público. Por esa razón hay cosas en duro ("hardcoded").
  * El único estilo que tiene es el botón rojo de eliminar. Lo demás es básicamente como está por defecto en el navegador. Prioricé la rápidez de carga y recursos.
  * **No se incluye** el script para "sincronizar" los datos con [Anki](https://ankiweb.net/)


Iniciar:
  
  * Poner los archivos en una carpeta, importar el dump de la base de datos (encontrado en el raíz), configurar las credenciales de la base de datos en el programa.
  * Credenciales por defecto para el sistema: admin:admin


A futuro:

  * Reescribirlo con mejores prácticas de PHP, usando librerías.
  * Reescribirlo usando algún framework PHP (posiblemente Symfony)



-----


Public repository for my [vocabulary system management](https://aquinzi.com/projects/goi-admin/).

Notes:

  * It was **done fast** based on an app I was using. In this process I wanted to copy part of the flow I was having, and add some functionality that I needed at that time. With the usage, I added more missong functionality. (This means I have to rewrite it to make it cleaner)
  * It's custom made for me and never ment to be public. For this reason, there are many hardocded things.
  * The only style it's got is the red delete button. Everything else is basically styled as the browser renders it by default. I've prioritized fast loading and low resources.
  * **Does not include** the "syncronizing" script for [Anki](https://ankiweb.net/)


Start:
  
  * Put files in a folder, import the database dump (found on the root folder), add the database credentials.
  * System default credentials: admin:admin


Future:

  * Rewrite it with better PHP practices, using libraries.
  * Rewrite it using a PHP framework (probably Symfony)

