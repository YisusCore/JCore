# JCore

Copyright (c) 2018 - 2023, JYS Perú

Se otorga permiso, de forma gratuita, a cualquier persona que obtenga una copia de este software y archivos de documentación asociados (el "Software"), para tratar el Software sin restricciones, incluidos, entre otros, los derechos de uso, copia, modificación y fusión, publicar, distribuir, sublicenciar y / o vender copias del Software, y permitir a las personas a quienes se les proporciona el Software que lo hagan, sujeto a las siguientes condiciones:

El aviso de copyright anterior y este aviso de permiso se incluirán en todas las copias o porciones sustanciales del software.

EL SOFTWARE SE PROPORCIONA "TAL CUAL", SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O IMPLÍCITA, INCLUIDAS, ENTRE OTRAS, LAS GARANTÍAS DE COMERCIABILIDAD, IDONEIDAD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN.<br>
EN NINGÚN CASO LOS AUTORES O PROPIETARIOS DE DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER RECLAMO, DAÑO O CUALQUIER OTRO TIPO DE RESPONSABILIDAD, YA SEA EN UNA ACCIÓN CONTRACTUAL, AGRAVIO U OTRO, DERIVADOS, FUERA DEL USO DEL SOFTWARE O EL USO U OTRAS DISPOSICIONES DEL SOFTWARE.

> [JCore PHP v3](https://jcore.jys.pe/v3) Licensed by MIT<br>
Powered by [JYS Perú](https://www.jys.pe/) &copy; 2019 - 2023<br><small>Todos los derechos reservados</small>

### Requerimientos

##### Versión PHP Mínima
- [x] `5.7`

##### Módulos Apache
- [x] `mod_rewrite`
- [ ] `mod_cache`
- [ ] `mod_deflate`
- [ ] `mod_expires`
- [ ] `mod_filter`
- [ ] `mod_headers`

##### Extensiones PHP
- [x] `mbstring`
- [x] `iconv`
- [x] `zip`
- [x] `json`
- [x] `session`
- [x] `curl`
- [x] `gd`
- [x] `mysqli`
- [x] `hash`
- [ ] `fileinfo`

<BR>

### Variables Globales

Variable | Nombre | Descripción
---|---|---
DS | DIRECTORY_SEPARATOR | Separador de Directorios
HOMEPATH | DIRECTORIO DEL SITIO  | Directorio Raiz de donde es accedido al sitio
SUBPATH | SUBDIRECTORIO DEL SITIO | Subdirectorio donde se encuentra alojado los recursos del sitio <br><i><small>(Recomendado cuando se aloja multiples sitios o plataformas en un mismo hosting)</small></i>
ABSPATH | DIRECTORIO ABSOLUTO DEL SITIO | Equivalente a <i>HOMEPATH</i>&nbsp;<b>.</b>&nbsp;<i>SUBPATH</i>
APPPATH | PROCESOS DE APLICACIÓN | Ruta a la carpeta que contiene los archivos para el APP
ENVIRONMENT | AMBIENTE DE DESARROLLO | **Posibles valores:** <br>- desarrollo<br>- pruebas<br>- produccion
APPNMSP | NOMINACIÓN DE ENTORNO | Un identificador o nominación del APP
$BASES_path | DIRECTORIOS BASES | Array de los directorios base que buscará las estructuras de archivos<br><small>Se autoagregan los directorios `$APP_path` y `$JCore_path`</small>
ROOTPATH | DIRECTORIO DEL NÚCLEO | Directorio de JCore PHP


<br>

### Esqueleto de Directorios Base


Directorio | Detalle
---|---
configs | Contiene los archivos de configuración
<small>configs/</small>functions | Contiene funciones a usar
<small>configs/</small>classes | Contiene clases a usar
<small>configs/</small>libs | Contiene librerías a usar
<small>configs/</small>translate | Contiene las traducciones
<small>configs/</small>install.bbdd | Contiene las actualizaciones de la base datos
response | Contiene los archivos para el RESPONSE
<small>response/</small>structure | Contiene las estructuras de respuestas
<small>response/</small>html | <small>(Opcional)</small> <br>Contiene los archivos que se responderán para la WEB
<small>response/</small>cli | <small>(Opcional)</small> <br>Contiene los archivos que se responderán para las llamadas CLI
request | Contiene todos los manejadores del REQUEST
<small>request/</small>cli | <small>(Opcional)</small> <br>Contiene todos los manejadores del REQUEST para cuando sean llamados desde CLI
<small>request/</small>POST | <small>(Opcional)</small> <br>Contiene todos los manejadores del REQUEST para cuando sean llamados desde POST
objects | Contiene todos los objetos

> Si los directorios **opcionales** no existen, se buscarán los archivos solicitados en la carpeta padre

<br>

### Archivo ```configs/config.php```

Contiene las configuraciones de la aplicación<br><br>

##### Configuraciones Básicas:

Opción | Tipo | Descripción | Defecto
---|---|---|---
charset | `String` | Charset por Defecto | `UTF-8`
timezone | `String` | TimeZone por Defecto | `America/Lima`
lang | `String`&nbsp;/&nbsp;`NULL` | Lenguaje por Defecto<br><b><small>(NULO = Detecta Lenguaje del Navegador)</small></b> | `NULL`
db / bd / bbdd | `Array` | Datos de la primera conección de Base Datos<br><b><small>(No se conectará si no esta el atributo ```name```)</small></b> | ```['host' => 'localhost', 'user' => 'root', 'pasw' => 'mysql']```
www | `Boolean`&nbsp;/&nbsp;`NULL` | WWW por Defecto<br><b><small>(NULO = No valida cual se ha usado)</small></b> | `NULL`
https | `Boolean`&nbsp;`NULL` | HTTPS por Defecto<br><b><small>(NULO = No valida cual se ha usado)</small></b> | `NULL`
http_methods | `Array` | Métodos autorizados para procesar los REQUESTs | `['GET', 'POST']`
default_method | `String` | El método por defecto para las uris que no se han indicado un parametro de método | `index`
home_display | `String` | El display por defecto para cuando el URI se encuentre vacío | `Inicio`
error404_display | `String` | El display por defecto para cuando no se encuentre un display correcto | `Error404`
images_zones | `Array` | Lista de Zonas o aplicaciones que permiten el procesamiento de la carga de imagenes. | ```['uri' => HOST, 'abspath' => ABSPATH, 'path' => SUBDIR_FROM_ABSPATH, 'upload' => UPLOAD_SUBDIR, 'slug' => SLUG]```
files_zones | `Array` | Lista de Zonas o aplicaciones que permiten el procesamiento de la carga de archivos. | ```['uri' => HOST, 'abspath' => ABSPATH, 'path' => SUBDIR_FROM_ABSPATH, 'upload' => UPLOAD_SUBDIR]```
subclass_prefix | `String` | Prefijo para Extensión de Clases | `MY_`

<br>

### Archivo ```configs/hook.php```

Contiene las tareas programadas de la aplicación<br><br>


### Archivo ```configs/install.bbdd/require.php```

Contiene las actualizaciones de la base datos<br><br>


### Archivo ```ROOTPATH/configs/functions/@base.php```

Contiene las funciones base para la APP<br><br>
