<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\ValueObjects;

use Cline\Volume\Enums\Unit;
use Cline\Volume\Exceptions\InvalidDimensionsException;
use Cline\Volume\ValueObjects\CubicCentimeter;
use Cline\Volume\ValueObjects\CubicDecimeter;
use Cline\Volume\ValueObjects\CubicMeter;
use Cline\Volume\ValueObjects\FloorMeter;
use Cline\Volume\ValueObjects\LoadingMeter;

use function array_is_list;
use function array_key_exists;
use function array_keys;
use function count;
use function throw_if;

/**
 * Represents a volume measurement with unit-agnostic dimension storage.
 *
 * Immutable value object that stores dimensions internally in meters and provides
 * conversion methods to various volume units (cubic meters, cubic decimeters,
 * cubic centimeters) and derived measurements (floor meters, loading meters).
 * Supports flexible input from multiple units and array formats.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class Volume
{
    /**
     * Creates a new volume instance with dimensions stored in meters.
     *
     * @param float $lengthInMeters The length dimension in meters
     * @param float $widthInMeters  The width dimension in meters
     * @param float $heightInMeters The height dimension in meters
     */
    private function __construct(
        private float $lengthInMeters,
        private float $widthInMeters,
        private float $heightInMeters,
    ) {}

    /**
     * Creates a volume from dimensions specified in centimeters.
     *
     * @param float $length The length in centimeters
     * @param float $width  The width in centimeters
     * @param float $height The height in centimeters
     *
     * @throws InvalidDimensionsException When any dimension is zero or negative
     */
    public static function fromCentimeters(float $length, float $width, float $height): self
    {
        throw_if($length <= 0 || $width <= 0 || $height <= 0, InvalidDimensionsException::nonPositive());

        return new self($length / 100, $width / 100, $height / 100);
    }

    /**
     * Creates a volume from dimensions specified in meters.
     *
     * @param float $length The length in meters
     * @param float $width  The width in meters
     * @param float $height The height in meters
     *
     * @throws InvalidDimensionsException When any dimension is zero or negative
     */
    public static function fromMeters(float $length, float $width, float $height): self
    {
        throw_if($length <= 0 || $width <= 0 || $height <= 0, InvalidDimensionsException::nonPositive());

        return new self($length, $width, $height);
    }

    /**
     * Creates a volume from dimensions specified in decimeters.
     *
     * @param float $length The length in decimeters
     * @param float $width  The width in decimeters
     * @param float $height The height in decimeters
     *
     * @throws InvalidDimensionsException When any dimension is zero or negative
     */
    public static function fromDecimeters(float $length, float $width, float $height): self
    {
        throw_if($length <= 0 || $width <= 0 || $height <= 0, InvalidDimensionsException::nonPositive());

        return new self($length / 10, $width / 10, $height / 10);
    }

    /**
     * Creates a volume from an array of dimensions.
     *
     * Accepts either an indexed array [length, width, height] or an associative
     * array with 'length', 'width', and 'height' keys. The unit parameter specifies
     * which unit the input dimensions are measured in.
     *
     * @param array<int|string, float|int> $dimensions Array of three dimension values
     * @param Unit                         $unit       The unit of measurement for the input dimensions (default: Centimeters)
     *
     * @throws InvalidDimensionsException When array count is not exactly 3, required keys are missing, or any dimension is zero or negative
     */
    public static function fromArray(array $dimensions, Unit $unit = Unit::Centimeters): self
    {
        throw_if(count($dimensions) !== 3, InvalidDimensionsException::arrayCountMismatch(count($dimensions)));

        if (array_is_list($dimensions)) {
            /** @var array{0: float|int, 1: float|int, 2: float|int} $dimensions */
            [$length, $width, $height] = $dimensions;
        } else {
            /** @var array<string, float|int> $dimensions */
            throw_if(!array_key_exists('length', $dimensions) || !array_key_exists('width', $dimensions) || !array_key_exists('height', $dimensions), InvalidDimensionsException::missingKeys(array_keys($dimensions)));
            $length = $dimensions['length'];
            $width = $dimensions['width'];
            $height = $dimensions['height'];
        }

        return match ($unit) {
            Unit::Centimeters => self::fromCentimeters((float) $length, (float) $width, (float) $height),
            Unit::Decimeters => self::fromDecimeters((float) $length, (float) $width, (float) $height),
            Unit::Meters => self::fromMeters((float) $length, (float) $width, (float) $height),
        };
    }

    /**
     * Converts the volume to cubic centimeters.
     */
    public function centimeters(): CubicCentimeter
    {
        return CubicCentimeter::fromMeters(
            $this->lengthInMeters,
            $this->widthInMeters,
            $this->heightInMeters,
        );
    }

    /**
     * Converts the volume to cubic meters.
     */
    public function meters(): CubicMeter
    {
        return CubicMeter::fromMeters(
            $this->lengthInMeters,
            $this->widthInMeters,
            $this->heightInMeters,
        );
    }

    /**
     * Converts the volume to cubic decimeters.
     */
    public function decimeters(): CubicDecimeter
    {
        return CubicDecimeter::fromMeters(
            $this->lengthInMeters,
            $this->widthInMeters,
            $this->heightInMeters,
        );
    }

    /**
     * Calculates the floor area in square meters.
     *
     * Returns a two-dimensional floor area measurement using only length and width,
     * ignoring the height dimension.
     */
    public function floorMeters(): FloorMeter
    {
        return FloorMeter::fromMeters(
            $this->lengthInMeters,
            $this->widthInMeters,
        );
    }

    /**
     * Calculates the loading meters (LDM) for freight logistics.
     *
     * Computes how many linear meters of truck space the cargo will occupy based
     * on the floor area, quantity, stacking efficiency, and truck dimensions.
     *
     * @param int   $quantity       The number of items (default: 1)
     * @param float $stackingFactor The vertical stacking efficiency factor (default: 1.0 for no stacking)
     * @param float $truckWidth     The internal truck width in meters (default: 2.4m for standard trucks)
     */
    public function loadingMeters(
        int $quantity = 1,
        float $stackingFactor = 1.0,
        float $truckWidth = 2.4,
    ): LoadingMeter {
        return LoadingMeter::fromCentimeters(
            $this->lengthInMeters * 100,
            $this->widthInMeters * 100,
            $quantity,
            $stackingFactor,
            $truckWidth,
        );
    }

    /**
     * Returns the length dimension in the specified unit.
     *
     * @param Unit $unit The unit to convert the length to (default: Meters)
     */
    public function getLength(Unit $unit = Unit::Meters): float
    {
        return match ($unit) {
            Unit::Centimeters => $this->lengthInMeters * 100,
            Unit::Decimeters => $this->lengthInMeters * 10,
            Unit::Meters => $this->lengthInMeters,
        };
    }

    /**
     * Returns the width dimension in the specified unit.
     *
     * @param Unit $unit The unit to convert the width to (default: Meters)
     */
    public function getWidth(Unit $unit = Unit::Meters): float
    {
        return match ($unit) {
            Unit::Centimeters => $this->widthInMeters * 100,
            Unit::Decimeters => $this->widthInMeters * 10,
            Unit::Meters => $this->widthInMeters,
        };
    }

    /**
     * Returns the height dimension in the specified unit.
     *
     * @param Unit $unit The unit to convert the height to (default: Meters)
     */
    public function getHeight(Unit $unit = Unit::Meters): float
    {
        return match ($unit) {
            Unit::Centimeters => $this->heightInMeters * 100,
            Unit::Decimeters => $this->heightInMeters * 10,
            Unit::Meters => $this->heightInMeters,
        };
    }
}
