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

use Dvelum\DR\Factory;
use Dvelum\DR\Record;
use Dvelum\DR\Config;
use Dvelum\DR\ValidationResult;
use PHPUnit\Framework\TestCase;

class RecordTest extends TestCase
{
    private Factory $factory;

    private function getFactory() : Factory
    {
        if(!isset($this->factory)){
            $this->factory = new Factory([
                'TestRecord' => function(){ return include __DIR__.'/configs/TestRecord.php';}
                ]
            );
        }
        return $this->factory;
    }

    private function createConfig(): Config
    {
        return $this->getFactory()->getRecordConfig('TestRecord');
    }

    private function createRecord(): Record
    {
        return $this->getFactory()->create('TestRecord');
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

    public function testWrongField()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('undefinedField', 123);
    }

    public function testNumericLimitMax()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('int_field_limit', 100);
    }

    public function testNumericLimitMin()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('int_field_limit', -100);
    }

    public function testStringLimit()
    {
        $record = $this->createRecord();
        $record->set('string_field_limit', 'abcd');
        $this->assertEquals('abcd', $record->get('string_field_limit'));
    }

    public function testStringLimitMax()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('string_field_limit', 'abcdefg');
    }

    public function testStringLimitMin()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('string_field_limit', 'ab');
    }

    public function testString()
    {
        $record = $this->createRecord();
        $record->set('string_field', 'abcdefg');
        $this->assertEquals('abcdefg', $record->get('string_field'));
        $record->set('string_field', 123);
        $this->assertEquals('123', $record->get('string_field'));
    }

    public function testDefaulDate()
    {
        $record = $this->createRecord();
        $date = date('Y-m-d H:i:s');
        $this->assertEquals($date, $record->get('string_field_date'));
    }

    public function testJson()
    {
        $record = $this->createRecord();
        $record->set('json_field', json_encode(['a' => 1, 'b' => 2]));
        $this->assertEquals(['a' => 1, 'b' => 2], $record->get('json_field'));

        $record->set('json_field', ['a' => 1, 'b' => 2]);
        $this->assertEquals(['a' => 1, 'b' => 2], $record->get('json_field'));
    }

    public function testJsonExceptionString()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('json_field', json_encode('abs'));
        $this->expectException(\InvalidArgumentException::class);
        $record->set('json_field', json_encode(123));
    }
    public function testJsonExceptionNum()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('json_field', json_encode(123));
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

    public function testDateTimeDefault()
    {
        $record = $this->createRecord();
        $value =  $record->get('datetime_default');
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
        $this->assertEquals(new \DateTime( '2021-01-02'),$record->get('datetime_min'));
        // 2021-01-01
    }

    public function testDateTimeObject()
    {
        $record = $this->createRecord();
        $record->set('datetime_min', new \DateTime( '2021-01-02'));
        $this->assertEquals(new \DateTime( '2021-01-02'),$record->get('datetime_min'));
        // 2021-01-01
    }


    public function testDateTimeMax()
    {
        $record = $this->createRecord();
        $this->expectException(\InvalidArgumentException::class);
        $record->set('datetime_max', new \DateTime( '2021-01-01 12:00:01'));
        // 2021-01-01 12:00:00
    }
    public function testDateTimeMaxGood()
    {
        $record = $this->createRecord();
        $record->set('datetime_max', new \DateTime( '2021-01-01 11:59:59'));
        $this->assertEquals(new \DateTime( '2021-01-01 11:59:59'),$record->get('datetime_max'));
        // 2021-01-01 12:00:00
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

    public function testDateType()
    {
        $record = $this->createRecord();
        $record->set('date', '2021-01-01');
        $this->assertInstanceOf(\DateTime::class, $record->get('date'));
        $this->assertEquals('2021-01-01', $record->get('date')->format('Y-m-d'));
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