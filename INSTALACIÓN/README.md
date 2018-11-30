# JCore

Copyright (c) 2018 - 2023, JYS Perú

Se otorga permiso, de forma gratuita, a cualquier persona que obtenga una copia de este software y archivos de documentación asociados (el "Software"), para tratar el Software sin restricciones, incluidos, entre otros, los derechos de uso, copia, modificación y fusión, publicar, distribuir, sublicenciar y / o vender copias del Software, y permitir a las personas a quienes se les proporciona el Software que lo hagan, sujeto a las siguientes condiciones:

El aviso de copyright anterior y este aviso de permiso se incluirán en todas las copias o porciones sustanciales del software.

EL SOFTWARE SE PROPORCIONA "TAL CUAL", SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O IMPLÍCITA, INCLUIDAS, ENTRE OTRAS, LAS GARANTÍAS DE COMERCIABILIDAD, IDONEIDAD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN.<br>
EN NINGÚN CASO LOS AUTORES O PROPIETARIOS DE DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER RECLAMO, DAÑO O CUALQUIER OTRO TIPO DE RESPONSABILIDAD, YA SEA EN UNA ACCIÓN CONTRACTUAL, AGRAVIO U OTRO, DERIVADOS, FUERA DEL USO DEL SOFTWARE O EL USO U OTRAS DISPOSICIONES DEL SOFTWARE.


> @author		@YisusCore<br>
@link		https://jcore.jys.pe/<br>
@copyright	JYS Perú (https://www.jys.pe/) (c) 2018 - 2023, Todos los derechos reservados.<br>

## Instalación

1. Crear un archivo `index.php` en el directorio de acceso a la aplicación **(LLEGADA DEL LINK)**:

2. Definir una variable `HOMEPATH` con el valor `__DIR__`

3. Definir una variable `SUBPATH` con el nombre del subdirectorio donde se encuentra alojado los archivos de la aplicación<br><small>Debe empezar con `DIRECTORY_SEPARATOR` aún si el directorio es el mismo</small>

4. <small><b>(OPCIONAL)</b></small>, La variable `ABSPATH` siempre debe ser el valor `realpath(HOMEPATH . SUBPATH)`

5. <small><b>(OPCIONAL)</b></small>, Definir la variable `APPPATH`  con la ruta del directorio donde se encuentra los archivos de configuración y procesadores de la aplicación<br><small>Por defecto equivale a la variable `ABSPATH`</small>

6. <small><b>(OPCIONAL)</b></small>, Definir la variable `ENVIRONMENT`  con el ambiente actual de desarrollo. Posibles valores: <b><i>pruebas</i></b>, <b><i>produccion</i></b> y <b><i>desarrollo</i></b>.<br><small>Por defecto equivale `desarrollo`</small>

7. <small><b>(OPCIONAL)</b></small>, Definir la variable `APP_NAMESPACE`  con la nominación del ambiente actual<br><small>Por defecto equivale `JCore App`</small>

8. Llamar al archivo `/[JCore PATH]/JCore.php`

9. Crear un archivo `.htaccess` con los RewriteRule llamando al index.php

10. Crear dentro de `APPPATH` todos los archivos necesarios para la aplicación.

<hr>

### Contenido `.htaccess`

##### ReqriteRule para llamar al index.php

```
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    Rewriterule . index.php [L]
</IfModule>
```

##### Códigos para la Cache

```

<IfModule mod_cache.c>
    <IfModule mod_mem_cache.c>
        CacheEnable mem /
        MCacheSize 4096
        MCacheMaxObjectCount 100
        MCacheMinObjectSize 1
        MCacheMaxObjectSize 2048
    </IfModule>
</IfModule> 

<IfModule mod_mime.c>
  # Audio
    AddType audio/mp4                                   m4a f4a f4b
    AddType audio/ogg                                   oga ogg

  # JavaScript
    # Normalize to standard type (it's sniffed in IE anyways):
    # http://tools.ietf.org/html/rfc4329#section-7.2
    AddType application/javascript                      js jsonp
    AddType application/json                            json

  # Video
    AddType video/mp4                                   mp4 m4v f4v f4p
    AddType video/ogg                                   ogv
    AddType video/webm                                  webm
    AddType video/x-flv                                 flv

  # Web fonts
    AddType application/font-woff                       woff
    AddType application/vnd.ms-fontobject               eot

    # Browsers usually ignore the font MIME types and sniff the content,
    # however, Chrome shows a warning if other MIME types are used for the
    # following fonts.
    AddType application/x-font-ttf                      ttc ttf
    AddType font/opentype                               otf

    # Make SVGZ fonts work on iPad:
    # https://twitter.com/FontSquirrel/status/14855840545
    AddType     image/svg+xml                           svg svgz
    AddEncoding gzip                                    svgz

  # Other
    AddType application/octet-stream                    safariextz
    AddType application/x-chrome-extension              crx
    AddType application/x-opera-extension               oex
    AddType application/x-shockwave-flash               swf
    AddType application/x-web-app-manifest+json         webapp
    AddType application/x-xpinstall                     xpi
    AddType application/xml                             atom rdf rss xml
    AddType image/webp                                  webp
    AddType image/x-icon                                ico
    AddType text/cache-manifest                         appcache manifest
    AddType text/vtt                                    vtt
    AddType text/x-component                            htc
    AddType text/x-vcard                                vcf
</IfModule>

<IfModule mod_headers.c>
    Header unset ETag
  
  <filesMatch "\.(ico|jpe?g|png|gif|swf)$">
    Header set Cache-Control "max-age=31536000, public"
  </filesMatch>
  <filesMatch "\.(css|woff|ttf|woff2?)$">
    Header set Cache-Control "max-age=31536000, public"
  </filesMatch>
  <filesMatch "\.(js)$">
    Header set Cache-Control "max-age=31536000, private"
  </filesMatch>
  <filesMatch "\.(x?html?|php)$">
    Header set Cache-Control "max-age=600, private, must-revalidate"
  </filesMatch>
  
#   Header add Access-Control-Allow-Origin "*"
</IfModule>

FileETag None

<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresDefault                                      "access plus 1 year"

  # CSS
    ExpiresByType text/css                              "access plus 1 year"

  # Data interchange
    ExpiresByType application/json                      "access plus 0 seconds"
    ExpiresByType application/xml                       "access plus 0 seconds"
    ExpiresByType text/xml                              "access plus 0 seconds"

  # Favicon (cannot be renamed!)
    ExpiresByType image/x-icon                          "access plus 1 week"

  # HTML components (HTCs)
    ExpiresByType text/x-component                      "access plus 1 month"

  # HTML
    ExpiresByType text/html                             "access plus 0 seconds"

  # JavaScript

    ExpiresByType application/javascript                "access plus 1 year"

  # Manifest files
    ExpiresByType application/x-web-app-manifest+json   "access plus 0 seconds"
    ExpiresByType text/cache-manifest                   "access plus 0 seconds"

  # Media
    ExpiresByType audio/ogg                             "access plus 1 year"
    ExpiresByType image/gif                             "access plus 1 year"
    ExpiresByType image/jpeg                            "access plus 1 year"
    ExpiresByType image/png                             "access plus 1 year"
    ExpiresByType video/mp4                             "access plus 1 year"
    ExpiresByType video/ogg                             "access plus 1 year"
    ExpiresByType video/webm                            "access plus 1 year"

  # Web feeds
    ExpiresByType application/atom+xml                  "access plus 1 hour"
    ExpiresByType application/rss+xml                   "access plus 1 hour"

  # Web fonts
    ExpiresByType application/font-woff                 "access plus 1 year"
    ExpiresByType application/vnd.ms-fontobject         "access plus 1 year"
    ExpiresByType application/x-font-ttf                "access plus 1 year"
    ExpiresByType font/opentype                         "access plus 1 year"
    ExpiresByType image/svg+xml                         "access plus 1 year"
</IfModule>


<IfModule mod_deflate.c>
    # Insert filter on all content
    SetOutputFilter DEFLATE
	
    # Insert filter on selected content types only
<IfModule mod_filter.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript
</IfModule>

    # Netscape 4.x has some problems...
    BrowserMatch ^Mozilla/4 gzip-only-text/html

    # Netscape 4.06-4.08 have some more problems
    BrowserMatch ^Mozilla/4\.0[678] no-gzip

    # MSIE masquerades as Netscape, but it is fine
    BrowserMatch \bMSIE !no-gzip !gzip-only-text/html

    # Don't compress images
    SetEnvIfNoCase Request_URI \.(?:gif|jpe?g|png)$ no-gzip dont-vary

    # Make sure proxies don't deliver the wrong content
    Header append Vary User-Agent env=!dont-vary
</IfModule>

<IfModule mod_deflate.c>
<IfModule mod_filter.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
</IfModule>
 
# Limit bandwidth consumption
<IfModule mod_php5.c>
    php_value zlib.output_compression 16386
</IfModule>
```
