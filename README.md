# Lechuga Negra - SettingManager para Laravel

Este paquete de Laravel proporciona una solución centralizada para la gestión de configuraciones de aplicación agrupadas por módulo, permitiendo almacenar, consultar y actualizar valores de configuración en base de datos con soporte para múltiples tipos de dato, caché automático y registro de auditoría.

## Características Principales

* **Gestión por módulo:** Organiza las configuraciones en módulos lógicos para facilitar su administración y búsqueda.
* **Tipos de dato:** Soporte para `string`, `integer`, `float`, `boolean`, `json` y `array`, `encrypted` con casteo automático en la capa de modelo.
* **Caché automático:** Los valores se cachean automáticamente y se invalidan al actualizarse.
* **Registro de auditoría:** Cada creación o modificación de un setting queda registrada en la tabla `settings_logs`.
* **Comando Artisan:** Creación de nuevos settings mediante el comando `settings:create`, sin necesidad de modificar seeders.
* **Endpoints REST:** Consulta y actualización de settings a través de una API con restricción por módulo.

## Instalación

1.  **Crear grupo de paquetes:**

    Crear la carpeta packages en la raíz del proyecto e ingresar a la carpeta:

    ```bash
    mkdir packages
    cd packages
    ```

    Crear el grupo de carpetas dentro de la carpeta creada, e ingresar a la carpeta:

    ```bash
    mkdir lechuganegra
    cd lechuganegra
    ```

2.  **Clonar el paquete:**

    Clonar el paquete en el grupo de carpetas creado y renombrarlo para que el Provider pueda registrarlo en la instalación:

    ```bash
    git clone https://github.com/mtorres-lechuganegra/package-settingmanager.git settingmanager
    ```

3.  **Configurar composer del proyecto:**

    Dirígete a la raíz de tu proyecto, edita tu archivo `composer.json` y añade el paquete como repositorio:

    ```json
    {
        "repositories": [
            {
                "type": "path",
                "url": "packages/lechuganegra/settingmanager"
            }
        ]
    }
    ```

    También deberás añadir el namespace del paquete al autoloading de PSR-4:

    ```json
    {
        "autoload": {
            "psr-4": {
                "LechugaNegra\\SettingManager\\": "packages/lechuganegra/settingmanager/src/"
            }
        }
    }
    ```

4.  **Ejecutar composer require:**

    Después de editar tu archivo, abre tu terminal y ejecuta el siguiente comando para agregar el paquete a las dependencias de tu proyecto:

    ```bash
    composer require lechuganegra/settingmanager:@dev
    ```

5.  **Publicar archivo de configuración:**

    Ejecuta el siguiente comando para copiar el archivo de configuración del paquete a la carpeta `config` del proyecto:

    ```bash
    php artisan vendor:publish --tag=settingmanager-config
    ```

    Esto te permitirá personalizar el comportamiento del paquete desde tu proyecto.

6.  **Ejecutar las migraciones:**

    Ejecuta las migraciones del paquete para crear las tablas necesarias en la base de datos:

    ```bash
    php artisan migrate --path=packages/lechuganegra/settingmanager/src/Database/Migrations
    ```

    Esto creará las tablas `settings` y `settings_logs`.

7.  **Limpiar la caché:**

    Limpia la caché de configuración y rutas para asegurar que los cambios se apliquen correctamente:

    ```bash
    php artisan config:clear
    php artisan config:cache
    php artisan route:clear
    php artisan route:cache
    ```

8.  **Regenerar clases:**

    Regenerar las clases con el cargador automático "autoload":

    ```bash
    composer dump-autoload
    ```

## Uso

### Endpoints del Servicio

Puede importar el archivo `postman_collection.json` que se ubica en la carpeta `docs` de la raíz del paquete.

### Crear un nuevo setting

Los settings no se crean desde el API. Para registrar un nuevo setting, utiliza el comando Artisan `settings:create`:

```bash
php artisan settings:create {module} {key} {type} [--group=] [--value=] [--description=] [--inactive] [--locked]
```

**Ejemplos:**

```bash
php artisan settings:create general maintenance_mode boolean --value="false" --description="Modo mantenimiento del sitio"

php artisan settings:create mail smtp_host string --group="smtp" --value="smtp.gmail.com" --description="Host del servidor SMTP"
```

> Si el setting ya existe, el comando no lo modifica ni genera error — simplemente lo omite.

### Variable de caché

Puedes configurar el tiempo de vida del caché de settings mediante la siguiente variable de entorno:

```nginx
SETTING_MANAGER_CACHE_TTL=3600
```

> Este paquete es compatible con `lechuganegra/authmanager`. Si lo tienes instalado, puedes utilizar el guard `api` que provee para la autenticación de los endpoints de settings.
