<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\Exceptions;

use function implode;
use function sprintf;

/**
 * Exception thrown when required dimension values are missing.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MissingRequiredDimensionsException extends InvalidDimensionsException
{
    /**
     * Creates an exception when required dimension values are missing.
     *
     * @param array<int, string> $missingDimensions The names of dimensions that are missing
     */
    public static function fromMissing(array $missingDimensions): self
    {
        return new self(
            sprintf('Missing required dimensions: %s', implode(', ', $missingDimensions)),
        );
    }
}
