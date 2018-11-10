# _basic

### define2()

Define la variable en caso de que aún no se haya definido la variable, esto para que no se produzca error

### is_php()

Determina si la versión de PHP es igual o mayor que el parametro

### is_cli()

Identifica si el REQUEST ha sido hecho desde comando de linea

### is_localhost()

Identificar si la aplicación está corriendo en modo local

*Se puede cambiar el valor durante la ejecución*

### regex()

Permite convertir un string para ser analizado como REGEXP

### protect_server_dirs()

Proteje los directorios base y los reemplaza por vacío o un parametro indicado

Utiliza la variable global `$BASES_path`

### mkdir2()

Crea los directorios faltantes desde la carpeta $base

Utiliza la variable global `$BASES_path`

### display_errors()

Identificar si la aplicación debe mostrar los errores o los logs

*Se puede cambiar el valor durante la ejecución*

### print_array()

Muestra los contenidos enviados en el parametro para mostrarlos en HTML

### print_r2()

@see [print_array()](#print_array)

### die_array()

Muestra los contenidos enviados en el parametro para mostrarlos en HTML y finaliza los segmentos

@see [print_array()](#print_array)

### ip_address()

Obtiene el IP del cliente

@ToDo ==Rediseñar y optimizar función==


---

# _variables

### numeros()

Obtener los números

### letras()

Obtener las letras en minúsculas

### letras_may()

Obtener las letras en mayúscula

### vocales()

Obtener las vocales

### tildes()

Obtener las tildes (letras) en minúsculas

### tildes_may()

Obtener las tildes (letras) en minúsculas

### simbolos()

Obtener las tildes (letras) en minúsculas

### meses()

Obtener los meses

### mes()

Obtener un mes

### dias()

Obtener los días

### dia()

Obtener un día

### vTab()

Obtener los caracteres de Tabulación cuantas veces se requiera

`define('vTab', vTab());`

### vEnter()

Obtener los caracteres de Salto de Linea cuantas veces se requiera

`define('vEnter',vEnter());`

### na ()

Obtener un numero aleatorio

##### Parámetros

- $digitos = 1

### la()

Obtener una letra aleatoria

##### Parámetros

- $digitos = 1
- bool $min = TRUE
- bool $may = TRUE
- bool $tilde = FALSE

### sa()

Obtener un símbolo aleatorio

### cs ()

Obtener un código seguro aleatorio

##### Parámetros

- $digitos = 60
- bool $min = TRUE
- bool $may = TRUE
- bool $tilde = FALSE
- bool $num = TRUE
- bool $sym = FALSE
- bool $spc = FALSE

### licencia()

Obtener un licencia aleatoria

### pswd ()

Obtener una clave aleatoria

### pswd_percent()

Obtener el porcentaje de seguridad de una clave


 Dígitos | 16 | 15 | 14 | 13 | 12 | 11 | 10 | 9 | 8 | 7 | 6 | 5
---| ---| ---| ---| ---| ---| ---| ---| ---| ---| ---| ---| ---
Percent | 100.00| 100.00 | 100.00 | 100.00 | 100.00| 99.90 | 99.85 | 99.35 | 93.80 | 82.65 | 65.65 | 49.90
Securit | 99.99| 99.98 | 99.74 | 99.49 | 98.02 | 95.33 | 90.21 | 82.89 | 74.06 | 63.24 | 54.09 | 45.71
Hacking | 16.32| 15.95 | 15.19 | 14.15 | 13.32 | 12.17 | 11.91 | 11.48 | 20.68 | 20.90 | 19.75 | 17.97


---


# _mimes

##### Variables Globales

- FT_COMPRESS
- FT_IMAGES
- FT_VIDEOS
- FT_DOCS
- FT_WEBS
- FT_OTHERS
- FT_DEF_MIME

##### Clase FT

``` php
class FT implements ArrayAccess
{
	/**
	 * instance()
	 * Retorna la instancia Única de la clase
	 *
	 * @static
	 * @return FT instance
	 */
	public static function instance()

	/**
	 * consulta()
	 * Función que permite buscar y retornar datos de una extensión
	 *
	 * @param string
	 * @return Mixed
	 */
	public function consulta($ext)
	
	/**
	 * getExtensionByMime()
	 * Permite buscar una extensión basada desde un MIME
	 *
	 * @param string
	 * @return Mixed
	 */
	public function getExtensionByMime($mime)
}
```

### FT()

Retorna la instancia de la clase FT

### get_mime()

Obtiene la información de una extensión

### filemime()

Obtiene el mime de un archivo indistinto a la extensión que tenga



---



# _validacion

### is_empty()

Validar si $valor está vacío

### def_empty()

Obtener un valor por defecto en caso se detecte que el primer valor se encuentra vacío

### non_empty()

Ejecutar una función si detecta que el valor no está vacío

### match()

Ejecuta de manera ordenada la función preg_match

### is_version()

Validar que el valor tenga el formato de una versión

### has_letter()

Valida que el valor tenga al menos una letra

### only_letters()

Valida que el valor sea solo letras

### has_number()

Valida que el valor tenga al menos un número

### only_numbers()

Valida que el valor sea solo números

### is_zero()

Valida que el valor sea ZERO (0)

### has_space()

Valida que el valor tenga al menos un espacio

### has_point()

Valida que el valor tenga al menos un punto

### only_letters_spaces()

Valida que el valor sea solo letras y/o espacios

### only_numbers_points()

Valida que el valor sea solo números y puntos

### is_ascii()

Valida si el valor es código ASCII

### is_date()

Valida si el valor es fecha

`$_regex = '^\d{4}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$';`

### is_time()

Valida si el valor es hora

`$_regex = '^([01][0-9]|2[0123])[:]([0-5][0-9])([:]([0-5][0-9])){0,1}$';`

### is_datetime()

Valida si el valor es fecha y hora

`$_regex = '^\d{4}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01]) ([01][0-9]|2[0123])[:]([0-5][0-9])([:]([0-5][0-9])){0,1}$';`

### words_len()

Valida si el valor es fecha

### min_len()

Valida que el valor tenga un largo mínimo

### max_len()

Valida que el valor tenga un largo máximo

### range_len()

Valida que el valor tenga un largo entre un mínimo y un máximo

### min_words()

Valida que el valor tenga un mínimo de palabras

### max_words()

Valida que el valor tenga un máximo de palabras

### range_words()

Valida que el valor tenga un rango de palabras

### is_mail()

Valida que el valor sea un correo válido

### is_ip()

Valida que el valor sea una IP válida



---


# _security

##### Clase Crypter

``` php
class Crypter
{
	/**
	 * Encrypt a message
	 * 
	 * @param string $message - message to encrypt
	 * @param string $key - encryption key
	 * @return string
	 */
	public function encrypt(string $message, string $key): string
	
	/**
	 * Decrypt a message
	 * 
	 * @param string $encrypted - message encrypted with safeEncrypt()
	 * @param string $key - encryption key
	 * @return string
	 */
	public function decrypt(string $encrypted, string $key): string
}
```

### encrypt()

Encrypt a message

##### Parámetros

- string $message
- string $key

### decrypt()

Decrypt a message

##### Parámetros

- string $encrypted
- string $key



---



# core

##### Variables Globales


- $JC_filters
- $JC_filters_defs
- $JC_actions
- $JC_actions_defs
- $gi_realsize


### filter_add()

Agrega funciones programadas para filtrar variables

##### Parámetros

- $key
- $function
- $priority = 50

### non_filtered()

Agrega funciones programadas para filtrar variables por defecto cuando no se hayan asignado alguno

### filter_apply()

Ejecuta funciones para validar o cambiar una variable

##### Parámetros

- $key
- &...$params


### action_add()

Agrega funciones programadas

##### Parámetros

- $key
- $function
- $priority = 50

### non_actioned()

Agrega funciones programadas por defecto cuando no se hayan asignado alguno

### action_apply()

Ejecuta las funciones programadas

##### Parámetros

- $key
- &...$params)

### redirect()

Redirecciona a una URL

##### Parámetros

- $url
- $query = NULL

### set_status_header()

Establece la cabecera del status HTTP

##### Parámetros

- $code = 200
- $text = ''

##### Códigos por Defecto

```
100	=> 'Continue',
101	=> 'Switching Protocols',

200	=> 'OK',
201	=> 'Created',
202	=> 'Accepted',
203	=> 'Non-Authoritative Information',
204	=> 'No Content',
205	=> 'Reset Content',
206	=> 'Partial Content',

300	=> 'Multiple Choices',
301	=> 'Moved Permanently',
302	=> 'Found',
303	=> 'See Other',
304	=> 'Not Modified',
305	=> 'Use Proxy',
307	=> 'Temporary Redirect',

400	=> 'Bad Request',
401	=> 'Unauthorized',
402	=> 'Payment Required',
403	=> 'Forbidden',
404	=> 'Not Found',
405	=> 'Method Not Allowed',
406	=> 'Not Acceptable',
407	=> 'Proxy Authentication Required',
408	=> 'Request Timeout',
409	=> 'Conflict',
410	=> 'Gone',
411	=> 'Length Required',
412	=> 'Precondition Failed',
413	=> 'Request Entity Too Large',
414	=> 'Request-URI Too Long',
415	=> 'Unsupported Media Type',
416	=> 'Requested Range Not Satisfiable',
417	=> 'Expectation Failed',
422	=> 'Unprocessable Entity',
426	=> 'Upgrade Required',
428	=> 'Precondition Required',
429	=> 'Too Many Requests',
431	=> 'Request Header Fields Too Large',

500	=> 'Internal Server Error',
501	=> 'Not Implemented',
502	=> 'Bad Gateway',
503	=> 'Service Unavailable',
504	=> 'Gateway Timeout',
505	=> 'HTTP Version Not Supported',
511	=> 'Network Authentication Required'
```

### http_code()

Establece la cabecera del status HTTP

@see [set_status_header()](#set_status_header)

### getUTC()

Obtiene el UTC del timezone actual

### APP()

Retorna la instancia del APP

### RSP()

Retorna la instancia del Reponse

### RTR()

Retorna la instancia del Router

### config()

Obtiene y retorna la configuración.

Utiliza la variable global `$BASES_path` y `ENVIRONMENT`

### _t()

Permite la traducción de una frase

##### Parámetros

- $frase
- $n = NULL
- $lang = NULL
- ...$sprintf

### request()

Obtiene los request ($_GET $_POST)


### url()

Obtiene la estructura y datos importantes de la URL

##### Datos Obtenibles


Dato | Tipo | Descripción | Ejemplo
---|--- | --- | ---
uri_subpath | `String` | Si el archivo index está dentro de una carpeta desde la raiz | `/subdir`
https | `Boolean` | Devuelve si usa https | `FALSE`
scheme | `String` | Devuelve 'http' o 'https' | `http`
host | `String` | Devuelve el host | `intranet.jys.net`
port | `Int` | Devuelve el port | `8080`
user | `String` | Solo si en la URL existe el user@ | `NULL`
pass | `String` | Solo si en la URL existe el pass@ | `NULL`
path | `String` | El path de la URL | `/usuarios/lista`
query | `String` | El query de la URL | `search=1&orderby=asc`
fragment | `String` | El fragment de la URL | `#user-1`
port-link | `String` | Devuelve el port en formato enlace | `:8080`
www | `Boolean` | Devuelve si usa WWW | `TRUE`
host-base | `String` | Devuelve el base host | `jys.net`
host-link | `String` | Devuelve el base host | `intranet.jys.net:8080`
host-clean | `String` | Devuelve el host sin puntos o guiones | `intranetjysnet`
host-uri | `String` | Devuelve el scheme mas el host-link | `http://intranet.jys.net:8080`
base | `String` | Devuelve la URL base hasta la aplicación | `http://intranet.jys.net:8080/subdir`
subpath | `String` | Devuelve la URL base hasta el alojamiento real de la aplicación | `/application`
abs | `String` | Devuelve la URL base hasta el alojamiento real de la aplicación | `http://intranet.jys.net:8080/subdir/application`
host-abs | `String` | Devuelve la URL base hasta el alojamiento real de la aplicación | `intranet.jys.net:8080/subdir/application`
full | `String` | Devuelve la URL completa incluido el PATH obtenido | `http://intranet.jys.net:8080/subdir/usuarios/lista`
full-wq | `String` | Devuelve la URL completa incluido el PATH obtenido | `http://intranet.jys.net:8080/subdir/usuarios/lista?search=1&orderby=asc`
cookie-base | `String` | Devuelve la ruta de la aplicación como directorio del cookie | `/subdir`
cookie-full | `String` | Devuelve la ruta de la aplicación como directorio del cookie hasta la carpeta de la ruta actual | `/subdir/usuarios/lista`
request | `array` | Obtiene todos los datos enviados | @see [request()](#request)
request_method | `string` | Obtiene el método solicitado el REQUEST | `GET`

### uri_rewrite_rules()

Obtiene las reglas de reescritura de los URIs

@ToDo ==Cambiar la función para que sea la clase RTR quien obtenga de manera automática las reglas==

### add_rewrite_rule()

Agrega una nueva regla de reescritura de url

@ToDo ==Cambiar la función para que sea la clase RTR quien agregue nuevas reglas==

### add_processor()

Agrega nuevos callbacks de procesamientos para las URIs

@ToDo ==Cambiar la función para que sea la clase RTR quien agregue nuevas reglas==

### add_display()

Agrega nuevo procesador display para las URIs

@ToDo ==Cambiar la función para que sea la clase RTR quien agregue nuevas reglas==

### uri_processors()

Obtiene los callbacks routes

@ToDo ==Cambiar la función para que sea la clase RTR quien obtenga de manera automática las reglas==

### uri_displays()

Obtiene los callbacks routes

@ToDo ==Cambiar la función para que sea la clase RTR quien obtenga de manera automática las reglas==

### class2()

Buscar una clase correcta

@ToDo ==Utiliozar los directorios BASE==

##### Parámetros

- $class
- $dir = NULL
- $ver = NULL
- $param = NULL

### obj()

Obtiene un clase objeto *(carpeta objects)*

##### Parámetros

- $class
- $ver = NULL
- $id = NULL

Si el `$id` es NULO y (la `$ver` es diferente a `Last` o no es versión), entonces `$id` tomará el valor de `$ver`

### exec_callable()

Intenta ejecutar una función callable o un método de una clase por iniciar o ya iniciazo

@ToDo ==Corregir *clase no existe* si la clase no corresponde a la clase llamada==

### _o()

Obtiene el ob_content de una función

### template()

Obtiene el archivo de una vista

### get_image()

Obtiene la ruta convertida de la imagen

### get_file()

Obtiene la ruta de un archivo

### _autoload()

Función a ejecutar para leer una clase que aún no ha sido declarada

### _error_handler()

Función a ejecutar al producirse un error durante la aplicación

### _exception_handler()

Función a ejecutar cuando se produzca una exception

### _shutdown_handler()

Función a ejecutar antes de finalizar el procesamiento de la aplicación

### mark()

Función que utiliza la clase BenchMark


---


# mngr.bbdd

##### Variables Globales


- $CON
- $CONs
- $MYSQL_QUERY
- $MYSQL_ERROR
- $MYSQL_ERRNO

@ToDo ==Almacenar todas los Querys Ejecutados en alguna variable ARRAY==


### mysqli_fetch_all()

Retorna toda la data de un `mysqli_result`

### dbd()

Cierra una conección de base datos

### cbd()

Inicia una conección de base datos

### esc()

Ejecuta la función `mysqli_real_escape_string`

### qp_esc()

Retorna el parametro correcto para una consulta de base datos

### sql()

Ejecuta una consulta a la Base Datos

##### Parámetros

- string $query
- $is_insert = FALSE
- mysqli $conection = NULL

### sql_data()

Ejecuta una consulta a la Base Datos

##### Parámetros

- string $query
- $return_first = FALSE
- $fields = NULL
- mysqli $conection = NULL

### sql_trans()

Procesa transacciones de Base Datos

**WARNING:** Si se abre pero no se cierra no se guarda pero igual incrementa AUTOINCREMENT<br>
**WARNING:** Se deben cerrar exitosamente la misma cantidad de los que se abren<br>
**WARNING:** El primero que cierra con error cierra todos los transactions activos (sería innecesario cerrar exitosamente las demas)

### sql_psw()

Obtiene el password de un texto

### sql_et()

Valida si existe una tabla

### sql_ect()

Valida si existe un campo de una tabla

### sql_ts()

Obtiene la estructura de una tabla

##### Datos Retornados

Dato | Tipo | Descripción
---|---|---
tblname | `String` | Nombre de la Tabla
keys | `Array` | Lista de Campos Llave
requireds | `Array` | Lista de Campos requeridos
protecteds | `Array` | Lista de Campos protegidos
referenceds | `Array` | Lista de Campos referenciados
hiddens | `Array` | Lista de Campos ocultos
columns | `Array` | Lista de Campos
key_column_usage | `Array` | Lista de Enlaces y Referencias
key_column_usage_referenced | `Array` | Lista de Tablas que estan enlazados o referenciados a esta tabla
tblname_singular | `String` | Nombre de la tabla en singular detectado
tblname_singular2 | `String` | Nombre de la tabla en singular detectado por si hubo error en la primera identificación
tblname_plural | `String` | Nombre de la tabla en plural detectado

```
$columns = 
SHOW FULL COLUMNS 
FROM $tabla`
;

$key_column_usage = 
SELECT 
    TABLE_SCHEMA, 
    TABLE_NAME, 
    REFERENCED_COLUMN_NAME, 
    COLUMN_NAME, 
    CONSTRAINT_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE 
    REFERENCED_TABLE_SCHEMA = $TABLE_SCHEMA 
    AND REFERENCED_TABLE_NAME = $tabla
;

$key_column_usage_referenced = 
SELECT 
    REFERENCED_TABLE_SCHEMA, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME, 
    COLUMN_NAME, 
    CONSTRAINT_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE 
    TABLE_SCHEMA = $TABLE_SCHEMA 
    AND TABLE_NAME = $tabla
	AND CONSTRAINT_NAME <> "PRIMARY" 
	AND REFERENCED_TABLE_SCHEMA IS NOT NULL 
	AND REFERENCED_TABLE_NAME IS NOT NULL
;

```

---



# mngr.vrbls

### grouping()

### array_search2()

### date_default_timezone_getUTC()

### timeinstant()

@ToDo ==Mejorar Función==

### date_iso8601()

@ToDo ==Mejorar Función==

### date_str()

@ToDo ==Mejorar Función==

### date2()

@ToDo ==Mejorar Función==

### date_()

@ToDo ==Mejorar Función==

### date_recognize()

@ToDo ==Mejorar Función==

### diffdates()

@ToDo ==Mejorar Función==

### convertir_tiempo()

@ToDo ==Mejorar Función==

### remove_invisible_characters()

Remove Invisible Characters

### utf8 ()

### clean_str()

### replace_tildes()

### quotes_to_entities()

### reduce_double_slashes()

### reduce_multiples()

### strtoslug()

### strtocapitalize()

### strtobool ()

### strtonumber ()

### transform_size()

@ToDo ==Validar Usabilidad==

### jys_rd()

@ToDo ==Mejorar Función==

---



# mngr.files

### csvstr()

### download()

Force Download, Generates headers that force a download to happen

### directory_map()

Create a Directory Map

Reads the specified directory and builds an array representation of it. Sub-folders contained with the directory will be mapped as well.



---



# mngr.html


##### Variables Globales

- $HTML_css
- $HTML_js

### register_css()

@ToDo ==Cambiar para que el RSP HEAD sea quien lo agregue==

### add_css ()

@ToDo ==Cambiar para que el RSP HEAD sea quien lo agregue==

### add_inline_css()

@ToDo ==Cambiar para que el RSP HEAD sea quien lo agregue==

### register_js()

@ToDo ==Cambiar para que el RSP HEAD sea quien lo agregue==

### add_js()

@ToDo ==Cambiar para que el RSP HEAD sea quien lo agregue==

### add_inline_js()

@ToDo ==Cambiar para que el RSP HEAD sea quien lo agregue==

### assets_reordered()

@ToDo ==Cambiar para que el RSP HEAD sea quien lo agregue==

### html_esc()

### extracto()

### youtube()

Obtener el código de YouTube

### compare()

### html_row()

### html_col()

### html_widget()

### html_compressor()

### js_compressor()

### css_compressor()

### json_compressor()

### array_html()

Convierte un Array en un formato nestable para HTML


---



# mngr.url

### subdomain()

### url_post()

### url_get()

### build_url()

Construye una URL
