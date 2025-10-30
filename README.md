[![GitHub Workflow Status][ico-tests]][link-tests]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

------

This library provides volume and dimensional calculation utilities for PHP 8.4+. It offers a flexible `volume()` function that returns a chainable `Volume` object with methods for unit conversions including cubic meters, floor meters, and loading meters.

## Requirements

> **Requires [PHP 8.4+](https://php.net/releases/)**

## Installation

```bash
composer require cline/volume
```

## Quick Start

```php
use function Cline\Volume\volume;

// Create from array (default: centimeters)
$vol = volume([120, 80, 100]);

// Get measurements
$vol->centimeters()->value(); // 960000 cm³
$vol->meters()->value();      // 0.96 m³
$vol->loadingMeters()->value(); // 0.4 ldm
$vol->floorMeters()->value(); // 0.96 m²
```

## Documentation

- **[Basic Usage](cookbook/basic-usage.md)** - Creating volumes, unit conversions, and working with value objects
- **[Loading Meters](cookbook/loading-meters.md)** - LDM calculations for cargo transportation
- **[Floor Meters](cookbook/floor-meters.md)** - Footprint area calculations
- **[Validation](cookbook/validation.md)** - Input validation rules and error handling
- **[Examples](cookbook/examples.md)** - Real-world usage patterns

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please use the [GitHub security reporting form][link-security] rather than the issue queue.

## Credits

- [Brian Faust][link-maintainer]
- [All Contributors][link-contributors]

## License

The MIT License. Please see [License File](LICENSE.md) for more information.

[ico-tests]: https://github.com/faustbrian/volume/actions/workflows/quality-assurance.yaml/badge.svg
[ico-version]: https://img.shields.io/packagist/v/cline/volume.svg
[ico-license]: https://img.shields.io/badge/License-MIT-green.svg
[ico-downloads]: https://img.shields.io/packagist/dt/cline/volume.svg

[link-tests]: https://github.com/faustbrian/volume/actions
[link-packagist]: https://packagist.org/packages/cline/volume
[link-downloads]: https://packagist.org/packages/cline/volume
[link-security]: https://github.com/faustbrian/volume/security
[link-maintainer]: https://github.com/faustbrian
[link-contributors]: ../../contributors
