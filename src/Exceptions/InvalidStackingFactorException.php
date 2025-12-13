<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\Exceptions;

/**
 * Exception thrown when stacking factor value is invalid.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidStackingFactorException extends InvalidLoadingMeterParameterException
{
    /**
     * Creates an exception for invalid stacking factor values.
     */
    public static function mustBePositive(): self
    {
        return new self('Stacking factor must be positive');
    }
}
