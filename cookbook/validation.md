# Validation

The library enforces strict validation to ensure all volume calculations are based on valid dimensional data.

## Required Dimensions

All three dimensions (length, width, height) are required:

```php
use function Cline\Volume\volume;

// ✅ Valid - all three dimensions provided
volume([120, 80, 100]);
volume(length: 120, width: 80, height: 100);

// ❌ Invalid - missing dimensions
volume([120, 80]); // Only 2 dimensions - throws InvalidArgumentException
volume(length: 120, width: 80); // Missing height - throws InvalidArgumentException
```

## Positive Values Only

All dimensions must be positive numbers (> 0):

```php
// ✅ Valid - all positive
volume([120, 80, 100]);
volume([0.1, 0.1, 0.1]);

// ❌ Invalid - zero or negative values
volume([120, 0, 100]);    // Zero dimension - throws InvalidArgumentException
volume([120, -80, 100]);  // Negative dimension - throws InvalidArgumentException
volume([-1, -1, -1]);     // All negative - throws InvalidArgumentException
```

## Array Validation

### Indexed Arrays

Indexed arrays must contain exactly 3 values:

```php
// ✅ Valid
volume([120, 80, 100]);

// ❌ Invalid
volume([120]);           // Too few - throws InvalidArgumentException
volume([120, 80]);       // Too few - throws InvalidArgumentException
volume([120, 80, 100, 50]); // Too many - throws InvalidArgumentException
```

### Associative Arrays

Associative arrays must use the exact keys: 'length', 'width', 'height':

```php
// ✅ Valid
volume(['length' => 120, 'width' => 80, 'height' => 100]);
volume(['height' => 100, 'length' => 120, 'width' => 80]); // Order doesn't matter

// ❌ Invalid - wrong keys
volume(['l' => 120, 'w' => 80, 'h' => 100]); // Wrong key names
volume(['depth' => 100, 'width' => 80, 'height' => 100]); // 'depth' instead of 'length'
```

## Error Messages

The library provides clear error messages for validation failures:

```php
try {
    volume([120, 80]); // Missing height
} catch (InvalidArgumentException $e) {
    echo $e->getMessage(); // "3 dimensions required, 2 provided"
}

try {
    volume([120, 0, 100]); // Zero width
} catch (InvalidArgumentException $e) {
    echo $e->getMessage(); // "All dimensions must be positive numbers"
}

try {
    volume(['length' => 120, 'width' => 80]); // Missing height
} catch (InvalidArgumentException $e) {
    echo $e->getMessage(); // "Missing required dimension: height"
}
```
