## Table of Contents

1. Overview (`docs/README.md`)
2. Basic Usage (`docs/basic-usage.md`)
3. Examples (`docs/examples.md`)
4. Floor Meters (`docs/floor-meters.md`)
5. Loading Meters (`docs/loading-meters.md`)
6. Validation (`docs/validation.md`)
Volume and dimensional calculation utilities for PHP 8.4+ with chainable unit conversions and logistics calculations.

## Requirements

> **Requires [PHP 8.4+](https://php.net/releases/)**

## Installation

```bash
composer require cline/volume
```

## Quick Example

```php
use function Cline\Volume\volume;

// Create from dimensions (default: centimeters)
$vol = volume([120, 80, 100]);

// Chainable unit conversions
$vol->centimeters()->value(); // 960000 cm³
$vol->meters()->value();      // 0.96 m³
$vol->decimeters()->value();  // 960 dm³

// Logistics calculations
$vol->floorMeters()->value();   // 0.96 m² (floor area)
$vol->loadingMeters()->value(); // 0.4 LDM (truck space)
```

## Input Formats

The `volume()` function accepts multiple input formats:

```php
// Indexed array [length, width, height]
volume([120, 80, 100]);

// Associative array (order doesn't matter)
volume(['height' => 100, 'length' => 120, 'width' => 80]);

// Named parameters
volume(length: 120, width: 80, height: 100);

// With explicit unit
volume([1.2, 0.8, 1.0], Unit::Meters);
volume(length: 1.2, width: 0.8, height: 1.0, unit: Unit::Meters);
```

## Available Units

```php
use Cline\Volume\Enums\Unit;

Unit::Centimeters; // Default
Unit::Decimeters;
Unit::Meters;
```

## Value Objects

All conversions return immutable value objects:

```php
$cm3 = $vol->centimeters();

$cm3->value();     // 960000.0 (raw value)
$cm3->format(2);   // "960,000.00" (formatted)
(string) $cm3;     // "960000" (string cast)

// Access original dimensions
$cm3->length;  // 120.0
$cm3->width;   // 80.0
$cm3->height;  // 100.0
```

## Features

- **[Basic Usage](./basic-usage.md)** - Creating volumes and unit conversions
- **[Loading Meters](./loading-meters.md)** - Freight logistics calculations
- **[Floor Meters](./floor-meters.md)** - Floor area calculations
- **[Examples](./examples.md)** - Real-world usage patterns
- **[Validation](./validation.md)** - Input validation rules

The `volume()` function creates a `Volume` instance that provides chainable unit conversion methods.

## Creating Volume Instances

```php
use function Cline\Volume\volume;

// From array (default: centimeters)
$vol = volume([120, 80, 100]);

// With explicit unit
$vol = volume([1.2, 0.8, 1.0], Unit::Meters);
$vol = volume([120, 80, 100], Unit::Centimeters);

// Associative array (order doesn't matter)
$vol = volume(['height' => 100, 'length' => 120, 'width' => 80]);

// Named parameters
volume(length: 120, width: 80, height: 100);
volume(length: 1.2, width: 0.8, height: 1.0, unit: Unit::Meters);
```

## Unit Conversions

```php
$vol = volume([120, 80, 100]);

// Cubic measurements
$vol->centimeters(); // CubicCentimeter: 960000 cm³
$vol->meters();      // CubicMeter: 0.96 m³
$vol->decimeters();  // CubicDecimeter: 960 dm³

// Logistics calculations
$vol->loadingMeters(); // LoadingMeter: 0.4 ldm
$vol->floorMeters();   // FloorMeter: 0.96 m²
```

## Working with Value Objects

All conversion methods return value objects with useful methods:

```php
$cm3 = $vol->centimeters();

// Get raw value
$cm3->value(); // 960000.0

// Access original dimensions
$cm3->length;  // 120.0
$cm3->width;   // 80.0
$cm3->height;  // 100.0

// Format with decimals
$cm3->format(2); // "960,000.00"

// String conversion
(string) $cm3; // "960000"
```

## Direct Volume Class Usage

```php
use Cline\Volume\ValueObjects\Volume;

// Create from centimeters
$vol = Volume::fromCentimeters(120, 80, 100);

// Create from meters
$vol = Volume::fromMeters(1.2, 0.8, 1.0);

// Create from decimeters
$vol = Volume::fromDecimeters(12, 8, 10);

// Create from array
$vol = Volume::fromArray([120, 80, 100], Unit::Centimeters);

// Get dimensions in different units
$vol->getLength(Unit::Meters);      // 1.2
$vol->getWidth(Unit::Centimeters);  // 80.0
$vol->getHeight(Unit::Decimeters);  // 10.0
```

## Immutability

All value objects are immutable. Each conversion returns a new instance:

```php
$vol = volume([120, 80, 100]);

$meters = $vol->meters();        // New CubicMeter instance
$centimeters = $vol->centimeters(); // New CubicCentimeter instance

// Original volume is unchanged
```

Practical examples demonstrating real-world usage of the volume library.

## E-Commerce Shipping Calculator

Calculate shipping costs based on package volume and dimensions:

```php
use function Cline\Volume\volume;

function calculateShippingCost(array $dimensions): float
{
    $vol = volume($dimensions); // dimensions in centimeters

    // Cubic meter pricing
    $cubicMeters = $vol->meters()->value();
    $volumeRate = $cubicMeters * 25.50; // €25.50 per m³

    // Loading meter pricing for logistics
    $loadingMeters = $vol->loadingMeters()->value();
    $ldmRate = $loadingMeters * 15.00; // €15.00 per LDM

    // Use whichever is higher (dimensional weight pricing)
    return max($volumeRate, $ldmRate);
}

$cost = calculateShippingCost([120, 80, 100]);
echo "Shipping cost: €" . number_format($cost, 2);
```

## Warehouse Inventory Management

Track storage space utilization:

```php
class WarehouseInventory
{
    private array $items = [];

    public function addItem(string $sku, array $dimensions, int $quantity): void
    {
        $vol = volume($dimensions);

        $this->items[$sku] = [
            'dimensions' => $dimensions,
            'quantity' => $quantity,
            'floor_space' => $vol->floorMeters()->value() * $quantity,
            'cubic_meters' => $vol->meters()->value() * $quantity,
        ];
    }

    public function getTotalFloorSpace(): float
    {
        return array_sum(array_column($this->items, 'floor_space'));
    }

    public function getTotalVolume(): float
    {
        return array_sum(array_column($this->items, 'cubic_meters'));
    }
}

$inventory = new WarehouseInventory();
$inventory->addItem('PALLET-001', [120, 80, 100], 50);
$inventory->addItem('BOX-LARGE', [60, 40, 40], 200);

echo "Total floor space: {$inventory->getTotalFloorSpace()} m²\n";
echo "Total volume: {$inventory->getTotalVolume()} m³\n";
```

## Pallet Loading Optimizer

Calculate optimal truck loading with stacking:

```php
class PalletLoadingOptimizer
{
    public function calculateTruckCapacity(
        array $palletDimensions,
        int $quantity,
        float $stackingFactor,
        float $truckLength = 13.6 // Standard truck length in meters
    ): array {
        $vol = volume($palletDimensions);

        $singlePalletLdm = $vol->loadingMeters()->value();
        $totalLdm = $vol->loadingMeters(
            quantity: $quantity,
            stackingFactor: $stackingFactor
        )->value();

        $trucksNeeded = ceil($totalLdm / $truckLength);
        $utilizationPercent = ($totalLdm / ($trucksNeeded * $truckLength)) * 100;

        return [
            'total_ldm' => $totalLdm,
            'trucks_needed' => (int) $trucksNeeded,
            'utilization' => round($utilizationPercent, 1),
            'ldm_per_truck' => round($totalLdm / $trucksNeeded, 2),
        ];
    }
}

$optimizer = new PalletLoadingOptimizer();
$result = $optimizer->calculateTruckCapacity(
    palletDimensions: [120, 80, 145], // Euro pallet
    quantity: 33,
    stackingFactor: 2.0 // Can stack 2 high
);

print_r($result);
// [
//     'total_ldm' => 6.6,
//     'trucks_needed' => 1,
//     'utilization' => 48.5,
//     'ldm_per_truck' => 6.6,
// ]
```

## Multi-Unit Conversion Dashboard

Display volume in multiple units for comparison:

```php
class VolumeConverter
{
    public function __construct(
        private readonly Volume $volume
    ) {}

    public static function fromDimensions(array $dimensions): self
    {
        return new self(volume($dimensions));
    }

    public function getAllMeasurements(): array
    {
        return [
            'cubic_centimeters' => $this->volume->centimeters()->format(0),
            'cubic_decimeters' => $this->volume->decimeters()->format(2),
            'cubic_meters' => $this->volume->meters()->format(4),
            'floor_meters' => $this->volume->floorMeters()->format(2),
            'loading_meters' => $this->volume->loadingMeters()->format(2),
        ];
    }

    public function displayTable(): void
    {
        $measurements = $this->getAllMeasurements();

        echo "Volume Measurements:\n";
        echo str_repeat('-', 40) . "\n";
        foreach ($measurements as $unit => $value) {
            $label = str_pad(ucwords(str_replace('_', ' ', $unit)), 20);
            echo "{$label}: {$value}\n";
        }
    }
}

$converter = VolumeConverter::fromDimensions([120, 80, 100]);
$converter->displayTable();
// Volume Measurements:
// ----------------------------------------
// Cubic Centimeters   : 960,000
// Cubic Decimeters    : 960.00
// Cubic Meters        : 0.9600
// Floor Meters        : 0.96
// Loading Meters      : 0.40
```

## Batch Processing with Different Units

Process multiple items with varying unit inputs:

```php
use Cline\Volume\Enums\Unit;

$shipment = [
    ['name' => 'Pallet A', 'dims' => [120, 80, 145], 'unit' => Unit::Centimeters],
    ['name' => 'Crate B', 'dims' => [1.5, 1.2, 2.0], 'unit' => Unit::Meters],
    ['name' => 'Box C', 'dims' => [50, 40, 30], 'unit' => Unit::Centimeters],
];

$totalCubicMeters = 0;
$totalLoadingMeters = 0;

foreach ($shipment as $item) {
    $vol = volume($item['dims'], $item['unit']);
    $cubicMeters = $vol->meters()->value();
    $loadingMeters = $vol->loadingMeters()->value();

    $totalCubicMeters += $cubicMeters;
    $totalLoadingMeters += $loadingMeters;

    printf(
        "%s: %.3f m³, %.2f LDM\n",
        $item['name'],
        $cubicMeters,
        $loadingMeters
    );
}

echo "\nTotals:\n";
echo "Cubic Meters: {$totalCubicMeters} m³\n";
echo "Loading Meters: {$totalLoadingMeters} LDM\n";
```

## Container Packing Estimation

Estimate how many items fit in a shipping container:

```php
class ContainerPacker
{
    // Standard 40ft container internal dimensions
    private const CONTAINER_40FT = [
        'length' => 1203, // cm
        'width' => 235,   // cm
        'height' => 269,  // cm
    ];

    public function estimateFit(array $itemDimensions, float $utilizationFactor = 0.85): int
    {
        $container = volume(self::CONTAINER_40FT);
        $item = volume($itemDimensions);

        $containerVolume = $container->meters()->value();
        $itemVolume = $item->meters()->value();

        // Apply utilization factor for realistic packing
        $usableVolume = $containerVolume * $utilizationFactor;

        return (int) floor($usableVolume / $itemVolume);
    }

    public function estimateByLoadingMeters(array $itemDimensions): int
    {
        $containerLdm = self::CONTAINER_40FT['length'] / 100 / 2.4;
        $itemLdm = volume($itemDimensions)->loadingMeters()->value();

        return (int) floor($containerLdm / $itemLdm);
    }
}

$packer = new ContainerPacker();
$boxDimensions = [60, 40, 40]; // Standard shipping box

echo "By volume: " . $packer->estimateFit($boxDimensions) . " boxes\n";
echo "By LDM: " . $packer->estimateByLoadingMeters($boxDimensions) . " boxes\n";
```

Floor meters calculate the footprint area (length × width) of cargo in square meters, providing the ground space required for storage or stacking.

## Basic Calculation

```php
use function Cline\Volume\volume;

$vol = volume([200, 150, 100]); // 200cm × 150cm × 100cm
$fm = $vol->floorMeters();

$fm->value();    // 3.0 m²
$fm->format(2);  // "3.00"
```

## Accessing Dimensions

```php
$fm = $vol->floorMeters();

// Original dimensions in meters
$fm->length;  // 2.0
$fm->width;   // 1.5

// Get the calculated area
$fm->value(); // 3.0 (square meters)

// Format for display
$fm->format(1); // "3.0"
(string) $fm;   // "3"
```

## Direct Instantiation

```php
use Cline\Volume\ValueObjects\FloorMeter;

// From meters
$fm = FloorMeter::fromMeters(2.0, 1.5);
$fm->value(); // 3.0 m²

// From centimeters
$fm = FloorMeter::fromCentimeters(200, 150);
$fm->value(); // 3.0 m²
```

## Practical Examples

### Warehouse Space Planning

```php
// Calculate floor space for multiple pallets
$pallet = volume([120, 80, 100]); // Standard Euro pallet
$singlePalletArea = $pallet->floorMeters()->value(); // 0.96 m²

// 50 pallets
$totalArea = $singlePalletArea * 50; // 48 m²
```

### Storage Optimization

```php
// Different package sizes
$small = volume([60, 40, 30])->floorMeters()->value();   // 0.24 m²
$medium = volume([100, 60, 50])->floorMeters()->value(); // 0.6 m²
$large = volume([150, 100, 80])->floorMeters()->value(); // 1.5 m²

// Calculate total warehouse footprint
$totalFootprint = ($small * 100) + ($medium * 50) + ($large * 25);
// = 24 + 30 + 37.5 = 91.5 m²
```

### Comparing Storage Efficiency

```php
$packages = [
    ['name' => 'Box A', 'dims' => [40, 30, 20]],
    ['name' => 'Box B', 'dims' => [50, 40, 30]],
    ['name' => 'Box C', 'dims' => [60, 45, 35]],
];

foreach ($packages as $pkg) {
    $vol = volume($pkg['dims']);
    $floor = $vol->floorMeters()->value();
    $cubic = $vol->meters()->value();
    $efficiency = $cubic / $floor; // Height utilization

    echo "{$pkg['name']}: {$floor} m² floor, {$cubic} m³ volume\n";
}
```

## API Reference

### FloorMeter::fromMeters()

```php
public static function fromMeters(float $length, float $width): FloorMeter
```

### FloorMeter::fromCentimeters()

```php
public static function fromCentimeters(float $length, float $width): FloorMeter
```

### Instance Methods

| Method | Return | Description |
|--------|--------|-------------|
| `value()` | `float` | Raw floor area in m² |
| `format(int $decimals = 2)` | `string` | Formatted with number_format |
| `__toString()` | `string` | String representation |

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `$length` | `float` | Length dimension in meters |
| `$width` | `float` | Width dimension in meters |
| `$value` | `float` | Calculated area in m² |

Loading meters (LDM) is a standard calculation in cargo transportation that determines how much space shipments occupy lengthwise in a truck.

## Basic Formula

**Formula:** `(Length × Width) / 2.4 = LDM`

The divisor 2.4 represents the standard internal width of a truck in meters.

```php
use function Cline\Volume\volume;

// Single Euro pallet (1.2m × 0.8m)
$vol = volume([120, 80, 100]); // dimensions in centimeters
$ldm = $vol->loadingMeters();
$ldm->value(); // 0.4 LDM (1.2 × 0.8 ÷ 2.4 = 0.4)

// Formatted output
$ldm->format(2); // "0.40"
```

## Quantity Parameter

When shipping multiple identical items:

**Formula:** `Quantity × (Length × Width) / 2.4 = Total LDM`

```php
// 5 Euro pallets via Volume API
$vol = volume([120, 80, 100]);
$ldm = $vol->loadingMeters(quantity: 5);
$ldm->value(); // 2.0 LDM (5 × 0.4 = 2.0)

// Direct instantiation
$ldm = LoadingMeter::fromCentimeters(120, 80, quantity: 5);
$ldm->value(); // 2.0 LDM
```

## Stacking Factor

For stackable goods, divide by the stacking factor:

**Formula:** `(Length × Width) / 2.4 / Stacking Factor = LDM`

```php
// Euro pallet stackable 2 high
$ldm = LoadingMeter::fromCentimeters(120, 80, stackingFactor: 2.0);
$ldm->value(); // 0.2 LDM (0.4 ÷ 2 = 0.2)

// Via Volume API
$vol = volume([120, 80, 100]);
$ldm = $vol->loadingMeters(stackingFactor: 2.0);
$ldm->value(); // 0.2 LDM
```

## Combined: Quantity + Stacking

**Formula:** `Quantity × (Length × Width) / 2.4 / Stacking Factor = Total LDM`

```php
// 10 Euro pallets, stackable 2 high
$ldm = LoadingMeter::fromCentimeters(120, 80, quantity: 10, stackingFactor: 2.0);
$ldm->value(); // 2.0 LDM ((10 × 0.4) ÷ 2 = 2.0)
```

## Custom Truck Width

For non-standard trucks, specify the actual truck width:

```php
// Using a 3-meter wide truck
$ldm = LoadingMeter::fromCentimeters(120, 80, truckWidth: 3.0);
$ldm->value(); // 0.32 LDM ((1.2 × 0.8) ÷ 3.0 = 0.32)
```

## Complete Example

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

## API Reference

### LoadingMeter::fromCentimeters()

```php
public static function fromCentimeters(
    float $lengthInCm,
    float $widthInCm,
    int $quantity = 1,
    float $stackingFactor = 1.0,
    float $truckWidth = 2.4,
): LoadingMeter
```

### LoadingMeter::fromMeters()

```php
public static function fromMeters(
    float $length,
    float $width,
    int $quantity = 1,
    float $stackingFactor = 1.0,
    float $truckWidth = 2.4,
): LoadingMeter
```

### Instance Methods

| Method | Return | Description |
|--------|--------|-------------|
| `value()` | `float` | Raw loading meter value |
| `format(int $decimals = 2)` | `string` | Formatted with number_format |
| `__toString()` | `string` | String representation |

The library enforces strict validation to ensure all volume calculations are based on valid dimensional data.

## Required Dimensions

All three dimensions (length, width, height) are required for volume calculations:

```php
use function Cline\Volume\volume;

// Valid - all three dimensions provided
volume([120, 80, 100]);
volume(length: 120, width: 80, height: 100);

// Invalid - throws InvalidDimensionsException
volume([120, 80]);           // Only 2 dimensions
volume(length: 120, width: 80); // Missing height
```

## Positive Values Only

All dimensions must be positive numbers (> 0):

```php
// Valid - all positive
volume([120, 80, 100]);
volume([0.1, 0.1, 0.1]);

// Invalid - throws InvalidDimensionsException
volume([120, 0, 100]);    // Zero dimension
volume([120, -80, 100]);  // Negative dimension
volume([-1, -1, -1]);     // All negative
```

## Array Validation

### Indexed Arrays

Indexed arrays must contain exactly 3 values:

```php
// Valid
volume([120, 80, 100]);

// Invalid - throws InvalidDimensionsException
volume([120]);              // Too few
volume([120, 80]);          // Too few
volume([120, 80, 100, 50]); // Too many
```

### Associative Arrays

Associative arrays must use the exact keys: `length`, `width`, `height`:

```php
// Valid
volume(['length' => 120, 'width' => 80, 'height' => 100]);
volume(['height' => 100, 'length' => 120, 'width' => 80]); // Order doesn't matter

// Invalid - throws InvalidDimensionsException
volume(['l' => 120, 'w' => 80, 'h' => 100]);          // Wrong keys
volume(['depth' => 100, 'width' => 80, 'height' => 100]); // 'depth' not 'length'
```

## Loading Meter Validation

Loading meter calculations have additional parameter validation:

```php
use Cline\Volume\ValueObjects\LoadingMeter;

// Valid
LoadingMeter::fromCentimeters(120, 80, quantity: 1, stackingFactor: 1.0, truckWidth: 2.4);

// Invalid - throws InvalidLoadingMeterParameterException
LoadingMeter::fromCentimeters(120, 80, quantity: 0);     // quantity < 1
LoadingMeter::fromCentimeters(120, 80, quantity: -1);    // quantity < 1
LoadingMeter::fromCentimeters(120, 80, stackingFactor: 0);   // stackingFactor <= 0
LoadingMeter::fromCentimeters(120, 80, stackingFactor: -1);  // stackingFactor <= 0
LoadingMeter::fromCentimeters(120, 80, truckWidth: 0);   // truckWidth <= 0
```

## Floor Meter Validation

Floor meters require positive length and width:

```php
use Cline\Volume\ValueObjects\FloorMeter;

// Valid
FloorMeter::fromMeters(1.2, 0.8);
FloorMeter::fromCentimeters(120, 80);

// Invalid - throws InvalidFloorDimensionsException
FloorMeter::fromMeters(0, 0.8);    // Zero length
FloorMeter::fromMeters(1.2, -0.8); // Negative width
```

## Exception Types

| Exception | Thrown When |
|-----------|-------------|
| `InvalidDimensionsException` | Volume dimensions invalid (count, keys, values) |
| `InvalidFloorDimensionsException` | Floor dimensions non-positive |
| `InvalidLoadingMeterParameterException` | LDM parameters invalid |

## Error Messages

The library provides clear error messages for validation failures:

```php
try {
    volume([120, 80]); // Missing height
} catch (InvalidDimensionsException $e) {
    echo $e->getMessage();
    // "3 dimensions required, 2 provided"
}

try {
    volume([120, 0, 100]); // Zero width
} catch (InvalidDimensionsException $e) {
    echo $e->getMessage();
    // "All dimensions must be positive numbers"
}

try {
    volume(['length' => 120, 'width' => 80]); // Missing height
} catch (InvalidDimensionsException $e) {
    echo $e->getMessage();
    // "Missing required dimension: height"
}

try {
    LoadingMeter::fromCentimeters(120, 80, quantity: 0);
} catch (InvalidLoadingMeterParameterException $e) {
    echo $e->getMessage();
    // "Quantity must be at least 1"
}
```

## Safe Handling Pattern

```php
use Cline\Volume\Exceptions\InvalidDimensionsException;

function safeVolumeCalculation(array $dimensions): ?float
{
    try {
        return volume($dimensions)->meters()->value();
    } catch (InvalidDimensionsException $e) {
        log_error("Invalid dimensions: " . $e->getMessage());
        return null;
    }
}

// Usage
$result = safeVolumeCalculation($userInput);
if ($result === null) {
    echo "Please provide valid dimensions (length, width, height > 0)";
}
```
