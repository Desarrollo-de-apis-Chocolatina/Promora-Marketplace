# Promora Marketplace — PromoCodeEngine

Motor de códigos promocionales para Promora Marketplace. El dominio está implementado en
PHP puro (sin dependencia directa de Laravel) y se integra con Laravel en la capa de
infraestructura.

## Tabla de contenidos

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Uso](#uso)
- [Tests](#tests)
- [API](#api)
- [Configuración](#configuración)
- [Troubleshooting](#troubleshooting)
- [Contribuir](#contribuir)
- [Recursos](#recursos)

## Requisitos

- PHP >= 8.3
- Composer
- Node.js + npm (opcional, solo para compilar assets con Vite)

## Instalación

Clonar el repositorio y moverse al directorio del proyecto, luego:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Crear la base de datos SQLite para desarrollo (necesario para levantar la app con
`php artisan serve` y probar los endpoints, por ejemplo desde Postman; los tests no lo
necesitan porque usan `:memory:`):

```bash
# Linux / macOS
mkdir -p database
touch database/database.sqlite
```

```powershell
# Windows PowerShell
New-Item -ItemType Directory -Force database
if (-not (Test-Path database/database.sqlite)) { New-Item -ItemType File database/database.sqlite }
```

Ejecutar las migraciones:

```bash
php artisan migrate
```

Instalar dependencias de Node (necesario si vas a usar `composer run dev`, ya que ese comando
levanta Vite además del servidor):

```bash
npm install
```

> Alternativa: `composer run setup` ejecuta todo lo anterior de una vez (install PHP,
> `.env`, `key:generate`, migraciones, `npm install` y build de assets).

## Uso

Levantar el servidor de desarrollo:

```bash
php artisan serve
```

Esto ya es suficiente para probar la API (por ejemplo, con la colección de Postman): las
rutas de `routes/api.php` devuelven JSON directamente y no dependen de Vite.

`npm run dev` solo hace falta si además vas a abrir la vista `resources/views/welcome.blade.php`
en el navegador, ya que es la única que carga assets (CSS/JS) a través de Vite. Si la necesitás,
corré esto en otra terminal:

```bash
npm run dev
```

## Tests

La suite está organizada en `Unit` y `Feature`. PHPUnit está configurado para usar SQLite
en memoria (ver `phpunit.xml`), así que no hace falta configurar una base de datos aparte.

Ejecutar toda la suite:

```bash
php artisan test
```

Ejecutar solo un grupo:

```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

## API

Rutas principales (ver `routes/api.php`):

| Método | Ruta                        | Descripción                                                |
|--------|-----------------------------|-------------------------------------------------------------|
| POST   | `/api/promo-codes`          | Crea un código promocional (incluye reglas y tramos si aplica). |
| POST   | `/api/promo-codes/validate` | Valida un código frente a una orden y devuelve el descuento. |

Para pruebas manuales hay una colección Postman en `postman/`
(`postman/Promora-PromoCodeEngine.postman_collection.json`).

## Configuración

- Base de datos por defecto en desarrollo: `database/database.sqlite` (se puede cambiar
  editando `.env`).
- Los tests usan `DB_CONNECTION=sqlite` y `DB_DATABASE=:memory:` (ver `phpunit.xml`).

## Troubleshooting

- **Las migraciones fallan por permisos o archivo inexistente**: confirmar que existe
  `database/database.sqlite` y que el usuario tiene permisos de escritura sobre esa carpeta.
- **npm falla al compilar assets**: ejecutar `npm install` y luego `npm run build`.

## Contribuir

1. Crear un branch con el cambio propuesto.
2. Ejecutar los tests y confirmar que pasan.
3. Abrir un PR con una descripción clara.

## Recursos

- Colección Postman: `postman/Promora-PromoCodeEngine.postman_collection.json`
- Rutas: `routes/api.php`
