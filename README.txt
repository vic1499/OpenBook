# OpenBook

## Descripción
OpenBook es un sistema de gestión de libros construido con PHP, SQLite y la Open Library API.  
Permite registrar usuarios, iniciar sesión y gestionar libros (crear, listar, actualizar, eliminar).

## Requisitos
- PHP 8+
- Composer
- SQLite
- Docker 


## Instalación

1. Clonar el repositorio:

git clone https://github.com/vic1499/OpenBook.git
cd OpenBook
Instalar dependencias:

composer install
Asegurarse de que la carpeta de la base de datos exista y tenga permisos de escritura:

mkdir -p /var/www/sqlite
chmod 777 /var/www/sqlite

Ejecutar con Docker
Construir la imagen:

docker build -t openbook-app .
Correr el contenedor:

docker run -d -p 8080:80 --name openbook-app openbook-app

Acceder a la aplicación desde:
http://localhost:8080


openbook-app → nombre de la imagen y contenedor
8080:80 → puerto local mapeado al contenedor

Uso de la aplicación: 
Registro de usuario: public/register.php

Login de usuario: public/index.php

Gestión de libros: desde la interfaz principal, puedes crear, listar, actualizar y eliminar libros.

CSRF token incluido en todos los formularios para mayor seguridad.

## API

La aplicación incluye los siguientes endpoints, accesibles vía `api.php`:

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST   | /register           | Registrar un nuevo usuario |
| POST   | /login              | Iniciar sesión |
| POST   | /logout             | Cerrar sesión |
| GET    | /books              | Listar todos los libros |
| GET    | /books/search?q=... | Buscar libros por título o autor |
| POST   | /books/create       | Crear un libro (requiere `title`, `author`, `isbn`) |
| POST   | /books/update       | Actualizar un libro existente (requiere `id`) |
| DELETE / POST | /books/delete  | Eliminar un libro (requiere `id`) |
| GET    | /books/{id}         | Obtener un libro por ID |

> Todos los endpoints requieren envío del token CSRF para seguridad.  
> Las descripciones de los libros se obtienen directamente de la Open Library API (en inglés)


