<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Volume\Enums;

/**
 * Represents measurement units for dimensional calculations.
 *
 * Defines the available units of measurement that can be used throughout
 * the volume calculation system for specifying and converting dimensions.
 *
 * @author Brian Faust <brian@cline.sh>
 */
enum Unit: string
{
    case Centimeters = 'centimeters';
    case Decimeters = 'decimeters';
    case Meters = 'meters';
}
