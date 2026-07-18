# README — PromoCodeEngine para Promora Marketplace

## 1. Propósito del proyecto

Este proyecto consiste en diseñar e implementar un motor de códigos promocionales para **Promora Marketplace**, una plataforma que conecta compradores con proveedores de servicios digitales.

El problema actual es que cada nueva promoción obliga a modificar código y desplegar nuevamente la aplicación. La solución propuesta, llamada **PromoCodeEngine**, debe permitir que las reglas de las promociones se configuren desde la base de datos y se interpreten en tiempo de ejecución.

El motor debe cumplir tres responsabilidades principales:

1. Validar si un código promocional puede aplicarse a una orden.
2. Calcular el descuento correspondiente.
3. Trabajar con cualquier tipo de orden mediante contratos, sin depender de una clase concreta.

---

## 2. Objetivo principal

Construir un motor extensible, desacoplado y testeable que permita agregar nuevas reglas de validación y nuevos tipos de descuento sin modificar el flujo principal del sistema.

La solución debe aplicar principios SOLID y los siguientes patrones de diseño:

- Specification
- Factory
- Strategy
- Chain of Responsibility

---

## 3. Stack tecnológico

- PHP
- Laravel
- PHPUnit
- MySQL
- Eloquent ORM
- Docker Compose
- Laravel Service Container
- Model Factories
- TDD

---

## 4. Alcance

La implementación funcional incluye:

- Validación completa de códigos promocionales.
- Evaluación de reglas fijas.
- Evaluación de reglas configurables.
- Cálculo del descuento.
- Aplicación de un límite máximo de descuento.
- Endpoint HTTP.
- Pruebas unitarias.
- Pruebas Feature o de integración.

La persistencia se diseña y documenta, pero no necesariamente debe implementarse por completo si la asignación indica que queda fuera del alcance funcional.

---

# 5. Requerimientos funcionales

## RF-01. Tipos de descuento

El sistema debe soportar tres tipos de descuento.

### Fixed

Aplica un monto fijo en dólares.

Regla:

```text
descuento = min(monto_fijo, subtotal)
```

El descuento nunca puede superar el subtotal de la orden.

### Percent

Aplica un porcentaje sobre el subtotal.

Regla:

```text
descuento = subtotal × porcentaje
```

### Tiered

Aplica un porcentaje según el historial del comprador.

Solo deben considerarse órdenes completadas.

No deben incluirse:

- Órdenes canceladas.
- Borradores.
- Órdenes en proceso.

Debe utilizarse el porcentaje correspondiente al tramo más alto alcanzado.

Ejemplo conceptual:

```text
0–4 órdenes completadas   → 5%
5–9 órdenes completadas   → 10%
10 o más órdenes          → 15%
```

---

## RF-02. Validaciones fijas

Antes de evaluar reglas configurables, el sistema debe comprobar en este orden:

1. El código existe.
2. El código está vigente.
3. El código está activo.

Si una validación falla, el flujo debe detenerse inmediatamente.

---

## RF-03. Reglas configurables

Cada código promocional puede tener cero o más reglas activas almacenadas en la base de datos.

Reglas requeridas:

| Regla | Responsabilidad |
|---|---|
| `min_purchase_amount` | Exige un subtotal mínimo. |
| `eligible_categories` | Restringe el código a determinadas categorías y su jerarquía. |
| `first_order_only` | Permite utilizar el código únicamente en la primera compra. |
| `user_usage_limit` | Limita la cantidad de usos por comprador. |
| `global_usage_limit` | Limita el total de usos del código. |
| `global_amount_limit` | Limita el monto total acumulado de descuentos. |
| `restricted_usage` | Permite el uso solo a compradores autorizados. |

Las reglas deben cargarse dinámicamente durante la ejecución.

---

## RF-04. Regla de post-cálculo

Después de calcular el descuento puede aplicarse un límite máximo en dólares.

Ejemplo:

```text
descuento calculado = $40
límite máximo = $25
descuento final = $25
```

Este comportamiento debe estar separado de las estrategias de cálculo.

---

## RF-05. Errores semánticos

Cuando una validación falle, el sistema debe devolver un código de error entendible.

| Código | Significado |
|---|---|
| `invalid_code` | Código inexistente, inactivo o no válido para la categoría. |
| `expired_coupon` | Código fuera de su periodo de vigencia. |
| `usage_limit_reached` | Se alcanzó el límite global o por usuario. |
| `maximum_discount_reached` | Se alcanzó el monto máximo permitido. |
| `min_amount_required` | El subtotal no cumple el mínimo requerido. |
| `code_already_used` | El código de primera compra ya fue utilizado. |
| `restricted_usage` | El comprador no está autorizado. |

No se deben usar mensajes genéricos cuando exista un código de error específico.

---

## RF-06. Ciclo de vida

Estados permitidos:

```text
draft
active
paused
expired
```

Reglas principales:

- Solo un código `active` puede aplicarse.
- Un código `active` puede pasar a `paused`.
- Un código `paused` puede volver a `active` si aún está vigente.
- Un código vencido debe tratarse como `expired`.

No es obligatorio aplicar el patrón State, porque el comportamiento cambia muy poco entre estados.

---

## RF-07. Endpoint HTTP

Debe existir un endpoint que reciba:

- Código promocional.
- Datos de la orden.
- Contexto del comprador.

El endpoint debe:

1. Validar el payload.
2. Consultar el código mediante el repositorio.
3. Ejecutar validaciones fijas.
4. Construir reglas configurables.
5. Evaluarlas.
6. Calcular el descuento.
7. Aplicar el límite máximo.
8. Devolver una respuesta JSON.

Ejemplo sugerido:

```http
POST /api/promo-codes/validate
Content-Type: application/json
```

Ejemplo de request:

```json
{
  "code": "WELCOME20",
  "order": {
    "id": "ORD-1001",
    "subtotal": 150,
    "category_id": 8
  },
  "buyer": {
    "id": 25,
    "completed_orders": 3
  }
}
```

Respuesta exitosa:

```json
{
  "valid": true,
  "code": "WELCOME20",
  "discount": 30,
  "subtotal": 150,
  "total": 120,
  "error": null
}
```

Respuesta fallida:

```json
{
  "valid": false,
  "discount": 0,
  "error": "min_amount_required"
}
```

---

## RF-08. Registro de usos

Cada aplicación del código debe asociarse con:

- Orden.
- Comprador.
- Monto descontado.
- Estado de pago.

Los límites solo deben contar órdenes pagadas.

Las órdenes en proceso no deben contarse.

---

# 6. Requerimientos no funcionales

## RNF-01. Extensibilidad

Agregar una regla o un tipo de descuento nuevo no debe modificar el flujo principal del motor.

## RNF-02. Configuración dinámica

Las reglas activas deben recuperarse desde la base de datos en tiempo de ejecución.

## RNF-03. Desacoplamiento

El dominio no debe depender de:

- Controllers.
- Requests HTTP.
- Eloquent.
- MySQL.
- Una clase concreta de orden.

## RNF-04. Testeabilidad

Cada regla debe probarse de manera independiente.

Las pruebas deben usar:

- TDD.
- Factories.
- Mocks o fakes cuando corresponda.
- Casos válidos e inválidos.

No deben depender de fixtures estáticos.

## RNF-05. Separación de fases

La validación y el cálculo del descuento deben ser procesos independientes.

---

# 7. Arquitectura

## Enfoque recomendado

Usar una arquitectura **MVC de Laravel con inversión de dependencias en los bordes**.

No convertir todo el proyecto en una arquitectura hexagonal completa si eso agrega clases sin beneficio real.

La solución debe dividirse en tres zonas.

---

## 7.1. Dominio

Contiene lógica de negocio pura en PHP.

No debe importar Laravel, HTTP ni Eloquent.

Elementos principales:

```text
Domain/
├── Contracts/
│   ├── OrderableInterface.php
│   ├── RuleSpecificationInterface.php
│   ├── FixedValidationRuleInterface.php
│   ├── DiscountStrategyInterface.php
│   └── PromoCodeRepositoryInterface.php
├── Entities/
│   └── PromoCode.php
├── Enums/
│   └── PromoCodeStatus.php
├── ValueObjects/
│   ├── OrderContext.php
│   ├── BuyerProfile.php
│   └── ValidationResult.php
├── Rules/
│   ├── Configurable/
│   │   ├── MinPurchaseAmountRule.php
│   │   ├── EligibleCategoriesRule.php
│   │   ├── FirstOrderOnlyRule.php
│   │   ├── UserUsageLimitRule.php
│   │   ├── GlobalUsageLimitRule.php
│   │   ├── GlobalAmountLimitRule.php
│   │   └── RestrictedUsageRule.php
│   └── Fixed/
│       ├── CodeExistsRule.php
│       ├── CodeIsActiveRule.php
│       └── CodeIsWithinValidityPeriodRule.php
├── Discounts/
│   ├── FixedDiscountStrategy.php
│   ├── PercentDiscountStrategy.php
│   ├── TieredDiscountStrategy.php
│   ├── DiscountCalculator.php
│   └── MaxDiscountCap.php
└── Services/
    ├── PromoCodeEngine.php
    └── FixedRuleChain.php
```

---

## 7.2. Aplicación

Coordina el caso de uso.

No debe contener detalles HTTP ni consultas directas a Eloquent.

Elementos principales:

```text
Application/
├── UseCases/
│   └── ValidatePromoCodeUseCase.php
├── Factories/
│   └── PromoCodeRuleFactory.php
└── DTOs/
    ├── ValidatePromoCodeInput.php
    └── ValidatePromoCodeOutput.php
```

Responsabilidades de `ValidatePromoCodeUseCase`:

1. Consultar el código.
2. Ejecutar reglas fijas.
3. Pedir al Factory las reglas configurables.
4. Entregar las reglas al motor.
5. Ejecutar la estrategia de descuento.
6. Aplicar el límite máximo.
7. Devolver el resultado.

---

## 7.3. Infraestructura

Contiene detalles de Laravel y persistencia.

```text
Infrastructure/
├── Http/
│   ├── Controllers/
│   │   └── PromoCodeController.php
│   └── Requests/
│       └── ValidatePromoCodeRequest.php
├── Persistence/
│   ├── Models/
│   └── Repositories/
│       └── EloquentPromoCodeRepository.php
└── Providers/
    └── AppServiceProvider.php
```

El único bind esencial debe ser:

```php
$this->app->bind(
    PromoCodeRepositoryInterface::class,
    EloquentPromoCodeRepository::class
);
```

---

# 8. Contratos principales

## OrderableInterface

Representa cualquier orden que pueda evaluarse.

Ejemplo conceptual:

```php
interface OrderableInterface
{
    public function getId(): string|int;

    public function getSubtotal(): float;

    public function getOrderContext(): OrderContext;
}
```

El motor no debe conocer una clase concreta como `Order`, `ServiceOrder` o `MarketplaceOrder`.

---

## RuleSpecificationInterface

Contrato de las reglas configurables.

```php
interface RuleSpecificationInterface
{
    public function evaluate(OrderContext $context): ValidationResult;
}
```

Cada regla debe devolver un `ValidationResult`.

No debe lanzar excepciones técnicas para representar una validación de negocio esperada.

---

## FixedValidationRuleInterface

Contrato para validaciones obligatorias y ordenadas.

```php
interface FixedValidationRuleInterface
{
    public function validate(?PromoCode $promoCode): ValidationResult;
}
```

---

## DiscountStrategyInterface

```php
interface DiscountStrategyInterface
{
    public function calculate(
        PromoCode $promoCode,
        OrderContext $context
    ): float;
}
```

---

## PromoCodeRepositoryInterface

```php
interface PromoCodeRepositoryInterface
{
    public function findByCode(string $code): ?PromoCode;
}
```

El dominio no debe consultar Eloquent directamente.

---

# 9. Value Objects

## ValidationResult

Debe ser inmutable.

Campos sugeridos:

```text
valid
errorCode
message
```

Métodos sugeridos:

```php
ValidationResult::success();

ValidationResult::failure(
    string $errorCode,
    ?string $message = null
);
```

---

## OrderContext

Debe contener toda la información necesaria para las reglas.

Campos sugeridos:

```text
orderId
subtotal
categoryId
categoryAncestors
buyerProfile
paidPromoCodeUsages
globalPaidUsages
globalDiscountAmount
```

Debe ser inmutable.

---

## BuyerProfile

Campos sugeridos:

```text
buyerId
completedOrdersCount
paidPromoCodeUsages
isFirstOrder
```

---

# 10. Patrones de diseño

## 10.1. Specification

Se aplica a las reglas configurables.

Objetivo:

- Encapsular cada regla en una clase independiente.
- Evitar condicionales gigantes dentro del motor.
- Probar cada regla por separado.
- Permitir agregar reglas sin modificar `PromoCodeEngine`.

Ejemplo:

```text
MinPurchaseAmountRule
EligibleCategoriesRule
RestrictedUsageRule
```

`PromoCodeEngine` solo debe iterar una colección de especificaciones.

---

## 10.2. Factory

Se aplica en `PromoCodeRuleFactory`.

Objetivo:

- Traducir configuración de base de datos a objetos.
- Construir reglas dinámicamente.
- Centralizar el mapeo entre una clave y una clase.

Ejemplo conceptual:

```php
match ($rule->type) {
    'min_purchase_amount' =>
        new MinPurchaseAmountRule($rule->parameters),

    'first_order_only' =>
        new FirstOrderOnlyRule(),

    default =>
        throw new UnsupportedRuleException($rule->type),
};
```

El Factory construye reglas, pero no las evalúa.

---

## 10.3. Strategy

Se aplica al cálculo del descuento.

Estrategias:

```text
FixedDiscountStrategy
PercentDiscountStrategy
TieredDiscountStrategy
```

`DiscountCalculator` debe seleccionar o recibir la estrategia correcta y delegar el cálculo.

No debe contener la fórmula interna de cada descuento.

---

## 10.4. Chain of Responsibility

Se aplica a las validaciones fijas.

Orden requerido:

```text
existencia → vigencia → estado activo
```

Debe detenerse ante el primer error.

La cadena no debe evaluar reglas configurables.

---

# 11. Patrones descartados

## Decorator

No se utiliza porque las reglas no necesitan envolverse o anidarse entre sí.

## State

No se utiliza porque los estados del código no tienen suficiente comportamiento diferente para justificar una clase por estado.

## Template Method

No se utiliza porque la composición con reglas y estrategias es más flexible que una jerarquía de herencia.

## Observer

No se utiliza porque no existe un requerimiento actual de notificar eventos después de aplicar una promoción.

Puede considerarse como mejora futura.

---

# 12. Principios SOLID

## SRP

Cada clase debe tener una responsabilidad.

Ejemplos:

- `PromoCodeEngine`: evalúa reglas configurables.
- `PromoCodeRuleFactory`: crea reglas.
- `DiscountCalculator`: ejecuta el cálculo.
- `FixedRuleChain`: ejecuta validaciones fijas.
- `EloquentPromoCodeRepository`: obtiene y transforma datos.

## OCP

El sistema debe extenderse agregando clases.

No se debe modificar el motor para agregar una regla nueva.

## LSP

Toda implementación de un contrato debe poder sustituirse sin romper el flujo.

Ejemplos:

- Cualquier `OrderableInterface`.
- Cualquier `RuleSpecificationInterface`.
- Cualquier `DiscountStrategyInterface`.

## ISP

Las interfaces deben ser pequeñas y enfocadas.

No agregar métodos de persistencia, facturación o notificación a `OrderableInterface`.

## DIP

Los componentes de alto nivel deben depender de abstracciones.

El dominio depende de `PromoCodeRepositoryInterface`, no de Eloquent.

---

# 13. Flujo completo

```text
Cliente
  ↓
PromoCodeController
  ↓
ValidatePromoCodeRequest
  ↓
ValidatePromoCodeUseCase
  ↓
PromoCodeRepositoryInterface.findByCode()
  ↓
FixedRuleChain
  ├─ código existe
  ├─ código vigente
  └─ código activo
  ↓
PromoCodeRuleFactory
  ↓
Colección de RuleSpecificationInterface
  ↓
PromoCodeEngine
  ↓
DiscountCalculator
  ↓
Fixed | Percent | Tiered Strategy
  ↓
MaxDiscountCap
  ↓
ValidationResult + descuento final
  ↓
Response JSON
```

---

# 14. Comportamiento esperado del motor

Pseudocódigo:

```php
$promoCode = $repository->findByCode($input->code);

$fixedValidation = $fixedRuleChain->validate($promoCode);

if (!$fixedValidation->isValid()) {
    return ValidatePromoCodeOutput::failure(
        $fixedValidation->errorCode()
    );
}

$rules = $ruleFactory->createFrom($promoCode->rules());

$validation = $promoCodeEngine->validate(
    $input->order,
    $rules
);

if (!$validation->isValid()) {
    return ValidatePromoCodeOutput::failure(
        $validation->errorCode()
    );
}

$discount = $discountCalculator->calculate(
    $promoCode,
    $input->order->getOrderContext()
);

$finalDiscount = $maxDiscountCap->apply(
    $discount,
    $promoCode->maximumDiscountAmount()
);

return ValidatePromoCodeOutput::success(
    $finalDiscount
);
```

---

# 15. Diseño sugerido de base de datos

## promo_codes

```text
id
code
discount_type
discount_value
status
starts_at
expires_at
maximum_discount_amount
created_at
updated_at
```

## promo_code_rules

```text
id
promo_code_id
rule_type
parameters_json
is_active
created_at
updated_at
```

Ejemplo de `parameters_json`:

```json
{
  "minimum": 100
}
```

## promo_code_usages

```text
id
promo_code_id
order_id
buyer_id
discount_amount
payment_status
created_at
updated_at
```

Estados de pago sugeridos:

```text
pending
paid
failed
cancelled
refunded
```

Solo `paid` cuenta para los límites.

---

# 16. Pruebas requeridas

## Pruebas unitarias

Cada regla debe tener al menos:

- Caso exitoso.
- Caso fallido.
- Caso límite.

Ejemplos:

```text
MinPurchaseAmountRuleTest
EligibleCategoriesRuleTest
FirstOrderOnlyRuleTest
UserUsageLimitRuleTest
GlobalUsageLimitRuleTest
GlobalAmountLimitRuleTest
RestrictedUsageRuleTest
```

## Pruebas de estrategias

```text
FixedDiscountStrategyTest
PercentDiscountStrategyTest
TieredDiscountStrategyTest
MaxDiscountCapTest
```

## Pruebas de cadena

Validar:

- Código inexistente.
- Código vencido.
- Código inactivo.
- Corte temprano.
- Orden correcto de validaciones.

## Pruebas del caso de uso

Validar:

- El repositorio se consulta una sola vez.
- Las reglas configurables solo se construyen si pasan las reglas fijas.
- No se calcula descuento si la validación falla.
- Se aplica el máximo después del cálculo.
- Se devuelve el error correcto.

## Pruebas Feature

Validar el endpoint completo:

- Request válido.
- Payload inválido.
- Código inexistente.
- Código vencido.
- Regla configurable fallida.
- Descuento fixed.
- Descuento percent.
- Descuento tiered.
- Límite máximo.

---

# 17. Orden recomendado de implementación

## Fase 1. Dominio básico

1. Crear enums.
2. Crear value objects.
3. Crear contratos.
4. Crear entidad `PromoCode`.
5. Crear `ValidationResult`.

## Fase 2. Reglas fijas

1. Crear reglas.
2. Crear `FixedRuleChain`.
3. Crear pruebas.

## Fase 3. Specification

1. Crear reglas configurables.
2. Crear pruebas independientes.
3. Crear `PromoCodeEngine`.

## Fase 4. Factory

1. Crear estructura de configuración.
2. Crear `PromoCodeRuleFactory`.
3. Probar el mapeo de cada regla.

## Fase 5. Strategy

1. Crear estrategias.
2. Crear `DiscountCalculator`.
3. Crear `MaxDiscountCap`.
4. Probar fórmulas.

## Fase 6. Caso de uso

1. Crear DTO de entrada.
2. Crear DTO de salida.
3. Implementar `ValidatePromoCodeUseCase`.
4. Crear pruebas con repositorio fake o mock.

## Fase 7. Laravel

1. Crear Request.
2. Crear Controller.
3. Crear Repository Eloquent.
4. Registrar el bind.
5. Crear ruta.
6. Crear pruebas Feature.

---

# 18. Reglas para la IA que implemente este proyecto

La IA debe seguir estas instrucciones:

1. Leer este README completo antes de generar código.
2. No implementar todo de una sola vez.
3. Trabajar por fases pequeñas.
4. Aplicar TDD.
5. Crear primero la prueba y después la implementación.
6. No acoplar el dominio a Laravel.
7. No usar Eloquent dentro del dominio.
8. No crear un `if` o `match` gigante en `PromoCodeEngine`.
9. No mezclar validación con cálculo.
10. No agregar patrones que no resuelvan un requerimiento real.
11. Mantener los contratos pequeños.
12. Usar tipos estrictos.
13. Usar objetos inmutables cuando corresponda.
14. Evitar lógica de negocio en Controllers.
15. Mantener errores semánticos consistentes.
16. Preguntar antes de tomar una decisión que cambie la arquitectura.
17. Entregar código ejecutable, pruebas y explicación breve.
18. Indicar qué archivos se crean o modifican.
19. No inventar requisitos fuera de este documento.
20. Marcar claramente cualquier supuesto.

---

# 19. Definition of Done

Una tarea se considera terminada cuando:

- El código compila.
- Las pruebas pasan.
- La responsabilidad de la clase está clara.
- No se rompe la separación entre dominio e infraestructura.
- No se modifica el flujo principal para agregar una nueva regla.
- Los errores usan códigos semánticos.
- La lógica está respaldada por pruebas.
- El código cumple PSR-12.
- No existen dependencias innecesarias.
- Se actualiza la documentación cuando se toma una decisión nueva.

---

# 20. Prompt sugerido para el agente de IA

```text
Actúa como Tech Lead Senior especializado en PHP, Laravel, TDD, SOLID y patrones de diseño.

Antes de comenzar cualquier análisis o implementación, activa y utiliza la skill **Superpowers**. Debes aprovechar sus capacidades para planificar, razonar, dividir el trabajo, validar decisiones técnicas, detectar riesgos, revisar el código y mantener un proceso de implementación disciplinado.

Usa especialmente las capacidades de Superpowers para:

- Analizar el problema antes de escribir código.
- Crear un plan de implementación por fases.
- Aplicar brainstorming cuando existan decisiones ambiguas.
- Trabajar con TDD y escribir primero las pruebas.
- Depurar errores de forma sistemática.
- Revisar que la solución cumpla SOLID y los patrones definidos.
- Verificar el trabajo antes de declarar una tarea como terminada.
- Evitar implementar funcionalidades no solicitadas.
- Mantener una lista clara de tareas pendientes y completadas.
- Revisar los cambios finales y detectar posibles regresiones.

Debes ayudarme a construir PromoCodeEngine para Promora Marketplace usando este README como fuente principal de verdad.

Reglas obligatorias:

- Usa la skill Superpowers durante todo el proceso y menciona qué capacidad estás aplicando cuando sea relevante.
- Trabaja por fases pequeñas.
- Antes de implementar, explica brevemente el objetivo de la fase.
- Aplica TDD: primero pruebas y luego código.
- El dominio debe ser PHP puro y no depender de Laravel, Eloquent, HTTP ni MySQL.
- Usa Specification para reglas configurables.
- Usa Factory para construir reglas desde configuración.
- Usa Strategy para los tipos de descuento.
- Usa Chain of Responsibility para las validaciones fijas.
- Mantén separadas la validación y el cálculo.
- Depende de contratos, no de implementaciones concretas.
- No agregues patrones innecesarios.
- No inventes requisitos.
- Cuando exista una decisión ambigua, pregunta antes de implementarla.
- Indica siempre los archivos creados o modificados.
- Incluye pruebas unitarias y, cuando corresponda, pruebas Feature.
- Mantén compatibilidad con PSR-12 y tipos estrictos.
- Usa nombres claros y código listo para producción.

Primero revisa el README y propón un plan de implementación por fases. No escribas todavía todo el proyecto.
```

---

# 21. Resultado esperado

Al finalizar, el sistema debe permitir que Marketing configure campañas usando datos almacenados en la base de datos y que el motor interprete dichas configuraciones sin modificar su flujo principal.

El resultado debe ser:

- Extensible.
- Desacoplado.
- Testeable.
- Fácil de mantener.
- Compatible con Laravel.
- Independiente de una clase concreta de orden.
- Preparado para agregar nuevas reglas y descuentos.
