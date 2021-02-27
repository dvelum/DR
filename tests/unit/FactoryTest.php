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

namespace Dvelum\DR\UnitTest;

use Dvelum\DR\Export\Database;
use Dvelum\DR\Factory;
use Dvelum\DR\Record;
use Dvelum\DR\Type\StringType;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    private function createFactory(): Factory
    {
        return new Factory(
            [
                'records' =>
                    [
                        'TestRecord' => function (): array {
                            return [
                                'fields' => [
                                    'field1' => ['type' => 'int'],
                                    'field2' => ['type' => StringType::class],
                                    'field3' => ['type' => StringType::class, 'validator' => Validator::class],
                                ]
                            ];
                        }
                    ],
                'exports' =>
                    [
                        'Database' => Database::class
                    ]
            ]
        );
    }

    public function testFactory()
    {
        $factory = $this->createFactory();
        $record = $factory->create('TestRecord');
        $record->getData();

        $this->assertInstanceOf(Record::class, $record);
        $this->assertTrue($record->getConfig()->fieldExists('field2'));

        $this->expectException(\InvalidArgumentException::class);
        $factory->create('undefinedObject');
    }

    public function testGetDbExport()
    {
        $factory = $this->createFactory();
        $export = $factory->getExport('Database');
        $this->assertInstanceOf(Database::class, $export);
    }
}