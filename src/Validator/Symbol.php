<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Symbol extends Constraint
{
    public const NO_SUCH_SYMBOL_ERROR = 'd5c85e70-4726-4d96-a8f2-0b775c0e9a92';

    protected const ERROR_NAMES = [
        self::NO_SUCH_SYMBOL_ERROR => 'NO_SUCH_SYMBOL_ERROR',
    ];

    public string $unknownSymbolMessage = 'Unknown company symbol "{{ value }}"';
}
