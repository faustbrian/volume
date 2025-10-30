# Floor Meters

Floor meters calculate the footprint area (length × width) of cargo, providing the ground space required.

## Basic Calculation

```php
use function Cline\Volume\volume;

$vol = volume([200, 150, 100]); // 200cm × 150cm × 100cm
$fm = $vol->floorMeters();
$fm->value(); // 3.0 m²
$fm->format(2);  // "3.00"
```

## Accessing Dimensions

```php
$fm = $vol->floorMeters();

// Original dimensions in centimeters
$fm->length;  // 200.0
$fm->width;   // 150.0

// Get the calculated area
$fm->value(); // 3.0 (square meters)

// Format for display
$fm->format(1); // "3.0"
(string) $fm;   // "3"
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
$small = volume([60, 40, 30])->floorMeters()->value();  // 0.24 m²
$medium = volume([100, 60, 50])->floorMeters()->value(); // 0.6 m²
$large = volume([150, 100, 80])->floorMeters()->value(); // 1.5 m²

// Calculate total warehouse footprint
$totalFootprint = ($small * 100) + ($medium * 50) + ($large * 25);
// = 24 + 30 + 37.5 = 91.5 m²
```
