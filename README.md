# Promora Marketplace — PromoCodeEngine

Motor de códigos promocionales para Promora Marketplace, desarrollado como proyecto del
Examen Final del curso de Patrones de Diseño (TDR-PROMO-001).

Permite validar la elegibilidad de un código promocional para una orden, calcular el
descuento correspondiente y operar con cualquier entidad de orden mediante contratos, sin
depender de una implementación concreta. Las reglas de cada promoción se configuran desde
la base de datos en tiempo de ejecución, sin requerir cambios en el código ni un nuevo
despliegue.

## Stack tecnológico

- **PHP** 8.3
- **Laravel** 13
- **SQLite** (persistencia real, sin Docker; los tests corren contra una base en memoria)
- **PHPUnit** (unit + feature, con TDD y factories)

## Requisitos

- PHP >= 8.3
- Composer

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Levantar el proyecto

```bash
php artisan serve
```

## Correr los tests

```bash
php artisan test                    # todos los tests
php artisan test --testsuite=Unit   # solo unitarios (dominio, sin base de datos)
php artisan test --testsuite=Feature # solo de integración (endpoints)
```

## Arquitectura

El sistema sigue el estilo **MVC + DIP mínimo**: el dominio (`PromoCodeEngine` y todo lo que
lo rodea) se implementa en PHP puro, sin depender de Laravel, Eloquent ni HTTP. Laravel se
integra como capa de infraestructura sobre ese dominio. La inversión de dependencias se
concentra en dos contratos: `OrderableInterface` (para no acoplarse a una orden concreta) y
`PromoCodeRepositoryInterface` (para no acoplarse a Eloquent/MySQL/SQLite).

### Estructura de carpetas

```
app/
├── Application/PromoCode/        Casos de uso y Factory de reglas
├── Domain/PromoCode/             Dominio en PHP puro (sin dependencias de Laravel)
│   ├── Contracts/                Interfaces del dominio (los puertos del DIP)
│   ├── Discount/                 Estrategias de descuento (Strategy) + tope post-cálculo
│   ├── Validation/Fixed/         Validaciones fijas (Chain of Responsibility)
│   ├── Validation/Configurable/  Reglas configurables (Specification)
│   └── ValueObjects/             OrderContext, BuyerProfile, ValidationResult
├── Http/                         Controllers y Requests (endpoints)
├── Infrastructure/Persistence/   Adaptador Eloquent del repositorio (implementa el puerto)
├── Models/                       Modelos Eloquent
└── Providers/                    Bind del puerto de persistencia a su adaptador

database/
├── migrations/                   Esquema de las 8 tablas del dominio
└── factories/                    Factories Eloquent para los tests Feature

tests/
├── Unit/PromoCode/                Tests del dominio (sin base de datos)
├── Feature/                       Tests de integración de los endpoints
└── Factories/                     Builders de dominio en PHP puro (sin Eloquent)
```

## Patrones de diseño aplicados

| Patrón | Dónde | Por qué |
|---|---|---|
| **Chain of Responsibility** | `Domain/PromoCode/Validation/Fixed` | Las validaciones fijas (existencia, vigencia, estado activo) se ejecutan en orden estricto y cortan al primer fallo. |
| **Specification** | `Domain/PromoCode/Validation/Configurable` | Cada regla configurable es una especificación autocontenida; el motor las evalúa sin conocer su tipo concreto. |
| **Factory** | `Application/PromoCode/PromoCodeRuleFactory` | Traduce la configuración de reglas leída en runtime a instancias de `RuleSpecificationInterface`. |
| **Strategy** | `Domain/PromoCode/Discount` | Cada tipo de descuento (fixed, percent, tiered) encapsula su propio algoritmo de cálculo. |

## Endpoints

| Método | Ruta | Descripción |
|---|---|---|
| `POST` | `/api/promo-codes` | Crea un código promocional (con sus reglas y, si aplica, sus tramos). |
| `POST` | `/api/promo-codes/validate` | Valida un código contra una orden y devuelve el descuento calculado. |

## Documentación del diseño

La justificación completa de la arquitectura, los patrones seleccionados/descartados, los
principios SOLID aplicados y los trade-offs se documentan en el ASD del equipo (fuera de
este repositorio).
