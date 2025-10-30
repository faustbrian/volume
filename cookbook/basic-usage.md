# Basic Usage

The `volume()` function creates a `Volume` instance that provides chainable unit conversion methods.

## Creating Volume Instances

```php
use function Cline\Volume\volume;

// From array (default: centimeters)
$vol = volume([120, 80, 100]);

// With explicit unit
$vol = volume([1.2, 0.8, 1.0], 'meters');
$vol = volume([120, 80, 100], 'cm');

// Associative array (order doesn't matter)
$vol = volume(['height' => 100, 'length' => 120, 'width' => 80]);

// Named parameters
$vol = volume(length: 120, width: 80, height: 100);
$vol = volume(length: 1.2, width: 0.8, height: 1.0, unit: 'meters');
```

## Unit Conversions

```php
$vol = volume([120, 80, 100]);

// Cubic measurements
$vol->centimeters(); // CubicCentimeter: 960000 cm³
$vol->meters();      // CubicMeter: 0.96 m³
$vol->decimeters();  // CubicDecimeter: 960 dm³

// Shipping/logistics calculations
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

// Create from array
$vol = Volume::fromArray([120, 80, 100], 'centimeters');

// Get dimensions in different units
$vol->getLength('meters');      // 1.2
$vol->getWidth('centimeters');  // 80.0
$vol->getHeight('cm');          // 100.0
```
