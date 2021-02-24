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

class DateTest extends TestCase
{

    private function createRecord(): Record
    {
        return (new RecordFactory())->getFactory()->create('TestRecord');
    }

    public function testDefaulDate()
    {
        $record = $this->createRecord();
        $date = date('Y-m-d H:i:s');
        $this->assertEquals($date, $record->get('string_field_date'));
    }

    public function testDateTimeDefault()
    {
        $record = $this->createRecord();
        $value = $record->get('datetime_default');
        $this->assertInstanceOf(\DateTime::class, $value);
        $this->assertEquals(new \DateTime('2021-01-01 00:00:00'), $value);
    }

    public function testDateTimeMinString()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('datetime_min', '2020-12-31');
        // 2021-01-01
    }

    public function testDateTimeMinObject()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('datetime_min', new \DateTime('2020-12-31'));
        // 2021-01-01
    }

    public function testDateTimeString()
    {
        $record = $this->createRecord();
        $record->set('datetime_min', '2021-01-02');
        $this->assertEquals(new \DateTime('2021-01-02'), $record->get('datetime_min'));
        // 2021-01-01
    }

    public function testDateTimeObject()
    {
        $record = $this->createRecord();
        $record->set('datetime_min', new \DateTime('2021-01-02'));
        $this->assertEquals(new \DateTime('2021-01-02'), $record->get('datetime_min'));
        // 2021-01-01
    }

    public function testDateTimeMax()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('datetime_max', new \DateTime('2021-01-01 12:00:01'));
        // 2021-01-01 12:00:00
    }

    public function testDateTimeMaxGood()
    {
        $record = $this->createRecord();
        $record->set('datetime_max', new \DateTime('2021-01-01 11:59:59'));
        $this->assertEquals(new \DateTime('2021-01-01 11:59:59'), $record->get('datetime_max'));
        // 2021-01-01 12:00:00
    }

    public function testDateType()
    {
        $record = $this->createRecord();
        $record->set('date', '2021-01-01');
        $this->assertInstanceOf(\DateTime::class, $record->get('date'));
        $this->assertEquals('2021-01-01', $record->get('date')->format('Y-m-d'));
    }
}

