# OpenBook - Gestión de Libros con PHP Nativo

Aplicación para gestionar libros con CRUD completo y conexión a una API externa (Open Library).

## Requisitos

- Docker y Docker Compose
- PHP 8+
- Composer

## Levantar el proyecto

Desde la raíz del proyecto:

```bash
# Construir y levantar contenedor
docker-compose up -d

# Instalar dependencias PHP (dentro del contenedor si es necesario)
docker exec -it openbook_app composer install
