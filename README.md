## SETUP INSTRUCTIONS

1. Crear una base de datos **MySQL** con el nombre que desee.

2. Instale las dependencias de **composer**:

```bash
$ composer install
```

4. Ejecutar las migraciones **SQL** que se encuentran en el directorio `app/Migrations`, dentro del proyecto.

5. Ejecutar el archivo `generate-key` para crear el archivo `.env` y generar una **app key**, ejemplo:

```bash
$ php ./generate-key
```

6. Configure su archivo `.env` correctamente.

7. Ejecute las pruebas unitarias con:

```bash
$ ./vendor/bin/phpunit
```

> El **Trait** `App\Base\Tests\TruncateDatabase` truncará su base de datos luego de cada test unitario, si no desea este comportamiento elimínelo en la clase `Tests\Feature\UserTest`