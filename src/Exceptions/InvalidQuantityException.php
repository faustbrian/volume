<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\Exceptions;

/**
 * Exception thrown when quantity value is invalid.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidQuantityException extends InvalidLoadingMeterParameterException
{
    /**
     * Creates an exception for invalid quantity values.
     */
    public static function mustBeAtLeastOne(): self
    {
        return new self('Quantity must be at least 1');
    }
}
