<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Cline\Volume\Enums\Unit;
use Cline\Volume\Exceptions\InvalidDimensionsException;
use Cline\Volume\Exceptions\InvalidLoadingMeterParameterException;
use Cline\Volume\ValueObjects\CubicDecimeter;
use Cline\Volume\ValueObjects\CubicMeter;
use Cline\Volume\ValueObjects\FloorMeter;
use Cline\Volume\ValueObjects\LoadingMeter;
use Cline\Volume\ValueObjects\Volume;

use function Cline\Volume\volume;
use function describe;
use function expect;
use function round;
use function test;

describe('volume() function', function (): void {
    describe('Happy Paths', function (): void {
        test('creates Volume from indexed array with default centimeters unit', function (): void {
            $vol = volume([120, 80, 100]);
            expect($vol)->toBeInstanceOf(Volume::class);
            expect($vol->centimeters()->value())->toBe(120.0 * 80.0 * 100.0);
        });

        test('creates Volume from indexed array with meters unit', function (): void {
            $vol = volume([1.2, 0.8, 1.0], Unit::Meters);
            expect($vol)->toBeInstanceOf(Volume::class);
            expect($vol->meters()->value())->toBe(1.2 * 0.8 * 1.0);
        });

        test('creates Volume from associative array', function (): void {
            $vol = volume(['height' => 100, 'length' => 120, 'width' => 80]);
            expect($vol)->toBeInstanceOf(Volume::class);
            expect($vol->centimeters()->value())->toBe(120.0 * 80.0 * 100.0);
        });

        test('creates Volume with named parameters', function (): void {
            $vol = volume(length: 120, width: 80, height: 100);
            expect($vol)->toBeInstanceOf(Volume::class);
            expect($vol->centimeters()->value())->toBe(120.0 * 80.0 * 100.0);
        });

        test('supports Unit::Centimeters', function (): void {
            $vol = volume([120, 80, 100], Unit::Centimeters);
            expect($vol->centimeters()->value())->toBe(120.0 * 80.0 * 100.0);
        });

        test('supports Unit::Meters', function (): void {
            $vol = volume([1.2, 0.8, 1.0], Unit::Meters);
            expect($vol->meters()->value())->toBe(1.2 * 0.8 * 1.0);
        });

        test('supports Unit::Decimeters', function (): void {
            $vol = volume([12, 8, 10], Unit::Decimeters);
            expect($vol->decimeters()->value())->toBe(12.0 * 8.0 * 10.0);
        });

        test('creates Volume from decimeters with named parameters', function (): void {
            $vol = volume(unit: Unit::Decimeters, length: 12, width: 8, height: 10);
            expect($vol)->toBeInstanceOf(Volume::class);
            expect($vol->decimeters()->value())->toBe(12.0 * 8.0 * 10.0);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception when dimensions array has wrong count', function (): void {
            volume([120, 80]);
        })->throws(InvalidDimensionsException::class, 'Dimensions array must contain exactly 3 values');

        test('throws exception when required dimensions are missing', function (): void {
            volume(length: 120, width: 80);
        })->throws(InvalidDimensionsException::class, 'Missing required dimensions: height');

        test('throws exception when only length is missing', function (): void {
            volume(width: 80, height: 100);
        })->throws(InvalidDimensionsException::class, 'Missing required dimensions: length');

        test('throws exception when only width is missing', function (): void {
            volume(length: 120, height: 100);
        })->throws(InvalidDimensionsException::class, 'Missing required dimensions: width');

        test('throws exception for invalid associative array keys', function (): void {
            volume(['length' => 120, 'width' => 80, 'depth' => 100]);
        })->throws(InvalidDimensionsException::class);
    });
});

describe('Volume class', function (): void {
    describe('Creation', function (): void {
        test('creates from centimeters', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            expect($vol)->toBeInstanceOf(Volume::class);
        });

        test('creates from meters', function (): void {
            $vol = Volume::fromMeters(1.2, 0.8, 1.0);
            expect($vol)->toBeInstanceOf(Volume::class);
        });

        test('creates from array with centimeters', function (): void {
            $vol = Volume::fromArray([120, 80, 100], Unit::Centimeters);
            expect($vol)->toBeInstanceOf(Volume::class);
        });

        test('creates from decimeters', function (): void {
            $vol = Volume::fromDecimeters(12, 8, 10);
            expect($vol)->toBeInstanceOf(Volume::class);
            expect($vol->decimeters()->value())->toBe(12.0 * 8.0 * 10.0);
        });

        test('creates from array with decimeters', function (): void {
            $vol = Volume::fromArray([12, 8, 10], Unit::Decimeters);
            expect($vol)->toBeInstanceOf(Volume::class);
            expect($vol->decimeters()->value())->toBe(12.0 * 8.0 * 10.0);
        });

        test('throws exception for non-positive dimensions', function (): void {
            Volume::fromCentimeters(120, 0, 100);
        })->throws(InvalidDimensionsException::class, 'All dimensions must be positive numbers');
    });

    describe('Unit Conversions', function (): void {
        test('converts to cubic centimeters', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            $cm3 = $vol->centimeters();

            expect($cm3->value())->toBe(120.0 * 80.0 * 100.0);
            expect($cm3->length)->toBe(120.0);
            expect($cm3->width)->toBe(80.0);
            expect($cm3->height)->toBe(100.0);
        });

        test('converts to cubic meters', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            $m3 = $vol->meters();

            expect($m3->value())->toBe(1.2 * 0.8 * 1.0);
            expect($m3->length)->toBe(1.2);
            expect($m3->width)->toBe(0.8);
            expect($m3->height)->toBe(1.0);
        });

        test('converts to cubic decimeters', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            $dm3 = $vol->decimeters();

            expect($dm3->value())->toBe(12.0 * 8.0 * 10.0);
        });
    });

    describe('Loading Meters Calculation', function (): void {
        test('calculates loading meters correctly', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            $ldm = $vol->loadingMeters();

            $expected = ((120 / 100) * (80 / 100)) / 2.4;
            expect(round($ldm->value(), 2))->toBe(round($expected, 2));
        });

        test('loading meters example from documentation: 120cm x 80cm', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            $ldm = $vol->loadingMeters();

            expect(round($ldm->value(), 1))->toBe(0.4);
        });

        test('calculates LDM with quantity parameter', function (): void {
            $ldm = LoadingMeter::fromCentimeters(120, 80, quantity: 5);

            // 5 units of 0.4 LDM each = 2.0 LDM
            expect(round($ldm->value(), 1))->toBe(2.0);
            expect($ldm->quantity)->toBe(5);
        });

        test('calculates LDM with stacking factor', function (): void {
            $ldm = LoadingMeter::fromCentimeters(120, 80, stackingFactor: 2.0);

            // Single pallet stackable 2 high: 0.4 / 2 = 0.2 LDM
            expect(round($ldm->value(), 1))->toBe(0.2);
            expect($ldm->stackingFactor)->toBe(2.0);
        });

        test('calculates LDM with quantity and stacking factor combined', function (): void {
            $ldm = LoadingMeter::fromCentimeters(120, 80, quantity: 10, stackingFactor: 2.0);

            // 10 pallets, stackable 2 high: (10 * 0.4) / 2 = 2.0 LDM
            expect(round($ldm->value(), 1))->toBe(2.0);
        });

        test('calculates LDM with custom truck width', function (): void {
            $ldm = LoadingMeter::fromCentimeters(120, 80, truckWidth: 3.0);

            // Using 3m truck instead of 2.4m: (1.2 * 0.8) / 3.0 = 0.32
            expect(round($ldm->value(), 2))->toBe(0.32);
            expect($ldm->truckWidth)->toBe(3.0);
        });

        test('calculates LDM with all parameters', function (): void {
            $ldm = LoadingMeter::fromCentimeters(
                lengthInCm: 120,
                widthInCm: 80,
                quantity: 6,
                stackingFactor: 3.0,
                truckWidth: 2.5,
            );

            // (6 * (1.2 * 0.8)) / 2.5 / 3.0
            $expected = (6 * 0.96) / 2.5 / 3.0;
            expect(round($ldm->value(), 4))->toBe(round($expected, 4));
        });

        test('formats loading meters', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            $ldm = $vol->loadingMeters();

            expect($ldm->format(2))->toBeString();
        });

        test('loading meters are stringable', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            $ldm = $vol->loadingMeters();

            expect((string) $ldm)->toBeString();
        });

        test('throws exception for invalid quantity', function (): void {
            LoadingMeter::fromCentimeters(120, 80, quantity: 0);
        })->throws(InvalidLoadingMeterParameterException::class, 'Quantity must be at least 1');

        test('throws exception for invalid stacking factor', function (): void {
            LoadingMeter::fromCentimeters(120, 80, stackingFactor: 0);
        })->throws(InvalidLoadingMeterParameterException::class, 'Stacking factor must be positive');

        test('throws exception for invalid truck width', function (): void {
            LoadingMeter::fromCentimeters(120, 80, truckWidth: -1);
        })->throws(InvalidLoadingMeterParameterException::class, 'Truck width must be positive');

        test('Volume class supports LDM parameters via chainable API', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            $ldm = $vol->loadingMeters(quantity: 5, stackingFactor: 2.0, truckWidth: 2.5);

            $expected = (5 * (1.2 * 0.8)) / 2.5 / 2.0;
            expect(round($ldm->value(), 4))->toBe(round($expected, 4));
        });
    });

    describe('Floor Meters Calculation', function (): void {
        test('calculates floor meters as area', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            $fm = $vol->floorMeters();

            expect($fm->value())->toBe(1.2 * 0.8);
            expect($fm->length)->toBe(1.2);
            expect($fm->width)->toBe(0.8);
        });

        test('formats floor meters', function (): void {
            $vol = Volume::fromCentimeters(200, 150, 100);
            $fm = $vol->floorMeters();

            expect($fm->format(2))->toBe('3.00');
        });

        test('floor meters are stringable', function (): void {
            $vol = Volume::fromCentimeters(200, 150, 100);
            $fm = $vol->floorMeters();

            expect((string) $fm)->toBeString();
        });
    });

    describe('Dimension Getters', function (): void {
        test('gets length in specified unit', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);

            expect($vol->getLength(Unit::Centimeters))->toBe(120.0);
            expect($vol->getLength(Unit::Meters))->toBe(1.2);
        });

        test('gets width in specified unit', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);

            expect($vol->getWidth(Unit::Centimeters))->toBe(80.0);
            expect($vol->getWidth(Unit::Meters))->toBe(0.8);
        });

        test('gets height in specified unit', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);

            expect($vol->getHeight(Unit::Centimeters))->toBe(100.0);
            expect($vol->getHeight(Unit::Meters))->toBe(1.0);
        });

        test('gets dimensions in decimeters', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);

            expect($vol->getLength(Unit::Decimeters))->toBe(12.0);
            expect($vol->getWidth(Unit::Decimeters))->toBe(8.0);
            expect($vol->getHeight(Unit::Decimeters))->toBe(10.0);
        });
    });

    describe('Value Object Formatting', function (): void {
        test('cubic centimeters can be formatted', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            $cm3 = $vol->centimeters();

            expect($cm3->format(0))->toBeString();
            expect($cm3->format(2))->toContain('.');
        });

        test('cubic meters can be formatted', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);
            $m3 = $vol->meters();

            expect($m3->format(3))->toBeString();
        });

        test('cubic decimeters can be formatted with various decimal places', function (): void {
            $dm3 = Volume::fromDecimeters(12.345, 8.678, 10.111)->decimeters();

            expect($dm3->format(0))->toBe('1,083');
            expect($dm3->format(2))->toBe('1,083.19');
            expect($dm3->format(4))->toBe('1,083.1905');
        });

        test('cubic meters can be formatted with various decimal places', function (): void {
            $m3 = Volume::fromMeters(1.234_5, 0.867_8, 1.011_1)->meters();

            expect($m3->format(0))->toBe('1');
            expect($m3->format(2))->toBe('1.08');
            expect($m3->format(4))->toBe('1.0832');
        });

        test('floor meters can be formatted with various decimal places', function (): void {
            $vol = Volume::fromCentimeters(123.456, 87.890, 100);
            $fm = $vol->floorMeters();

            expect($fm->format(0))->toBe('1');
            expect($fm->format(2))->toBe('1.09');
            expect($fm->format(4))->toBe('1.0851');
        });

        test('loading meters can be formatted with various decimal places', function (): void {
            $ldm = LoadingMeter::fromCentimeters(123.456, 87.890, quantity: 5);

            expect($ldm->format(0))->toBe('2');
            expect($ldm->format(2))->toBe('2.26');
            expect($ldm->format(4))->toBe('2.2605');
        });

        test('all value objects are stringable', function (): void {
            $vol = Volume::fromCentimeters(120, 80, 100);

            expect((string) $vol->centimeters())->toBeString();
            expect((string) $vol->meters())->toBeString();
            expect((string) $vol->decimeters())->toBeString();
            expect((string) $vol->floorMeters())->toBeString();
            expect((string) $vol->loadingMeters())->toBeString();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles very small dimensions', function (): void {
            $vol = Volume::fromCentimeters(0.1, 0.1, 0.1);
            expect(round($vol->centimeters()->value(), 6))->toBe(0.001);
        });

        test('handles very large dimensions', function (): void {
            $vol = Volume::fromCentimeters(10_000, 10_000, 10_000);
            expect($vol->meters()->value())->toBe(1_000_000.0);
        });

        test('mixed integer and float dimensions', function (): void {
            $vol = Volume::fromCentimeters(120, 80.5, 100);
            expect($vol->centimeters()->value())->toBe(120.0 * 80.5 * 100.0);
        });

        test('CubicDecimeter fromCentimeters conversion', function (): void {
            $dm3 = CubicDecimeter::fromCentimeters(120, 80, 100);
            expect($dm3->value())->toBe(12.0 * 8.0 * 10.0);
        });

        test('CubicMeter fromCentimeters conversion', function (): void {
            $m3 = CubicMeter::fromCentimeters(120, 80, 100);
            expect($m3->value())->toBe(1.2 * 0.8 * 1.0);
        });

        test('FloorMeter fromCentimeters conversion', function (): void {
            $fm = FloorMeter::fromCentimeters(120, 80);
            expect($fm->value())->toBe(1.2 * 0.8);
        });

        test('LoadingMeter fromMeters conversion', function (): void {
            $ldm = LoadingMeter::fromMeters(1.2, 0.8);
            expect(round($ldm->value(), 2))->toBe(0.4);
        });
    });
});
