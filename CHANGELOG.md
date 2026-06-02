# Changelog

All notable changes to `ss-ipg/value-objects` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-06-02

PHP 8.4+ forward compatibility (post-`round()` rewrite), plus type-safety and contract-strictness work. See [`UPGRADING.md`](UPGRADING.md) for a v1-to-v2 migration guide with code examples.

### Added

- Arithmetic methods on `NumericTrait`: `add`, `subtract`, `multiply`, `divide`. Promotion follows PHP's native semantics; `IntegerValue` auto-promotes to `FloatValue` when results are non-whole. `CurrencyValue` and `PercentValue` preserve their subclass through arithmetic.
- `Equatable` interface as the marker for value objects that define equality semantics. Implemented by all four primitive types.
- `getComparisonPrecision()` method on `AbstractValue` (default `PHP_FLOAT_DIG`) and `FloatValue` (returns `$this->precision`).
- `SSIPG\ValueObjects\Testing\ValueComparator`: a PHPUnit Comparator that routes `assertEquals` on value-object pairs through `Equatable::eq()`. Eliminates ULP-level float fragility under PHP 8.4+.
- `SSIPG\ValueObjects\Testing\ValueObjectExtension`: a PHPUnit Extension that registers `ValueComparator` once at suite bootstrap. Consumers wire it in `phpunit.xml` with a single `<bootstrap>` line.
- Templated generics across the package: `AbstractValue<TValue>`, `ValueInterface<TValue>` (covariant), with concrete bindings on all subclasses. `->value` and `->getValue()` now type-narrow correctly for consumers.
- Conditional return types on `from()` and `PercentValue::fromFraction()` so literal non-null / non-zero inputs narrow off `null` at static-analysis time.
- Shaped return type on `toArray()`: `array{value, formatted}` on `AbstractValue`, extended to `array{value, formatted, precision}` on `FloatValue`.
- `suggest` entry in `composer.json` for `phpunit/phpunit` (required only when using the `Testing` namespace).

### Changed

- **BREAKING:** PHP requirement bumped from `^8.1` to `^8.3`.
- **BREAKING:** PHPUnit dev requirement bumped from `^9.0` to `^12.5`.
- **BREAKING:** `from()` now returns `?static` instead of `static|NullValue`. `Foo::from(null)` returns plain PHP `null`.
- **BREAKING:** Stricter `supports()` contracts across primitive types. Each type accepts the scalars PHP coerces losslessly and throws `UnsupportedValueType` for everything else:
  - `BooleanValue`: `bool | int`
  - `StringValue`: `string | int | float`
  - `IntegerValue`: `int` or whole-number finite floats
  - `FloatValue`: `int | float`
- **BREAKING:** `FloatValue` no longer rounds its internal value at construction. Precision is display-only via `toString()` / `->formatted`.
- **BREAKING:** `NumericTrait::eq()` is now precision-aware (epsilon comparison). Cross-type numerics with equal value compare as equal (e.g. `IntegerValue::from(962)->eq(FloatValue::from(962.0))` is `true`).
- **BREAKING:** `gt` / `gte` / `lt` / `lte` route through `eq()` for consistency with epsilon-aware equality. Within-epsilon values report `gte === true` and `gt === false` from both directions.
- **BREAKING:** `isWhole()` semantics corrected. Previously tested "stored as int"; now tests "value has no fractional part". `FloatValue::from(2.0)->isWhole()` returns `true` (was `false` in v1).
- **BREAKING:** `ValueInterface` now extends `\Stringable`. Custom implementations that do not extend `AbstractValue` must provide `__toString(): string`.
- **BREAKING:** `ValueInterface` moved from `SSIPG\ValueObjects\Values\ValueInterface` to `SSIPG\ValueObjects\Contracts\ValueInterface`. The new `Equatable` interface also lives in `Contracts`. Concrete value classes (`BooleanValue`, `FloatValue`, etc.) remain in `Values`. Consumers must update `use` statements.
- **BREAKING:** `PercentValue::fromFraction()` returns `?self` (was `self|NullValue`). Zero denominator returns `null`.
- **BREAKING:** `cast()` static helpers narrowed input type from `mixed` to `int|float|string|bool|null` (the scalars `(type)` casts handle safely).
- Fluent setters return `static` instead of `self`. Chains on subclasses now preserve the subclass type in static analysis.
- `PercentValue::fromFraction()` input union widened from `float|int|FloatValue|IntegerValue` to `float|int|ValueInterface<int|float>`. Existing call sites continue to work; future numeric value objects are accepted automatically.
- Static analysis baseline raised: PHPStan now passes at level 10 with one scoped `ignoreError` for a known generic-static-factory limitation on `AbstractValue::from()` (input type wider than `TValue`).

### Removed

- **BREAKING:** `SSIPG\ValueObjects\Values\NullValue` class. Use plain PHP `null` with the null-safe operator (`$vo?->getValue() ?? $default`).
- **BREAKING:** `SSIPG\ValueObjects\Values\InfinityValue` class. Use `FloatValue::from(INF)`.
- **BREAKING:** `isDivisibleBy()`, `isEven()`, `isOdd()` moved off `NumericTrait` and onto `IntegerValue` directly. The `%` operator throws `TypeError` on floats in PHP 8.x, so the trait was lying about its `int|float` support. `isDivisibleBy(self $n)` now requires both operands to be `IntegerValue`.
- `CurrencyValue::$precision` redeclaration removed (inherits from `FloatValue` with the same `int 2` default; no behavior change).

### Fixed

- `StringValue::from(['x'])` and similar non-coercible inputs no longer silently become `'Array'`; they throw `UnsupportedValueType`.
- `BooleanValue::setValue()` and `StringValue::setValue()` no longer bypass `supports()`.
- `CurrencyValue::toString()` handles `NumberFormatter::format()` returning `false`.
- `PercentValue::fromWhole()` return type narrowed from a false `self|NullValue` to just `self`.
- Cross-type `NumericTrait::eq()` no longer returns spurious equality from the `(float)` coercion of non-numeric operands (e.g. `FloatValue::from(1.0)->eq(BooleanValue::from(true))` correctly returns `false` instead of `true`).
- ULP-level float equality fragility that surfaces on PHP 8.4+ after the `round()` rewrite. `eq()` now uses epsilon comparison driven by the receiver's declared precision; `CurrencyValue::from(-80.81)` is equal to `CurrencyValue::from(-80.81000000000002)` (precision 2, epsilon 0.005).

[2.0.0]: https://github.com/ss-ipg/value-objects/releases/tag/v2.0.0
