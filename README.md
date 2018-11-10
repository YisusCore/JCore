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

### Requerimientos

##### Versión PHP
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

## CARACTERÍSTICAS DE LA VERSIÓN

### Variables Globales

Variable | Nombre | Descripción
---|---|---
DS | DIRECTORY_SEPARATOR | Separador de Directorios
HOMEPATH | DIRECTORIO DEL SITIO  | Directorio Raiz de donde es leído el APP
SUBPATH | SUBDIRECTORIO DEL SITIO | Subdirectorio donde se encuentra alojado el archivo `init.php`
ABSPATH | DIRECTORIO ABSOLUTO DEL SITIO | Carpeta donde se encuentra alojado el `init.php`
ROOTPATH | NÚCLEO JCORE | Ruta a la carpeta del núcleo JCore
APPPATH | PROCESOS DE APLICACIÓN | Ruta a la carpeta que contiene las funciones, configuraciones, objetos, procesadores y pantallas
ENVIRONMENT | AMBIENTE DE DESARROLLO | **Posibles valores:** <br>- desarrollo<br>- pruebas<br>- produccion
$BASES_path | DIRECTORIOS BASES | Array de los directorios base que buscará las estructuras de archivos<br>Se autoagregan los directorios `$APP_path` y `$JCore_path`


<br>

### Esqueleto de Directorios Base


Directorio | Detalle
---|---
displays | Contiene todos los manejadores del RESPONSE
processors | Contiene todos los manejadores del REQUEST
objects | Contiene todos los objetos
templates | Contiene partes de HTML que pueden ser leídos por las pantallas
the.configs | Contiene todas las configuraciones
the.functns | Contiene todas las funciones
the.classes | Contiene clases utilizables
the.libs | Contiene librerías a utilizar en la aplicación
translate | Contiene archivos para cambiar lenguajes

> Se procedió a cambiar las carpetas config, functions, class y libs de la versión V2 por anteponerle `the.` adelante lo cual ofrece una mayor seguridad y mejor orden en las carpetas.

<br>

### Archivos de Funciones Bases (the.functns)

Archivo | Detalle
---|---
_variables | Funciones de conjuntos de Variables
_mimes | Funciones y clase manipuladora de los mimes
_validacion | Funciones de validación
_security | Funciones de Seguridad
core | Funciones principales del núcleo
mngr.bbdd | Funciones manipuladores de las BBDDs
mngr.vrbls | Funciones manipuladores de (Array, Date, Strings, Numerics)
mngr.files | Funciones manipuladores de (Directory, Download, File)
mngr.html | Funciones manipuladores de (Html)
mngr.url | Funciones manipuladores de (URL)


@see [Lista de Funciones](README.funcs.md)

<br>

### Archivos de Configuraciones (the.configs)

Archivo | Detalle
---|---
config.php | Contiene las configuraciones básicas
hooks.php | Contiene todas las tareas programadas


<br>

### Configuraciones Básicas



Opción | Tipo | Descripción | Defecto
---|---|---|---
charset | `String` | Charset por Defecto | `UTF-8`
timezone | `String` | TimeZone por Defecto | `America/Lima`
lang | `String`&nbsp;`NULL` | Lenguaje por Defecto | `NULL`<br>Si es NULO, el sistema detecta automáticamente el lenguaje del usuario
subclass_prefix | `String` | Prefijo para Extensión de Clases | `MY_`
log | `Array` | Datos de Registros de Logs<br>(Si al ejecutar el filtro `save_logs` retorna `TRUE` entonces ya no se almacenará en el archivo) | @see [Opciones&nbsp;LOG](#opciones-log)
db - bd | `Array` | Datos de la primera conección de Base Datos | @see [Opciones&nbsp;DB&nbsp;-&nbsp;BD](#opciones-db-bd)
functions_files | `Array` | Listado de archivos de funciones | `[vacío]`
autoload_paths | `Array` | Listado de directorios donde buscar las clases no encontradas | `[vacío]`
www | `Boolean`&nbsp;`NULL` | WWW por Defecto<br>Si es NULO entonces no validará que se haya ingresado con el WWW deseado | `NULL`
https | `Boolean`&nbsp;`NULL` | HTTPS por Defecto<br>Si es NULO entonces no validará que se haya ingresado con el HTTP(s) deseado | `NULL`
allowed_http_methods | `Array` | Métodos autorizados para llamar los REQUESTs | `['GET', 'POST']`
default_method | `String` | El método por defecto para las uris que no se han indicado un parametro de método | `index`
home_display | `String` | El display por defecto para cuando el URI se encuentre vacío | `inicio`
error404_display | `String` | El display por defecto para cuando no se encuentre un display correcto | `error404`
images_zones | `Array` | Lista de Zonas o aplicaciones que permiten el procesamiento de la carga de imagenes. | @see [Opciones&nbsp;Zonas&nbsp;de&nbsp;Imágenes](#opciones-zonas-de-imagenes)
files | `Array` | Datos de la configuración para la carga de archivos en modo local (servidor local) | @see [Opciones&nbsp;Files](#opciones-files)

<br>

##### Opciones LOG


Opción | Tipo | Descripción | Defecto
---|---|---|---
path | `String` | Directorio donde se almacenarán los archivos  | `APPPATH . DS . 'logs'`
file_ext | `String` | Extensión del archivo a crear  | `csv`
file_permissions | `Integer` | Permisos del archivo a crear  | `0644`
format_line | `Callable` | Función que retorna la linea `String` que se agregará al archivo | `function ($message, $severity, $code, $filepath, $line, $trace, $meta)`

<br>

##### Opciones DB - BD


Opción | Tipo | Descripción | Defecto
---|---|---|---
host | `String` | Host del servidor mysql  | `localhost`
user | `String` | Usuario para conectar en el servidor  | `root`
pasw | `String` | Clave de la conección.´<BR>(Si es NULO entonces el usuario no requiere de clave)  | `mysql`
name | `String` | Nombre de la base datos autorizado | `intranet`
pref | `String` | Prefijo que se utilizará para la creación de tablas por defecto | `jc_`


