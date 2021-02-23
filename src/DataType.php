<?php
/*
 * DVelum DR library https://github.com/dvelum/dr
 *
 * MIT License
 *
 * Copyright (C) 2011-2021 Kirill Yegorov
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
declare(strict_types=1);
namespace Dvelum\DR;

use Dvelum\DR\Type\BoolType;
use Dvelum\DR\Type\DateTimeType;
use Dvelum\DR\Type\DateType;
use Dvelum\DR\Type\FloatType;
use Dvelum\DR\Type\IntType;
use Dvelum\DR\Type\JsonType;
use Dvelum\DR\Type\StringType;


class DataType
{
    public const STRING_TYPE = StringType::class;
    public const INT_TYPE = IntType::class;
    public const FLOAT_TYPE = FloatType::class;
    public const DATE_TYPE = DateType::class;
    public const DATETIME_TYPE =DateTimeType::class;
    public const BOOL_TYPE =BoolType::class;
    public const JSON_TYPE =  JsonType::class;

    public const ALIASES = [
        'string' => self::STRING_TYPE,
        'int' => self::INT_TYPE,
        'float' => self::FLOAT_TYPE,
        'date' => self::DATE_TYPE,
        'datetime' => self::DATETIME_TYPE,
        'bool' => self::BOOL_TYPE,
        'json' => self::JSON_TYPE
    ];
}