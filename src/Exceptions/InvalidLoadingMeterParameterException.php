<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\Exceptions;

use InvalidArgumentException;

/**
 * Exception thrown when loading meter calculation parameters are invalid.
 *
 * Validates parameters used in loading meter calculations including
 * quantity, stacking factor, and truck width specifications.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidLoadingMeterParameterException extends InvalidArgumentException
{
    /**
     * Creates an exception for invalid quantity values.
     */
    public static function invalidQuantity(): self
    {
        return new self('Quantity must be at least 1');
    }

    /**
     * Creates an exception for invalid stacking factor values.
     */
    public static function invalidStackingFactor(): self
    {
        return new self('Stacking factor must be positive');
    }

    /**
     * Creates an exception for invalid truck width values.
     */
    public static function invalidTruckWidth(): self
    {
        return new self('Truck width must be positive');
    }
}
