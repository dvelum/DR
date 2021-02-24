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

namespace Dvelum\DR\UnitTest\Field;

use Dvelum\DR\Record;
use Dvelum\DR\UnitTest\RecordFactory;
use PHPUnit\Framework\TestCase;

class NumericTest extends TestCase
{
    private function createRecord(): Record
    {
        return (new RecordFactory())->getFactory()->create('TestRecord');
    }

    public function testNumeric()
    {
        $record = $this->createRecord();
        $record->set('int_field', 12);
        $this->assertEquals(12, $record->get('int_field'));

        $record->set('int_field', '120');
        $this->assertEquals(120, $record->get('int_field'));

        $record->set('float_field', 129.12);
        $this->assertEquals(129.12, $record->get('float_field'));

        $record->set('float_field', '120.01');
        $this->assertEquals(120.01, $record->get('float_field'));

        try {
            $record->set('float_field', 'sometext');
        } catch (\InvalidArgumentException $e) {
            return;
        }
        $this->fail('Incorrect type validation exception expected');
    }

    public function testNumericLimitMax()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('int_field_limit', 100);
    }

    public function testFloatLimitMax()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('float_field_limit', 100);
    }

    public function testFloatLimitMin()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('float_field_limit', -100);
    }

    public function testNumericLimitMin()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('int_field_limit', -100);
    }
}

