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

use Dvelum\DR\Config;
use Dvelum\DR\Factory;
use Dvelum\DR\Record;
use Dvelum\DR\ValidationResult;
use PHPUnit\Framework\TestCase;

class RecordTest extends TestCase
{
    private function createRecord(): Record
    {
        return (new RecordFactory())->getFactory()->create('TestRecord');
    }

    private function createConfig(): Config
    {
        return (new RecordFactory())->getFactory()->getRecordConfig('TestRecord');
    }

    public function testWrongField()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('undefinedField', 123);
    }


    public function testSetData()
    {
        $record = $this->createRecord();
        $record->setData(
            [
                'int_field' => 11,
                'float_field' => 11.2
            ]
        );
        $this->assertEquals(11, $record->get('int_field'));
        $this->assertEquals(11.2, $record->get('float_field'));
    }

    public function testValidator()
    {
        //string_field_email
        $record = $this->createRecord();
        $record->set('string_field_email', 'testmail@gmail.com');
        $this->assertEquals('testmail@gmail.com', $record->get('string_field_email'));
        $this->expectException(\InvalidArgumentException::class);
        $record->set('string_field_email', 'notmail');
    }

    public function testUndefinedField()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->get('undefinedField');
    }

    public function testCommitChanges()
    {
        $record = $this->createRecord();
        $record->getData();
        $record->commitChanges();
        $updates = $record->getUpdates();
        $this->assertTrue(empty($updates));
        $record->set('int_field', 1);
        $this->assertTrue($record->hasUpdates());
        $this->assertNotEmpty($record->getUpdates());
        $record->commitChanges();
        $this->assertEmpty($record->getUpdates());
        $this->assertEquals(1, $record->get('int_field'));
    }

    public function testDefault()
    {
        $record = $this->createRecord();
        $this->assertEquals('default', $record->get('string_default'));
    }

    public function testIsRequired()
    {
        $config = $this->createConfig();
        $this->assertTrue($config->getField('string_field_email')->isRequired());
        $this->assertFalse($config->getField('int_field')->isRequired());
    }

    public function testValidateRequired()
    {
        $record = $this->createRecord();
        $result = $record->validateRequired();
        $this->assertFalse($result->isSuccess());
        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue(isset($result->getErrors()['string_field_email']));
    }

    public function testNoUpdates()
    {
        $record = $this->createRecord();
        //init defaults
        $record->getData();

        $record->set('int_field', 10);
        $record->commitChanges();
        $this->assertTrue(empty($record->getUpdates()));
        $record->set('int_field', 10);
        $this->assertTrue(empty($record->getUpdates()));
        $record->set('int_field', 11);
        $this->assertTrue(!empty($record->getUpdates()));
    }


    public function testBoolType()
    {
        $record = $this->createRecord();
        $record->set('bool_field', '1');
        $this->assertTrue(is_bool($record->get('bool_field')));
        $this->assertEquals(true, $record->get('bool_field'));
        $record->set('bool_field', '0');
        $this->assertTrue(is_bool($record->get('bool_field')));
        $this->assertEquals(false, $record->get('bool_field'));
    }

}