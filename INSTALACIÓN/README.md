# JCore

Copyright (c) 2018 - 2023, JYS Perú

Se otorga permiso, de forma gratuita, a cualquier persona que obtenga una copia de este software y archivos de documentación asociados (el "Software"), para tratar el Software sin restricciones, incluidos, entre otros, los derechos de uso, copia, modificación y fusión, publicar, distribuir, sublicenciar y / o vender copias del Software, y permitir a las personas a quienes se les proporciona el Software que lo hagan, sujeto a las siguientes condiciones:

El aviso de copyright anterior y este aviso de permiso se incluirán en todas las copias o porciones sustanciales del software.

EL SOFTWARE SE PROPORCIONA "TAL CUAL", SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O IMPLÍCITA, INCLUIDAS, ENTRE OTRAS, LAS GARANTÍAS DE COMERCIABILIDAD, IDONEIDAD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN.<br>
EN NINGÚN CASO LOS AUTORES O PROPIETARIOS DE DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER RECLAMO, DAÑO O CUALQUIER OTRO TIPO DE RESPONSABILIDAD, YA SEA EN UNA ACCIÓN CONTRACTUAL, AGRAVIO U OTRO, DERIVADOS, FUERA DEL USO DEL SOFTWARE O EL USO U OTRAS DISPOSICIONES DEL SOFTWARE.


> @author		YisusCore<br>
@link		https://jcore.jys.pe/jcore<br>
@version		1.0.0<br>
@copyright	Copyright (c) 2018 - 2023, JYS Perú (https://www.jys.pe/)<br>

## Instalación

1. Copiar el archivo ZIP `index.zip` en el directorio de la aplicación.
2. En caso que el directorio de la aplicación sea diferente al directorio de acceso a la aplicación **(LLEGADA DEL LINK)**:

    2.1. Mover los archivos `index.php` y `.htaccess`
    2.2. Declarar la variable `SUBPATH` en el archivo `index.php` indicando el subdirectorio donde se encuentra el directorio de la aplicación

3. En caso que el archivo `init.php` no se encuentre en el mismo directorio que `index.php`:

    3.1. Modificar la variable `$init_php` en el archivo `index.php` indicando la ruta donde se encuentra el archivo `init.php`

4. En caso que se desee validar los requerimientos de la versión actual del núcleo `JCore`:

    4.1. Modificar la variable `$server_validation` en el archivo `index.php` indicando `TRUE`

5. En caso que el ambiente de desarrollo no está en `desarrollo`:

    5.1. Declarar la variable `ENVIRONMENT` en el archivo `index.php` indicando el ambiente de desarrollo el cual se encuentre el aplicativo (`pruebas` ó `produccion`)

6. En caso que el núcleo `JCore` no se encuentre en la subcarpeta `JCore` del directorio de la aplicación:

    6.1. Modificar la variable `$JCore_path` en el archivo `index.php` indicando la ruta al núcleo `JCore`

6. En caso que el desarrollo de la `APLICACIÓN` no se encuentre en la subcarpeta `APP` del directorio de la aplicación:

    6.1. Modificar la variable `$APP_path` en el archivo `index.php` indicando la ruta del desarrollo

6. En caso que la `APLICACIÓN` cuente con múltiples directorios `BASES`:

    6.1. Modificar la variable `$BASES_path` en el archivo `index.php` indicando las rutas de los directorios `BASES` considerando que el núcleo priorizará sus busquedas de archivos necesarios comenzando del primer directorio al último en aquella variable.<br><small>Los directorios `$APP_path` y `$JCore_path` se agregan automáticamente y en ese órden.</small>
