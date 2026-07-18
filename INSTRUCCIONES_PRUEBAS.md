# Guía de Pruebas: Reglas Configurables y Factories (Gabriel)

Este documento detalla cómo ejecutar la batería de pruebas (TDD) para la sección de **Reglas Configurables** del PromoCodeEngine. Las pruebas no requieren conexión a base de datos, ya que utilizan mocks y *Test Data Builders*.

## Requisitos Previos

Cualquier miembro del equipo que quiera ejecutar estas pruebas debe tener instaladas las dependencias del proyecto. Como la carpeta `vendor` no se sube a GitHub, debes instalarla localmente ejecutando:

```bash
composer install
```

*(Si no tienes PHP y Composer instalados nativamente, pero tienes Docker, puedes usar: `docker run --rm -v "${PWD}:/app" -w /app composer install`)*

## Ejecutando las pruebas (PHPUnit)

Las pruebas están ubicadas en la carpeta `tests/Unit/Rules/Configurable`. Hemos escrito pruebas exhaustivas comprobando que las reglas devuelven objetos `ValidationResult` válidos o sus respectivos códigos de error semántico requeridos en el TDR.

Para ejecutar **todas las pruebas** de las reglas configurables al mismo tiempo, corre el siguiente comando en tu terminal:

```bash
./vendor/bin/phpunit tests/
```

### Ejecutar una prueba específica

Si estás revisando una regla en específico (por ejemplo, el límite mínimo de compra), puedes aislar la prueba pasándole la ruta del archivo:

```bash
./vendor/bin/phpunit tests/Unit/Rules/Configurable/MinPurchaseAmountRuleTest.php
```

## Resumen de Reglas Probadas

Las pruebas validan que no se rompan las siguientes condiciones:
1. **MinPurchaseAmountRule**: Falla con `min_amount_required`.
2. **EligibleCategoriesRule**: Falla con `invalid_code`.
3. **FirstOrderOnlyRule**: Falla con `code_already_used`.
4. **UserUsageLimitRule**: Falla con `usage_limit_reached`.
5. **GlobalUsageLimitRule**: Falla con `usage_limit_reached`.
6. **GlobalAmountLimitRule**: Falla con `maximum_discount_reached`.
7. **RestrictedUsageRule**: Falla con `restricted_usage`.

Todas las reglas respetan el **Dependency Inversion Principle (DIP)**. Si la regla requiere información histórica (como conteos globales), depende estrictamente del contrato `PromoCodeRepositoryInterface`, el cual ha sido "mockeado" dentro de las pruebas.
