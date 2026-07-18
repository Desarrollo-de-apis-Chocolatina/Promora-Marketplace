# Estructura Ideal del Proyecto (Laravel 12 + DDD)

```text
Promora-Marketplace/
│
├── app/
│   ├── Domain/                              ← ZONA DOMINIO · PHP puro, cero Laravel
│   │   └── PromoCode/
│   │       ├── PromoCodeEngine.php
│   │       ├── PromoCode.php                (entidad de dominio)
│   │       ├── PromoCodeStatus.php          (enum: draft|active|paused|expired)
│   │       │
│   │       ├── Contracts/
│   │       │   ├── OrderableInterface.php           ← frontera 1 (Order)
│   │       │   └── PromoCodeRepositoryInterface.php ← frontera 2 (persistencia)
│   │       │
│   │       ├── ValueObjects/
│   │       │   ├── OrderContext.php         (implements OrderableInterface)
│   │       │   ├── BuyerProfile.php
│   │       │   └── ValidationResult.php
│   │       │
│   │       ├── Validation/
│   │       │   ├── Fixed/                    ← Chain of Responsibility (RF-02)
│   │       │   │   ├── FixedValidationRuleInterface.php
│   │       │   │   ├── FixedRuleChain.php
│   │       │   │   ├── CodeExistsRule.php
│   │       │   │   ├── WithinValidityPeriodRule.php
│   │       │   │   └── IsActiveRule.php
│   │       │   │
│   │       │   └── Configurable/             ← Specification (RF-03)
│   │       │       ├── RuleSpecificationInterface.php
│   │       │       ├── MinPurchaseAmountSpec.php
│   │       │       ├── EligibleCategoriesSpec.php
│   │       │       ├── FirstOrderOnlySpec.php
│   │       │       ├── UserUsageLimitSpec.php
│   │       │       ├── GlobalUsageLimitSpec.php
│   │       │       ├── GlobalAmountLimitSpec.php
│   │       │       └── RestrictedUsageSpec.php
│   │       │
│   │       └── Discount/                     ← Strategy (RF-01) + post-cálculo (RF-04)
│   │           ├── DiscountStrategyInterface.php
│   │           ├── FixedDiscount.php
│   │           ├── PercentDiscount.php
│   │           ├── TieredDiscount.php
│   │           ├── DiscountCalculator.php
│   │           └── MaxDiscountCap.php
│   │
│   ├── Application/                          ← ZONA APLICACIÓN · PHP puro (conoce el dominio)
│   │   └── PromoCode/
│   │       ├── ValidatePromoCodeUseCase.php  (orquestador)
│   │       └── PromoCodeRuleFactory.php      ← Factory (RF-03 / RNF-02)
│   │
│   ├── Infrastructure/                       ← ZONA INFRAESTRUCTURA · acoplada a Laravel
│   │   └── Persistence/
│   │       └── Eloquent/
│   │           ├── EloquentPromoCodeRepository.php  (implements el puerto)
│   │           └── Mappers/
│   │               └── PromoCodeMapper.php   (Eloquent ⇄ entidad de dominio)
│   │
│   ├── Http/                                 ← ZONA INFRAESTRUCTURA · nativa de Laravel
│   │   ├── Controllers/
│   │   │   └── PromoCodeController.php        (RF-07: endpoint)
│   │   └── Requests/
│   │       └── ValidatePromoCodeRequest.php
│   │
│   ├── Models/                               ← Eloquent (infraestructura)
│   │   ├── PromoCode.php                     (modelo; ojo con el choque de nombre)
│   │   ├── PromoCodeRule.php                 (reglas configurables persistidas)
│   │   ├── PromoCodeRedemption.php           (conteo de usos, RF-08)
│   │   └── Order.php
│   │
│   └── Providers/
│       └── AppServiceProvider.php            (el único bind puerto→adaptador)
│
├── bootstrap/
│   ├── app.php                               (routing, middleware, providers — L11/12)
│   ├── providers.php
│   └── cache/
│
├── config/                                   (app.php, database.php, ...)
│
├── database/
│   ├── factories/                            ← RNF-04: factories, no fixtures
│   │   ├── PromoCodeFactory.php
│   │   ├── PromoCodeRuleFactory.php
│   │   └── OrderFactory.php
│   ├── migrations/
│   │   ├── xxxx_create_promo_codes_table.php
│   │   ├── xxxx_create_promo_code_rules_table.php
│   │   ├── xxxx_create_promo_code_redemptions_table.php
│   │   └── xxxx_create_orders_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
│
├── public/
│   └── index.php
│
├── resources/
│   └── views/
│
├── routes/
│   ├── api.php                               (POST /promo-codes/validate)
│   ├── web.php
│   └── console.php
│
├── storage/
│
├── tests/
│   ├── Unit/                                 ← TDD del dominio (sin framework)
│   │   └── PromoCode/
│   │       ├── PromoCodeEngineTest.php
│   │       ├── Validation/
│   │       │   ├── FixedRuleChainTest.php
│   │       │   └── Configurable/             (un test por Spec)
│   │       └── Discount/
│   │           ├── FixedDiscountTest.php
│   │           ├── PercentDiscountTest.php
│   │           ├── TieredDiscountTest.php
│   │           └── MaxDiscountCapTest.php
│   ├── Feature/                              ← integración del endpoint (con Laravel)
│   │   └── ValidatePromoCodeEndpointTest.php
│   └── TestCase.php
│
├── vendor/
├── .env
├── .env.example
├── artisan
├── composer.json
├── phpunit.xml
└── README.md
```

### Decisiones de esta estructura ideal

1. **Las 3 zonas del ADS son namespaces bajo `App\`**: `App\Domain`, `App\Application`, `App\Infrastructure`. No hay `composer.json` extra — el PSR-4 nativo (`App\ → app/`) las autoloadea solas.
2. **Controllers y Requests quedan en sus carpetas nativas** (`app/Http/...`), no en `Infrastructure/`. Es lo idiomático en Laravel y encaja con el ADS.
3. **`Mappers/PromoCodeMapper`** es opcional.
4. **Choque de nombre `PromoCode`**: Se maneja por namespace.
5. **`routes/api.php` no viene por defecto** en Laravel 11/12: se genera con `php artisan install:api`.
6. **Tres tablas**: Documentan RF-03 y RF-08.
