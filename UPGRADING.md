# Upgrading

Migration guides for upgrading between major versions of `ss-ipg/value-objects`. Most recent first.

For a complete list of changes (new features, bug fixes, internals), see [`CHANGELOG.md`](CHANGELOG.md). This file covers only what consumers need to do when upgrading.

## Upgrading from v1 to v2

v2 is a major release focused on type safety, contract strictness, and developer ergonomics. Most migrations are mechanical and PHPStan catches what slips through.

### TL;DR

- **`NullValue` is removed.** `Foo::from(null)` now returns `null`. Update consumers from `instanceof NullValue` to `=== null` (or use `?->`).
- **Stricter input validation** on `Boolean/String/Integer/Float`. Incompatible inputs throw `UnsupportedValueType` instead of silently coercing. `FloatValue` and `IntegerValue` do accept numeric strings (`"100.50"`, `"42"`); `cast()` static helpers narrow their input type to `int|float|string|bool|null`.
- **`FloatValue` no longer rounds at construction.** Precision is display-only.
- **`InfinityValue` is removed.** Use `FloatValue::from(INF)`.
- **`NumericTrait::eq()` is precision-aware** and `gt`/`gte`/`lt`/`lte` route through it. ULP-noisy floats within epsilon now compare equal; cross-type numeric equality (e.g. `IntegerValue(962) eq FloatValue(962.0)`) is `true`.
- **`isWhole()` semantics changed**: `FloatValue::from(2.0)->isWhole()` now returns `true` (was `false`).
- **`ValueInterface` moved** to the `Contracts` namespace; the new `Equatable` interface lives there too. Update `use` statements.

### Requirements

|               | v1     | v2      |
|---------------|--------|---------|
| PHP           | `^8.1` | `^8.3`  |
| PHPUnit (dev) | `^9.0` | `^12.5` |

Bump your `composer.json` accordingly.

### Breaking changes

#### 1. `NullValue` removed

`from()` no longer returns a `NullValue` sentinel for `null` inputs; it returns plain PHP `null`.

```php
// v1
$value = StringValue::from(null);

if ($value instanceof NullValue) {
    echo 'no value';
}

echo $value->getValue(); // null
echo $value->formatted;  // ''

// v2
$value = StringValue::from(null); // returns null directly

if ($value === null) {
    echo 'no value';
}

echo $value?->getValue();      // null
echo $value?->formatted ?? ''; // ''
```

**Migration pattern:** any DTO field that held a possibly-null VO becomes a true nullable:

```php
// v1
public StringValue $name; // could be a NullValue at runtime

// v2
public ?StringValue $name;
```

Then update reads with `?->` and `??`:

```php
// v1
$name = $dto->name->getValue() ?? 'unknown';

// v2: identical syntax, now type-checked
$name = $dto->name?->getValue() ?? 'unknown';
```

#### 2. Stricter `supports()` contracts

In v1, `BooleanValue` and `StringValue` silently coerced any input via `(bool)` / `(string)`, including arrays and objects. `StringValue::from(['x'])` would produce `'Array'` (with a warning). v2 validates inputs through `supports()` and throws `UnsupportedValueType` for anything that doesn't coerce losslessly.

The accepted-input matrix (after `from()` short-circuits `null`):

| Type           | `supports()` accepts                                            | `supports()` rejects (throws `UnsupportedValueType`)              |
|----------------|-----------------------------------------------------------------|-------------------------------------------------------------------|
| `BooleanValue` | `bool`, `int`                                                   | strings, floats, arrays, objects                                  |
| `StringValue`  | `string`, `int`, `float`                                        | arrays, objects, bools                                            |
| `IntegerValue` | `int`, whole-number finite floats, whole-number numeric strings | fractional floats, INF, NaN, non-numeric strings, arrays, objects |
| `FloatValue`   | `int`, `float`, numeric strings                                 | non-numeric strings, arrays, objects                              |

`null` is handled by `from()` itself (returns `null`, see section 1) and never reaches `supports()`. Direct construction (`new IntegerValue(null)`) does reach `supports()` and throws.

`FloatValue` and `IntegerValue` accept numeric strings (`is_numeric($v) === true`) so values that arrive as strings (e.g. `"100.50"`, `"42"`) no longer need to be cast at the call site. `IntegerValue` continues to reject any input with a fractional part, including fractional numeric strings like `"5.5"`.

```php
// v1
$s = StringValue::from(['x']);    // 'Array' (with PHP warning)
$b = BooleanValue::from('hello'); // true (coerced)
$i = IntegerValue::from(1.5);     // 1 (truncated; actually rejected in v1 supports() returning false but you might not have hit this)

// v2
$s = StringValue::from(['x']);    // throws UnsupportedValueType
$b = BooleanValue::from('hello'); // throws UnsupportedValueType
$i = IntegerValue::from(1.5);     // throws UnsupportedValueType
```

**Migration pattern:** if you were relying on lossy coercion, route through the `cast()` static helper first:

```php
// v1
$s = StringValue::from($mixedInput);

// v2: explicit coercion at the call site
$s = StringValue::from(StringValue::cast($mixedInput));
```

For `IntegerValue` specifically: whole-number floats are accepted (`IntegerValue::from(3.0)` works), but fractional floats are not. Consumers who want lossy conversion write it explicitly:

```php
// v2: explicit rounding mode at the call site
$i = IntegerValue::from((int) round($value));
$i = IntegerValue::from((int) round($value, 0, PHP_ROUND_HALF_DOWN));
```

Related: `cast()` static helpers now accept `int|float|string|bool|null` rather than `mixed`. Pass arrays/objects through your own coercion first.

#### 3. `FloatValue` no longer rounds at construction

v1 rounded the internal `$value` to `ini_get('precision')` digits during `setValue`. v2 stores the full input precision and applies rounding only when rendering via `toString()` / `->formatted`.

```php
// v1
$float = FloatValue::from(1.8570000000000002);
$float->value;     // 1.857 (rounded by ini_get('precision'))
$float->formatted; // '1.86'

// v2
$float = FloatValue::from(1.8570000000000002);
$float->value;     // 1.8570000000000002 (full precision)
$float->formatted; // '1.86' (display rounding unchanged)
```

**Migration pattern:** if your tests asserted `assertSame(1.857, $float->value)`, update to the actual stored value or use `assertEqualsWithDelta`.

#### 4. `InfinityValue` removed

`InfinityValue` was a single-purpose class that ignored its constructor argument. `FloatValue` already accepts `INF` and round-trips it correctly.

```php
// v1
$inf = InfinityValue::from(anything());
$inf->value;     // INF
$inf->formatted; // 'INF'

// v2
$inf = FloatValue::from(INF);
$inf->value;                                   // INF
is_infinite($inf->value);                      // true
$inf->formatWith(fn ($f) => 'INF')->formatted; // 'INF' (custom formatter for the display)
```

#### 5. `NumericTrait::eq()` is now precision-aware

`eq()` compares numeric values within half a unit-in-the-last-place of the more-restrictive declared precision rather than via strict `===`. This absorbs the ULP noise that surfaces post-arithmetic on PHP 8.4+. It also makes cross-type numeric equality work as a user would expect.

> **Why this change:** PHP 8.4 [rewrote `round()` internally](https://www.php.net/manual/en/migration84.other-changes.php), removing a pre-rounding step that previously masked IEEE 754 noise. Pre-8.4 code that relied on that incidental laundering now sees raw ULP noise in any post-arithmetic float comparison; v2's `eq()` absorbs it intentionally.

```php
// v1
CurrencyValue::from(15.0)->eq(CurrencyValue::from(14.99999999999999)); // false
IntegerValue::from(962)->eq(FloatValue::from(962.0));                  // false (different types)

// v2
CurrencyValue::from(15.0)->eq(CurrencyValue::from(14.99999999999999)); // true (within 0.005 epsilon)
IntegerValue::from(962)->eq(FloatValue::from(962.0));                  // true (value-equal)
```

Each type's precision drives its epsilon: `FloatValue`/`CurrencyValue`/`PercentValue` use their declared `$precision`; `IntegerValue` and other non-`FloatValue` types use `PHP_FLOAT_DIG` (~5e-16, effectively strict for ints).

Cross-type comparisons against non-numeric VOs (e.g. `BooleanValue`, `StringValue`) always return `false`. `BooleanValue::eq()` and `StringValue::eq()` are themselves identity-based: same type, same value via `===`.

```php
// v2: cross-type with non-numeric returns false rather than spurious (float) coercion
FloatValue::from(1.0)->eq(BooleanValue::from(true));  // false
FloatValue::from(0.0)->eq(BooleanValue::from(false)); // false
StringValue::from('1')->eq(IntegerValue::from(1));    // false (different types)
```

#### 6. `gt` / `gte` / `lt` / `lte` route through `eq()`

Ordering operators are now epsilon-aware to stay consistent with `eq()`. Within-epsilon values report `gte === true` from both directions (in v1 the raw `>=` was asymmetric on noisy floats).

```php
$a = FloatValue::from(15.0);
$b = FloatValue::from(14.99999999999999);

// v1
$a->gte($b); // true
$b->gte($a); // false  (asymmetric)

// v2
$a->gte($b); // true
$b->gte($a); // true   (symmetric via eq())
```

#### 7. `isWhole()` semantically corrected

In v1, `isWhole()` tested whether the value was *stored* as `int`, so `FloatValue::from(2.0)->isWhole()` returned `false`. v2 tests whether the value has no fractional part.

```php
// v1
FloatValue::from(2.0)->isWhole(); // false (was buggy)
// v2
FloatValue::from(2.0)->isWhole(); // true
FloatValue::from(2.5)->isWhole(); // false
FloatValue::from(INF)->isWhole(); // false
```

#### 8. `isDivisibleBy` / `isEven` / `isOdd` moved to `IntegerValue`

These methods used the `%` operator, which throws `TypeError` on floats in PHP 8.x. They no longer exist on `FloatValue`, `CurrencyValue`, or `PercentValue`. `isDivisibleBy(self $n)` now requires both operands to be `IntegerValue`.

```php
// v1
FloatValue::from(2.0)->isEven(); // ran, returned true

// v2
FloatValue::from(2.0)->isEven(); // Error: Call to undefined method SSIPG\ValueObjects\Values\FloatValue::isEven()
IntegerValue::from(2)->isEven(); // still works
```

#### 9. `PercentValue::fromFraction` returns `?self`

The zero-denominator case now returns `null` instead of `NullValue`. Same null-handling pattern as `from()`.

```php
// v1
$p = PercentValue::fromFraction(1, 0); // NullValue
echo $p->formatted;                    // ''

// v2
$p = PercentValue::fromFraction(1, 0); // null
echo $p?->formatted ?? '';             // ''
```

A conditional return type means literal non-zero denominators narrow off `null` for PHPStan:

```php
PercentValue::fromFraction(1, 3)->setPrecision(2); // OK, PHPStan knows result is PercentValue
PercentValue::fromFraction($num, $denom);          // returns ?PercentValue
```

The input union also widened from `float|int|FloatValue|IntegerValue` to `float|int|ValueInterface<int|float>`. Existing call sites continue to work.

#### 10. `ValueInterface` now extends `\Stringable`

If you have custom implementations of `ValueInterface` that don't extend `AbstractValue`, they must now provide a `__toString(): string` method. Classes extending `AbstractValue` already satisfy this and need no change.

#### 11. `ValueInterface` namespace moved to `Contracts`

`ValueInterface` lives at `SSIPG\ValueObjects\Contracts\ValueInterface` in v2 (was `SSIPG\ValueObjects\Values\ValueInterface`). The new `Equatable` interface lives there too. Update any `use` statements:

```php
// v1
use SSIPG\ValueObjects\Values\ValueInterface;

// v2
use SSIPG\ValueObjects\Contracts\ValueInterface;
use SSIPG\ValueObjects\Contracts\Equatable;
```

The concrete value classes (`BooleanValue`, `IntegerValue`, `FloatValue`, `StringValue`, `CurrencyValue`, `PercentValue`, `AbstractValue`) all stay in `SSIPG\ValueObjects\Values\`.

### Migration checklist

1. Bump composer requirements: PHP `^8.3`, PHPUnit `^12.5`.
2. Update `use` statements for the `Contracts` namespace move (`ValueInterface`, `Equatable`).
3. Run PHPStan against your consumer code. Address each error:
   - `Cannot call method ... on ...|null` → add `?->` or null-check.
   - `instanceof NullValue` → replace with `=== null`.
   - `Cannot access property ... on null` → add `?->` or null-check.
4. Audit construction sites passing non-canonical inputs:
   - `StringValue::from($input)` where `$input` could be an array/object → wrap in `StringValue::cast()` or pre-validate.
   - `IntegerValue::from($input)` where `$input` could be a fractional float → use `(int) round($input)` explicitly.
5. Update tests that asserted on `FloatValue::from(x.xxxxx)->value` if you were relying on the `ini_get('precision')` rounding.
6. Replace any `InfinityValue::from(...)` with `FloatValue::from(INF)`.
7. Audit calls to `isDivisibleBy` / `isEven` / `isOdd` on `FloatValue`-derived types. They no longer exist on those types.
8. If you rely on `assertEquals` over value-object pairs, [register the `ValueObjectExtension`](#registering-the-testing-helpers).

### Registering the testing helpers

v2 ships a PHPUnit Comparator so `assertEquals` on value-object pairs routes through `Equatable::eq()` instead of PHPUnit's default property-by-property comparison. Without this, post-arithmetic float comparisons would fail on PHP 8.4+ due to ULP noise that the new precision-aware `eq()` is designed to absorb.

Register the Extension in your `phpunit.xml`:

```xml
<extensions>
    <bootstrap class="SSIPG\ValueObjects\Testing\ValueObjectExtension"/>
</extensions>
```

That's all. No `TestCase` changes; every `assertEquals` on a value-object pair will now consult `eq()`.

### Static-analysis improvements (no runtime change)

These are not breaking changes. Existing call sites continue to work; PHPStan/PHPStorm just sees more accurate types.

- **Fluent setters return `static`** instead of `self`. Chains on subclasses preserve the subclass type:
  ```php
  // v1: PHPStan saw the result as FloatValue, ->setLocale() not visible
  $currency = CurrencyValue::from(100)->setPrecision(0);

  // v2: PHPStan sees the result as CurrencyValue, ->setLocale() chains cleanly
  $currency = CurrencyValue::from(100)->setPrecision(0)->setLocale('en-GB');
  ```
- **Templated generics** on `AbstractValue<TValue>` and `ValueInterface<TValue>` (covariant). `->value` and `->getValue()` narrow to the concrete type instead of `mixed`.
- **Conditional return types** on `from()` and `PercentValue::fromFraction()`. Literal non-null / non-zero inputs narrow off `null` at static-analysis time.
- **Shaped `toArray()`** returns: `array{value: TValue, formatted: string}` on `AbstractValue`, with `precision: int` added on `FloatValue`.

### Known limitations / deferred to v3

The following items are intentionally not addressed in v2 and may land in v3:

- **Immutability.** Properties (`$value`, `$formatted`, `$formatter`, `$precision`, `$locale`) remain mutable. The fluent setters still mutate `$this` in place. v3 will convert these to `readonly` and replace setters with a `with*()` copy API.
- **Integer-storage for `CurrencyValue` / `PercentValue`.** Float-based storage means ULP fragility is inherent (v2's epsilon-aware `eq()` is a mitigation, not a cure). A future redesign storing minor units / basis points internally would eliminate this category of bug at the source.
- **Asymmetric operand typing on `NumericTrait::eq()` vs `gt`/`gte`/`lt`/`lte`.** `eq()` accepts `ValueInterface<mixed>` (any operand, returns `false` for non-numerics); the ordering operators still declare `ValueInterface<int|float>`. Ordering across non-numeric types is undefined behavior either way; the docblock asymmetry is cosmetic but worth noting.
