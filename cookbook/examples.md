# Examples

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

    // Use whichever is higher
    return max($volumeRate, $ldmRate);
}

$cost = calculateShippingCost([120, 80, 100]);
echo "Shipping cost: €" . number_format($cost, 2); // €24.48 or €6.00
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
            'trucks_needed' => $trucksNeeded,
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
// Array
// (
//     [total_ldm] => 6.6
//     [trucks_needed] => 1
//     [utilization] => 48.5%
//     [ldm_per_truck] => 6.6
// )
```

## Multi-Unit Conversion Dashboard

Display volume in multiple units for comparison:

```php
class VolumeConverter
{
    private Volume $volume;

    public function __construct(array $dimensions)
    {
        $this->volume = volume($dimensions);
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

$converter = new VolumeConverter([120, 80, 100]);
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
$shipment = [
    ['name' => 'Pallet A', 'dims' => [120, 80, 145], 'unit' => 'cm'],
    ['name' => 'Crate B', 'dims' => [1.5, 1.2, 2.0], 'unit' => 'meters'],
    ['name' => 'Box C', 'dims' => [50, 40, 30], 'unit' => 'cm'],
];

$totalCubicMeters = 0;
$totalLoadingMeters = 0;

foreach ($shipment as $item) {
    $vol = volume($item['dims'], $item['unit']);
    $cubicMeters = $vol->meters()->value();
    $loadingMeters = $vol->loadingMeters()->value();

    $totalCubicMeters += $cubicMeters;
    $totalLoadingMeters += $loadingMeters;

    echo "{$item['name']}: {$cubicMeters} m³, {$loadingMeters} LDM\n";
}

echo "\nTotals:\n";
echo "Cubic Meters: {$totalCubicMeters} m³\n";
echo "Loading Meters: {$totalLoadingMeters} LDM\n";
```
