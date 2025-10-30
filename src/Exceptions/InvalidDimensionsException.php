<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\Exceptions;

use InvalidArgumentException;

use function implode;
use function sort;
use function sprintf;

/**
 * Exception thrown when dimension values are invalid or malformed.
 *
 * Handles validation errors for dimensional measurements including
 * non-positive values, incorrect array structures, and missing keys.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidDimensionsException extends InvalidArgumentException
{
    /**
     * Creates an exception for non-positive dimension values.
     */
    public static function nonPositive(): self
    {
        return new self('All dimensions must be positive numbers');
    }

    /**
     * Creates an exception when dimension array count is incorrect.
     *
     * @param int $given The actual number of array elements provided
     */
    public static function arrayCountMismatch(int $given): self
    {
        return new self(
            sprintf('Dimensions array must contain exactly 3 values, %d given', $given),
        );
    }

    /**
     * Creates an exception when dimension array has incorrect keys.
     *
     * @param array<int, string> $providedKeys The array keys that were provided
     */
    public static function missingKeys(array $providedKeys): self
    {
        sort($providedKeys);

        return new self(
            sprintf(
                'Dimensions array must have keys [length, width, height], got [%s]',
                implode(', ', $providedKeys),
            ),
        );
    }

    /**
     * Creates an exception when required dimension values are missing.
     *
     * @param array<int, string> $missingDimensions The names of dimensions that are missing
     */
    public static function missingRequired(array $missingDimensions): self
    {
        return new self(
            sprintf('Missing required dimensions: %s', implode(', ', $missingDimensions)),
        );
    }
}
