# Loading Meters (LDM)

Loading meters (LDM) is a standard calculation in cargo transportation that determines how much space shipments occupy lengthwise in a truck. The basic formula divides the cargo's floor area by the standard truck width of 2.4 meters.

## Basic Formula

**Formula:** `(Length × Width) / 2.4 = LDM`

```php
// Example: Single Euro pallet (1.2m × 0.8m)
$vol = volume([120, 80, 100]); // dimensions in centimeters
$ldm = $vol->loadingMeters();
$ldm->value(); // 0.4 LDM (1.2 × 0.8 ÷ 2.4 = 0.4)

// Formatted output
$ldm->format(2); // "0.40"
```

## Quantity Parameter

When shipping multiple identical items, multiply the area by the quantity:

**Formula:** `Quantity × (Length × Width) / 2.4 = Total LDM`

```php
// 5 Euro pallets
$ldm = LoadingMeter::fromCentimeters(120, 80, quantity: 5);
$ldm->value(); // 2.0 LDM (5 × 0.4 = 2.0)

// Via Volume chainable API
$vol = volume([120, 80, 100]);
$ldm = $vol->loadingMeters(quantity: 5);
$ldm->value(); // 2.0 LDM
```

## Stacking Factor

For stackable goods, divide by the stacking factor to reduce the loading meter requirement:

**Formula:** `(Length × Width) / 2.4 / Stacking Factor = LDM per unit`

```php
// Euro pallet stackable 2 high
$ldm = LoadingMeter::fromCentimeters(120, 80, stackingFactor: 2.0);
$ldm->value(); // 0.2 LDM (0.4 ÷ 2 = 0.2)
```

## Combined: Quantity + Stacking

**Formula:** `Quantity × (Length × Width) / 2.4 / Stacking Factor = Total LDM`

```php
// 10 Euro pallets, stackable 2 high
$ldm = LoadingMeter::fromCentimeters(120, 80, quantity: 10, stackingFactor: 2.0);
$ldm->value(); // 2.0 LDM ((10 × 0.4) ÷ 2 = 2.0)
```

## Custom Truck Width

For non-standard trucks, specify the actual truck width instead of the default 2.4m:

```php
// Using a 3-meter wide truck
$ldm = LoadingMeter::fromCentimeters(120, 80, truckWidth: 3.0);
$ldm->value(); // 0.32 LDM ((1.2 × 0.8) ÷ 3.0 = 0.32)
```

## Complete Example with All Parameters

```php
use Cline\Volume\ValueObjects\LoadingMeter;

$ldm = LoadingMeter::fromCentimeters(
    lengthInCm: 120,
    widthInCm: 80,
    quantity: 6,           // 6 pallets
    stackingFactor: 3.0,   // stackable 3 high
    truckWidth: 2.5,       // 2.5m wide truck
);

// (6 × (1.2 × 0.8)) / 2.5 / 3.0 = 0.768 LDM
$ldm->value(); // 0.768

// Access parameters used in calculation
$ldm->quantity;        // 6
$ldm->stackingFactor;  // 3.0
$ldm->truckWidth;      // 2.5
$ldm->length;          // 120.0 (cm)
$ldm->width;           // 80.0 (cm)
```
