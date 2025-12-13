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
 * Base exception for all dimension validation errors.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class InvalidDimensionsException extends InvalidArgumentException implements VolumeException
{
    // Abstract base - no factory methods
}
