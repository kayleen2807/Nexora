## Instalar PHP

*WINDOWS:*
Descargar en https://windows.php.net/download/ y buscar PHP Thread Safe x64 ZIP

Crear una carpeta en el disco: C:\php y extraer el zip que se descargo en esa carpeta

Verificar que exista C:\php.exe


*MAC:*
Primero verificar Homebrew, abre la terminal y con brew --version, si devuleve una version ya esta instalado.
Si no ejecutar:
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

Ya con Homebrew instalado escribir en la terminal: brew install php 

Verificar la instalacion con php -v, Resultado esperado: PHP 8.x.x (cualquier version pero preferiblemente de 8.3 en adelante)  


## Configurar PATH (windows)

Entra en configuracion -> Sistema -> Informacion -> Configuracion avanzada del sistema -> Variables de entorno 

En Variables de entorno:
Path -> Nuevo -> (agregar) C:\php

Despues verificar con php -v, Resultado esperado: PHP 8.x.x (cualquier version pero preferiblemente de 8.3 en adelante) 


## Configurar php.ini (windows)

Ir a C:\php
Copiar php.ini-development
Renombrar ese mismo a php.ini


## Activar extensiones (windows)

Buscar y quitar el ; en estas extensiones y guardar:
- extension_dir = "ext"
- extension=openssl
- extension=fileinfo
- extension=curl
- extension=zip
- extension=mbstring
- extension=pdo_pgsql
- extension=pgsql

Verificar modulos:
Abre una terminal y escribe php -m
Deberia aparecer un lista de las extensione y busca esto (si estan esta perfecto):
- curl
- fileinfo
- mbstring
- openssl
- pdo_pgsql
- pgsql
- zip


## Instalar Composer
*WINDOWS:*
Descargar en https://getcomposer.org/download/
Verificar en la terminal: composer --version


*MAC:*
En la terminal ejecutar: brew install composer
Verificar con: composer --version

## Instalar PostgreSQL
*WINDOWS:*
Descargar en https://www.postgresql.org/download/windows/
Durante la instalación marcar:
- PostgreSQL
- pgAdmin
- Command Line Tools
- Opcional: Stack Builder

Verificar en la terminar con : psql --version


*MAC:*
En la terminal ejecutar: brew install postgresql
Despues iniciar servicio con: brew services start postgresql
Verificar con: psql --version


## Instalar DBeaver
Descargar en https://dbeaver.io/download/


## Clonar proyecto/ repositorio
Mediante GitHub Desktop:
- Descargar e instalar en https://github.com/apps/desktop
- Iniciar sesion con tu cuenta de GitHub
- Seleccionar "File > Clone Repository"
- Ingresar la URL del repositorio o seleccionar uno de los repositorios disponibles
- Elegir el directorio local donde guardar el proyecto y dar clic en "Clone"

Mediante la Terminal:
- Elegir el directorio y abrir la terminar en ese directorio
- Ejecutar: git clone URL_DEL_REPOSITORIO


## Instalar dependencias Laravel
Entrar en Nexora/backend : cd Nexora/backend
En la terminar ejecutar: composer install


## Crear archivo .env (para entorno local)
*WINDOWS:*
En la terminal escribir copy .env.example .env

*MAC:*
En la terminal escribir cp .env.example .env


## Configurar PostgreSQL
Abrir .env y editar a :

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nexora_db
DB_USERNAME=postgres
DB_PASSWORD=TU_CONTRASEÑA

La contraseña es la que tienes para el usuario postgres, la que se configuro al instalar PostgreSQL

En ese mismo archivo buscar y modificar para evitar problemas iniciales (temporal):

SESSION_DRIVER=file

CACHE_STORE=file

QUEUE_CONNECTION=sync


## General clave Laravel
Para general la clave en una terminal dentro de Nexora/backend escribir: php artisan key:generate


## Importar base de datos
En DBeaver en la conexion de postgres crear una base de datos llamado *nexora_db* y de ahi importar *nexora_db.sql*

Despues verificar las tablas:
- rol
- usuario
- sucursal
- categoria
- producto
- inventario
- metodo_pago
- venta
- detalle_venta
- caja 
- corte_caja


## Limpiar configuracion
En una terminal dentro de Nexora/backend ejecutar:

php artisan config:clear

php artisan cache:clear

php artisan optimize:clear


## Iniciar servidor
En una terminal dentro de Nexora/backend ejecutar:

php artisan serve y abrir el URL que se ejecuta que principalmente es: http://127.0.0.1:8000

Si aparece la pantalla de laravel significa que el proyecto funciona correctamente
