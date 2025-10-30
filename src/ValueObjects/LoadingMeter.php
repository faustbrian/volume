<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\ValueObjects;

use Cline\Volume\Exceptions\InvalidFloorDimensionsException;
use Cline\Volume\Exceptions\InvalidLoadingMeterParameterException;
use Stringable;

use function number_format;
use function throw_if;

/**
 * Represents loading meter calculations for freight and logistics.
 *
 * Immutable value object for calculating loading meters (LDM) used in
 * freight and logistics to determine truck space requirements. Takes into
 * account quantity, stacking factor, and truck width specifications.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class LoadingMeter implements Stringable
{
    /**
     * Creates a new loading meter calculation instance.
     *
     * @param float $value          The calculated loading meter value
     * @param float $length         The length dimension in centimeters
     * @param float $width          The width dimension in centimeters
     * @param int   $quantity       The number of items being shipped
     * @param float $stackingFactor The factor representing vertical stacking efficiency (1.0 = no stacking)
     * @param float $truckWidth     The internal width of the truck in meters (typically 2.4m)
     */
    private function __construct(
        public float $value,
        public float $length,
        public float $width,
        public int $quantity,
        public float $stackingFactor,
        public float $truckWidth,
    ) {}

    /**
     * Returns the loading meter value as a string representation.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Creates a loading meter calculation from dimensions in centimeters.
     *
     * The formula used is: (quantity * (length * width)) / truckWidth / stackingFactor
     * This calculates how many linear meters of truck space the cargo will occupy.
     *
     * @param float $lengthInCm     The length of each item in centimeters
     * @param float $widthInCm      The width of each item in centimeters
     * @param int   $quantity       The number of items (default: 1)
     * @param float $stackingFactor The vertical stacking efficiency factor (default: 1.0 for no stacking)
     * @param float $truckWidth     The internal truck width in meters (default: 2.4m for standard trucks)
     *
     * @throws InvalidFloorDimensionsException       When length or width is zero or negative
     * @throws InvalidLoadingMeterParameterException When quantity, stackingFactor, or truckWidth is invalid
     */
    public static function fromCentimeters(
        float $lengthInCm,
        float $widthInCm,
        int $quantity = 1,
        float $stackingFactor = 1.0,
        float $truckWidth = 2.4,
    ): self {
        throw_if($lengthInCm <= 0 || $widthInCm <= 0, InvalidFloorDimensionsException::nonPositive());
        throw_if($quantity < 1, InvalidLoadingMeterParameterException::invalidQuantity());
        throw_if($stackingFactor <= 0, InvalidLoadingMeterParameterException::invalidStackingFactor());
        throw_if($truckWidth <= 0, InvalidLoadingMeterParameterException::invalidTruckWidth());

        $lengthInM = $lengthInCm / 100;
        $widthInM = $widthInCm / 100;

        $value = ($quantity * ($lengthInM * $widthInM)) / $truckWidth / $stackingFactor;

        return new self($value, $lengthInCm, $widthInCm, $quantity, $stackingFactor, $truckWidth);
    }

    /**
     * Creates a loading meter calculation from dimensions in meters.
     *
     * Converts meter dimensions to centimeters before calculating loading meters.
     *
     * @param float $length         The length of each item in meters
     * @param float $width          The width of each item in meters
     * @param int   $quantity       The number of items (default: 1)
     * @param float $stackingFactor The vertical stacking efficiency factor (default: 1.0 for no stacking)
     * @param float $truckWidth     The internal truck width in meters (default: 2.4m for standard trucks)
     *
     * @throws InvalidFloorDimensionsException       When length or width is zero or negative
     * @throws InvalidLoadingMeterParameterException When quantity, stackingFactor, or truckWidth is invalid
     */
    public static function fromMeters(
        float $length,
        float $width,
        int $quantity = 1,
        float $stackingFactor = 1.0,
        float $truckWidth = 2.4,
    ): self {
        return self::fromCentimeters($length * 100, $width * 100, $quantity, $stackingFactor, $truckWidth);
    }

    /**
     * Returns the calculated loading meter value.
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * Returns the loading meter value formatted with the specified decimal places.
     *
     * @param int $decimals The number of decimal places (default: 2)
     */
    public function format(int $decimals = 2): string
    {
        return number_format($this->value, $decimals);
    }
}
