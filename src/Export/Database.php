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

namespace Dvelum\DR\Export;

use Dvelum\DR\Config;
use Dvelum\DR\Record;
use Dvelum\DR\Type\DateTimeType;
use Dvelum\DR\Type\DateType;
use Dvelum\DR\Type\JsonType;
use \DateTime;
use function \json_encode;

class Database implements ExportInterface
{
    protected string $dateTimeFormat = 'Y-m-d H:i:s';
    protected string $dateFormat = 'Y-m-d';

    /**
     * @param Record $record
     * @return array <string,mixed>
     * @throws \JsonException
     */
    public function exportRecord(Record $record): array
    {
        return $this->convertData($record->getConfig(), $record->getData());
    }

    /**
     * @param Record $record
     * @return array <string,mixed>
     * @throws \JsonException
     */
    public function exportUpdates(Record $record): array
    {
        return $this->convertData($record->getConfig(), $record->getUpdates());
    }

    /**
     * @param Config $config
     * @param array <string,mixed> $data
     * @return array <string,mixed>
     * @throws \JsonException
     */
    private function convertData(Config $config, array $data): array
    {
        $fields = $config->getFields();
        foreach ($fields as $name => $field) {
            if ($field->getType() instanceof JsonType){
                if (!empty($data[$name])) {
                    $val = $data[$name];
                    if(is_array($val)){
                        $val = json_encode($data[$name], JSON_THROW_ON_ERROR);
                    }
                    $data[$name] = $val;
                }
            }

            if ($field->getType() instanceof DateTimeType) {
                if (!empty($data[$name])) {
                    /**
                     * @var DateTime $val
                     */
                    $val = $data[$name];
                    if($val instanceof DateTime){
                        $val = $val->format($this->dateTimeFormat);
                    }
                    $data[$name] = $val;
                }
            }

            if ($field->getType() instanceof DateType) {
                if (!empty($data[$name])) {
                    /**
                     * @var DateTime $val
                     */
                    $val = $data[$name];
                    if($val instanceof DateTime){
                        $val = $val->format($this->dateFormat);
                    }
                    $data[$name] = $val;
                }
            }
        }
        return $data;
    }
}