<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\ValueObjects;

use Cline\Volume\Exceptions\InvalidFloorDimensionsException;
use Stringable;

use function number_format;
use function throw_if;

/**
 * Represents floor area measurements in square meters.
 *
 * Immutable value object for calculating and formatting two-dimensional
 * floor area in square meters. Used for logistics and space calculations
 * where height is not required.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class FloorMeter implements Stringable
{
    /**
     * Creates a new floor meter area instance.
     *
     * @param float $value  The calculated floor area in square meters
     * @param float $length The length dimension in meters
     * @param float $width  The width dimension in meters
     */
    private function __construct(
        public float $value,
        public float $length,
        public float $width,
    ) {}

    /**
     * Returns the floor area as a string representation.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Creates a floor area from dimensions specified in meters.
     *
     * @param float $length The length in meters
     * @param float $width  The width in meters
     *
     * @throws InvalidFloorDimensionsException When any dimension is zero or negative
     */
    public static function fromMeters(float $length, float $width): self
    {
        throw_if($length <= 0 || $width <= 0, InvalidFloorDimensionsException::nonPositive());

        return new self($length * $width, $length, $width);
    }

    /**
     * Creates a floor area from dimensions specified in centimeters.
     *
     * Converts centimeter dimensions to meters before calculating area.
     *
     * @param float $length The length in centimeters
     * @param float $width  The width in centimeters
     *
     * @throws InvalidFloorDimensionsException When any dimension is zero or negative
     */
    public static function fromCentimeters(float $length, float $width): self
    {
        return self::fromMeters($length / 100, $width / 100);
    }

    /**
     * Returns the calculated floor area value in square meters.
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * Returns the floor area formatted with the specified decimal places.
     *
     * @param int $decimals The number of decimal places (default: 2)
     */
    public function format(int $decimals = 2): string
    {
        return number_format($this->value, $decimals);
    }
}
