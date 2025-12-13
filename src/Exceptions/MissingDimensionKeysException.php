<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\Exceptions;

use function implode;
use function sort;
use function sprintf;

/**
 * Exception thrown when dimension array has incorrect keys.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MissingDimensionKeysException extends InvalidDimensionsException
{
    /**
     * Creates an exception when dimension array has incorrect keys.
     *
     * @param array<int, string> $providedKeys The array keys that were provided
     */
    public static function fromProvidedKeys(array $providedKeys): self
    {
        sort($providedKeys);

        return new self(
            sprintf(
                'Dimensions array must have keys [length, width, height], got [%s]',
                implode(', ', $providedKeys),
            ),
        );
    }
}
