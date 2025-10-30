<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\ValueObjects;

use Cline\Volume\Exceptions\InvalidDimensionsException;
use Stringable;

use function number_format;
use function throw_if;

/**
 * Represents volume measurements in cubic decimeters.
 *
 * Immutable value object for calculating and formatting volume in cubic
 * decimeters (liters). Stores the calculated volume along with the original
 * dimensions used for the calculation.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class CubicDecimeter implements Stringable
{
    /**
     * Creates a new cubic decimeter volume instance.
     *
     * @param float $value  The calculated volume in cubic decimeters
     * @param float $length The length dimension in decimeters
     * @param float $width  The width dimension in decimeters
     * @param float $height The height dimension in decimeters
     */
    private function __construct(
        public float $value,
        public float $length,
        public float $width,
        public float $height,
    ) {}

    /**
     * Returns the volume as a string representation.
     */
    public function __toString(): string
    {
        return (string) $this->value;
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

        return new self($length * $width * $height, $length, $width, $height);
    }

    /**
     * Creates a volume from dimensions specified in meters.
     *
     * Converts meter dimensions to decimeters before calculating volume.
     *
     * @param float $length The length in meters
     * @param float $width  The width in meters
     * @param float $height The height in meters
     *
     * @throws InvalidDimensionsException When any dimension is zero or negative
     */
    public static function fromMeters(float $length, float $width, float $height): self
    {
        $length *= 10;
        $width *= 10;
        $height *= 10;

        return self::fromDecimeters($length, $width, $height);
    }

    /**
     * Creates a volume from dimensions specified in centimeters.
     *
     * Converts centimeter dimensions to decimeters before calculating volume.
     *
     * @param float $length The length in centimeters
     * @param float $width  The width in centimeters
     * @param float $height The height in centimeters
     *
     * @throws InvalidDimensionsException When any dimension is zero or negative
     */
    public static function fromCentimeters(float $length, float $width, float $height): self
    {
        return self::fromDecimeters($length / 10, $width / 10, $height / 10);
    }

    /**
     * Returns the calculated volume value in cubic decimeters.
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * Returns the volume formatted with the specified decimal places.
     *
     * @param int $decimals The number of decimal places (default: 2)
     */
    public function format(int $decimals = 2): string
    {
        return number_format($this->value, $decimals);
    }
}
