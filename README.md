# MeteoApp — Aplicación del Tiempo con Docker

Este documento recoge la práctica de montaje de una aplicación meteorológica desplegada en una instancia AWS mediante Docker Compose, con un contenedor Apache+PHP y otro con MariaDB.

---

## Descripción del Proyecto

Aplicación web desarrollada en PHP con arquitectura **MVC** que permite consultar el tiempo de cualquier ciudad utilizando la API de **OpenWeatherMap**.

### Funcionalidades principales

- Búsqueda de ciudad con selección entre múltiples resultados (5 resultados).
- Consulta del tiempo actual.
- Previsión por horas (próximas 24h en intervalos de 3h).
- Previsión semanal (7 días).
- Historial de búsquedas.

---

## Estructura del Proyecto

```
apache
    Dockerfile              # Creación de una imagen personalizada con Ubuntu 22.04, Apache y PHP
controllers
    MeteoController.php     # Controlador principal
css
    style.css
db
    db.php                  # Conexión entre MariaDB y php
    init.sql                # Script de creación de la tabla historial
models
    MeteoModel.php          # Consultas a la API OpenWeatherMap
    HistorialModel.php      # Operaciones sobre la tabla historial
views
    index.php               # Formulario de búsqueda
    resultados.php          # Lista de ciudades encontradas
    actual.php              # Tiempo actual
    horas.php               # Previsión por horas
    semana.php              # Previsión semanal
    historial.php           # Historial de búsquedas
    header.php              # Barra de navegación
    View.php                # Clase para cargar vistas
.env                        # Variables de entorno (credenciales de la BD y API key)
docker-compose.yaml         # Composición del entorno
```

---

## Arquitectura MVC

La aplicación sigue el patrón **Modelo–Vista–Controlador**:

- **Modelo** (`MeteoModel.php` y `HistorialModel.php`): Gestiona el acceso a los datos, tanto a la API como a la base de datos.
- **Vista** (`views`): Es lo que ve el usuario.
- **Controlador** (`MeteoController.php`): Hace la conexión entre los modelos y las vistas.

---

## Configuración con Docker Compose

Se usan dos servicios definido en `docker-compose.yaml`:

- **`web`**: Se utiliza una imagen construida a partir del `Dockerfile`. Expone el puerto 80 del host mapeado al 80 del contenedor. Monta la maquina en `/var/www/html`.
- **`db`**: Se utiliza la imagen `mariadb:latest`. Expone el puerto 3306 del host mapeado al 3306 del contenedor. Usa un volumen llamado `mysql_data` y ejecuta `init.sql` al arrancar para crear la tabla historial.

### `docker-compose.yaml`

```yaml
services: 
  web:
    build: ./apache
    ports: 
      - 80:80
    environment: 
      - MYSQL_HOST=db
      - MYSQL_NAME=${MYSQL_DATABASE}
      - MYSQL_USER=${MYSQL_USER}
      - MYSQL_PASSWORD=${MYSQL_PASSWORD}
      - API_KEY=${API_KEY}
    depends_on:
      - db
    volumes:
      - ./:/var/www/html

  db:
    image: mariadb:latest
    ports:
      - 3306:3306
    environment: 
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
      - MYSQL_DATABASE=${MYSQL_DATABASE}
      - MYSQL_USER=${MYSQL_USER}
      - MYSQL_PASSWORD=${MYSQL_PASSWORD}
    volumes:
      - mysql_data:/var/lib/mysql
      - ./db:/docker-entrypoint-initdb.d 

volumes:
  mysql_data:
```

---

## Dockerfile del Servicio Web

Imagen personalizada basada en **Ubuntu 22.04** que instala Apache2, PHP y los módulos necesarios:

```dockerfile
FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive 
ENV TZ=Europe/Madrid

RUN apt-get update \
    && apt-get install -y apache2 \
    && apt-get install -y php \
    && apt-get install -y libapache2-mod-php \
    && apt-get install -y php-mysql \
    && apt-get install -y php-curl \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2

EXPOSE 80

ENTRYPOINT ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
```

**Lo que realiza este Dockerfile:**

- Ubuntu 22.04 como base.
- Instalación de Apache2 y PHP junto con los módulos `libapache2-mod-php`, `php-mysql` y `php-curl`.
- Expone el puerto 80 y arranca Apache.

---

## Variables de Entorno

Las credenciales y la clave de la API se gestionan mediante un fichero `.env`:

```env
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=meteo
MYSQL_USER=usuario_principal
MYSQL_PASSWORD=123456789
API_KEY=<tu_api_key_de_openweathermap>
```

---

## Base de Datos

El fichero `init.sql` se ejecuta automáticamente al iniciar el contenedor `db` y crea la tabla de historial:

```sql
CREATE TABLE historial (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  ciudad VARCHAR(50) NOT NULL,
  latitud DECIMAL(10, 8) NOT NULL,
  longitud DECIMAL(11, 8) NOT NULL,
  tipo VARCHAR(20) NOT NULL,
  fecha_consulta DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

La clase db en `db.php` gestiona la conexión mediante **PDO**, leyendo las credenciales de las variables de entorno:

```php
$dsn = "mysql:host=db;dbname=" . getenv('MYSQL_NAME');
$dbh = new PDO($dsn, getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
```



---

## MeteoController

| Método | Descripción |
|---|---|
| `buscarOpciones()` | Busca ciudades que coincidan con el nombre introducido |
| `procesarBusqueda()` | Según la vista destino solicitada, llama al método de modelo correspondiente y llama a la función `cargarVista()`|
| `cargarVista()` | Prepara un array con los datos para posteriormente pasarselos a la vista seleccioanda mediante un switch|

---

## MeteoModel

Realiza las siguientes llamadas a la API de **OpenWeatherMap**:

| Método | Descripción |
|---|---|
| `ObtenerCoordenadas()` | Traduce nombre de ciudad a lat/lon |
| `BuscarOpcionesCiudad()` | Devuelve hasta 5 ciudades coincidentes |
| `DatosClimaticos()` | Tiempo actual |
| `DatosClimaticosHoras()` | Previsión próximas 24h |
| `DatosClimaticosSemanales()` | Previsión semanal agrupada por día |

---
## Ejecución
- Entrada en la maquina AWS
- Ejecución del comando `cd /var/www/html`
- Ejecución del comando `docker-compose up -d`
- Entrada a la URL <http://52.55.7.232>
