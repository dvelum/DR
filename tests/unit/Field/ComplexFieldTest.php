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

class ComplexFieldTest extends TestCase
{
    private function createNamedRecord(string $name): Record
    {
        return (new RecordFactory())->getFactory()->create($name);
    }

    public function testRecordType()
    {
        $libraryRecord = $this->createNamedRecord('LibraryRecord');
        $libraryRecord->setData(
            [
                'title' => 'Article1',
                'user' => [
                    'name' => 'Anrew',
                    'age' => 18
                ]
            ]
        );

        $this->assertInstanceOf(Record::class, $libraryRecord->get('user'));
        $this->assertEquals(18, $libraryRecord->get('user')->get('age'));

        $userRecord = $this->createNamedRecord('UserRecord');
        $userRecord->setData(
            [
                'name' => 'Tony',
                'age' => 20
            ]
        );
        $libraryRecord->set('user', $userRecord);
        $this->assertEquals(20, $libraryRecord->get('user')->get('age'));
    }

    public function testRecordWrongType()
    {
        $libraryRecord = $this->createNamedRecord('LibraryRecord');
        $this->expectException(\InvalidArgumentException::class);
        $libraryRecord->setData(
            [
                'title' => 'Article1',
                'user' => [
                    'name' => 'Anrew',
                    'age' => 10
                ]
            ]
        );
    }

    public function testWrongObject()
    {
        $libraryRecord = $this->createNamedRecord('LibraryRecord');
        $this->expectException(\InvalidArgumentException::class);
        $libraryRecord->setData(
            [
                'title' => 'Article1',
                'user' => new \stdClass()
            ]
        );
    }

    public function testWrongObjectType()
    {
        $libraryRecord = $this->createNamedRecord('LibraryRecord');
        $this->expectException(\InvalidArgumentException::class);
        $libraryRecord->setData(
            [
                'title' => 'Article1',
                'user' => $this->createNamedRecord('LibraryRecord')
            ]
        );
    }
}