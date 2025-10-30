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
 * Exception thrown when floor measurement dimensions are invalid.
 *
 * Handles validation errors specific to two-dimensional floor area
 * calculations where height is not required.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidFloorDimensionsException extends InvalidArgumentException
{
    /**
     * Creates an exception for non-positive floor dimension values.
     */
    public static function nonPositive(): self
    {
        return new self('Length and width must be positive numbers');
    }
}
