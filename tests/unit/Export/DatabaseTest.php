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

namespace Dvelum\DR\UnitTest\Export;

use Dvelum\DR\Export\Database;
use Dvelum\DR\Factory;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private function getFactory(): Factory
    {
        if (!isset($this->factory)) {
            $this->factory =  Factory::fromRecordsArray(
                [
                    'TestRecord' => function () {
                        return include __DIR__ . '/../configs/TestRecord.php';
                    }
                ]
            );
        }
        return $this->factory;
    }

    public function testExport()
    {
        $factory = $this->getFactory();
        $record = $factory->create('TestRecord');
        $record->set('json_field', ['a' => 1, 'b' => 2]);
        $export = new Database();
        $result = $export->exportRecord($record);
        $this->assertEquals(\json_encode(['a' => 1, 'b' => 2]), $result['json_field']);
    }

    public function testUpdatesExport()
    {
        $factory = $this->getFactory();
        $record = $factory->create('TestRecord');
        $record->set('int_field', 1);
        $export = new Database();
        $result = $export->exportUpdates($record);
        $this->assertEquals(1, $result['int_field']);
    }
}