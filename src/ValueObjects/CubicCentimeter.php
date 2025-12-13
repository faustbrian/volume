<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\ValueObjects;

use Cline\Volume\Exceptions\NonPositiveDimensionsException;
use Stringable;

use function number_format;
use function throw_if;

/**
 * Represents volume measurements in cubic centimeters.
 *
 * Immutable value object for calculating and formatting volume in cubic
 * centimeters. Stores the calculated volume along with the original
 * dimensions used for the calculation.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class CubicCentimeter implements Stringable
{
    /**
     * Creates a new cubic centimeter volume instance.
     *
     * @param float $value  The calculated volume in cubic centimeters
     * @param float $length The length dimension in centimeters
     * @param float $width  The width dimension in centimeters
     * @param float $height The height dimension in centimeters
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
     * Creates a volume from dimensions specified in centimeters.
     *
     * @param float $length The length in centimeters
     * @param float $width  The width in centimeters
     * @param float $height The height in centimeters
     *
     * @throws NonPositiveDimensionsException When any dimension is zero or negative
     */
    public static function fromCentimeters(float $length, float $width, float $height): self
    {
        throw_if($length <= 0 || $width <= 0 || $height <= 0, NonPositiveDimensionsException::create());

        return new self($length * $width * $height, $length, $width, $height);
    }

    /**
     * Creates a volume from dimensions specified in meters.
     *
     * Converts meter dimensions to centimeters before calculating volume.
     *
     * @param float $length The length in meters
     * @param float $width  The width in meters
     * @param float $height The height in meters
     *
     * @throws NonPositiveDimensionsException When any dimension is zero or negative
     */
    public static function fromMeters(float $length, float $width, float $height): self
    {
        $length *= 100;
        $width *= 100;
        $height *= 100;

        return self::fromCentimeters($length, $width, $height);
    }

    /**
     * Returns the calculated volume value in cubic centimeters.
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
