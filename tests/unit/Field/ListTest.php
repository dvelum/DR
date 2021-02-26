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

use Dvelum\DR\Record;
use Dvelum\DR\UnitTest\RecordFactory;
use PHPUnit\Framework\TestCase;

class ListTest extends TestCase
{

    private function createRecord(): Record
    {
        return (new RecordFactory())->getFactory()->create('TestRecord');
    }

    public function testValue()
    {
        $record = $this->createRecord();
        $record->set('list_string', 'key1');
        $this->assertEquals('key1', $record->get('list_string'));
    }

    public function testArrayValue()
    {
        $record = $this->createRecord();
        $record->set('list_multiple', ['key1','key2']);
        $this->assertEquals(['key1','key2'], $record->get('list_multiple'));
    }
    public function testArray2Value()
    {
        $record = $this->createRecord();
        $record->set('list_multiple', 'key1');
        $this->assertEquals(['key1'], $record->get('list_multiple'));
    }

    public function testFailTypeValue()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('list_string', ['key1','key2']);
    }
    public function testFailMTypeValue()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('list_multiple', ['key1','key7']);
    }
    public function testFailSTypeValue()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('list_string', 'undefined');
    }
    public function testFailOTypeValue()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('list_string', new \stdClass());
    }
    public function testIntValue()
    {
        $record = $this->createRecord();
        $record->set('list_num','1');
        $this->assertEquals(1 , $record->get('list_num'));
    }

    public function testIntKey()
    {
        $record = $this->createRecord();
        $record->set('list_string', 3);
        $this->assertEquals('3', $record->get('list_string'));
    }
}




