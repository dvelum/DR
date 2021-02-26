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

class RecordFactory
{
    private Factory $factory;

    public function getFactory(): Factory
    {
        if (!isset($this->factory)) {
            $this->factory =  Factory::fromRecordsArray(
                [
                    'TestRecord' => function () {
                        return include __DIR__ . '/configs/TestRecord.php';
                    },
                    'LibraryRecord' => function () {
                        return include __DIR__ . '/configs/LibraryRecord.php';
                    },
                    'UserRecord' => function () {
                        return include __DIR__ . '/configs/UserRecord.php';
                    },
                ]
            );
        }
        return $this->factory;
    }

    public function createConfig(): Config
    {
        return $this->getFactory()->getRecordConfig('TestRecord');
    }

    public function createRecord(): Record
    {
        return $this->getFactory()->create('TestRecord');
    }

    public function createNamedRecord(string $name): Record
    {
        return $this->getFactory()->create($name);
    }
}