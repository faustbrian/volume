<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\Exceptions;

use function sprintf;

/**
 * Exception thrown when dimension array does not contain exactly 3 values.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class DimensionArrayCountMismatchException extends InvalidDimensionsException
{
    /**
     * Creates an exception when dimension array count is incorrect.
     *
     * @param int $given The actual number of array elements provided
     */
    public static function fromCount(int $given): self
    {
        return new self(
            sprintf('Dimensions array must contain exactly 3 values, %d given', $given),
        );
    }
}
