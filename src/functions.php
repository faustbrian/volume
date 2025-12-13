<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume;

use Cline\Volume\Enums\Unit;
use Cline\Volume\Exceptions\MissingRequiredDimensionsException;
use Cline\Volume\ValueObjects\Volume;

/**
 * Creates a Volume instance from dimensional measurements.
 *
 * Provides flexible input formats for creating volume calculations from physical
 * dimensions. Accepts either an array of dimensions or individual named parameters,
 * with configurable unit of measurement for the input values.
 *
 * Supports three input formats:
 * - Named parameters: volume(length: 10, width: 5, height: 3)
 * - Indexed array: volume([10, 5, 3]) - interpreted as [length, width, height]
 * - Associative array: volume(['length' => 10, 'width' => 5, 'height' => 3])
 *
 * By default, dimensions are assumed to be in centimeters. Use the $unit parameter
 * to specify alternative units (meters or decimeters).
 *
 * ```php
 * // Calculate loading meters from centimeter dimensions
 * volume([120, 80, 100])->loadingMeters(); // 0.4 loading meters
 *
 * // Create volume from meter dimensions
 * volume([1.2, 0.8, 1], Unit::Meters)->meters()->value(); // 0.96 cubic meters
 *
 * // Use named parameters for clarity
 * volume(length: 120, width: 80, height: 100)->centimeters()->value(); // 960000 cm³
 * ```
 *
 * @author Brian Faust <brian@cline.sh>
 * @param  null|array<int|string, float|int>  $dimensions Array of dimensions in indexed format [length, width, height]
 *                                                        or associative format with 'length', 'width', 'height' keys.
 *                                                        When provided, individual dimension parameters are ignored.
 * @param  Unit                               $unit       Unit of measurement for input dimensions. Determines how the provided
 *                                                        numerical values are interpreted. Defaults to Unit::Centimeters.
 * @param  null|float|int                     $length     Length dimension value. Required when $dimensions is null.
 *                                                        Must be a positive number in the unit specified by $unit.
 * @param  null|float|int                     $width      Width dimension value. Required when $dimensions is null.
 *                                                        Must be a positive number in the unit specified by $unit.
 * @param  null|float|int                     $height     Height dimension value. Required when $dimensions is null.
 *                                                        Must be a positive number in the unit specified by $unit.
 * @throws MissingRequiredDimensionsException When required dimension parameters (length, width, height) are missing
 * @return Volume                             immutable volume instance with chainable methods for unit conversion
 *                                            and derived calculations (loading meters, floor meters, cubic units)
 */
function volume(
    ?array $dimensions = null,
    Unit $unit = Unit::Centimeters,
    int|float|null $length = null,
    int|float|null $width = null,
    int|float|null $height = null,
): Volume {
    if ($dimensions !== null) {
        return Volume::fromArray($dimensions, $unit);
    }

    if ($length === null || $width === null || $height === null) {
        $missing = [];

        if ($length === null) {
            $missing[] = 'length';
        }

        if ($width === null) {
            $missing[] = 'width';
        }

        if ($height === null) {
            $missing[] = 'height';
        }

        throw MissingRequiredDimensionsException::fromMissing($missing);
    }

    return match ($unit) {
        Unit::Centimeters => Volume::fromCentimeters($length, $width, $height),
        Unit::Decimeters => Volume::fromDecimeters($length, $width, $height),
        Unit::Meters => Volume::fromMeters($length, $width, $height),
    };
}
