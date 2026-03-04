<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\Exceptions;

/**
 * Exception thrown when dimension values are not positive numbers.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class NonPositiveDimensionsException extends InvalidDimensionsException
{
    /**
     * Creates an exception for non-positive dimension values.
     */
    public static function create(): self
    {
        return new self('All dimensions must be positive numbers');
    }
}
