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

namespace Dvelum\DR;

use Dvelum\DR\Config\Field;
use InvalidArgumentException;

class Record
{
    protected Config $config;
    /**
     * @var array <string,mixed>
     */
    protected array $data = [];
    /**
     * @var array <string,mixed>
     */
    protected array $updates = [];
    private string $name;

    /**
     * performance cache
     * @var array<string,Field>
     */
    private array $fieldsCache;

    public function __construct(string $name, Config $config)
    {
        $this->name = $name;
        $this->config = $config;
        $this->fieldsCache = $config->getFields();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Set record data, rewrite and init defaults
     * @param array <string,mixed>$data
     * @return void
     * @throws InvalidArgumentException
     */
    public function setData(array $data): void
    {
        $fields = $this->config->getFields();
        foreach ($fields as $name => $field) {
            if (isset($data[$name])) {
                $this->set($name, $data[$name]);
            }
        }
    }

    /**
     *  Set values from array without validation.
     *  Use carefully with a full understanding of how it works.
     *  Types will not be converted and values will be returned as is.
     *  Can be used when data is obtained from a trusted source and validated format
     *  to get highest performance. No need in CommitChanges.
     * @param array<string,mixed> $data
     * @return void
     */
    public function setRawData(array $data) : void
    {
        foreach ($data as $key => $value){
            if(isset($this->fieldsCache[$key])){
                $this->data[$key] = $value;
            }
        }
        $this->data = $data;
    }

    /**
     * @param string $fieldName
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function set(string $fieldName, $value): void
    {
        if (!isset($this->fieldsCache[$fieldName])) {
            throw new InvalidArgumentException('Undefined field: ' . $fieldName);
        }

        $field = $this->fieldsCache[$fieldName];
        $type  = $field->getType();

        if($value === null && !$field->isNullable()){
            throw new InvalidArgumentException('Invalid data type for  field: ' . $fieldName.', can not be null');
        }

        $fieldData = $field->getData();
        // validate not nul values type (any field can be null)
        if ($value!==null && !$type->validateType($fieldData, $value)) {
            throw new InvalidArgumentException('Invalid data type for  field: ' . $fieldName);
        }

        // covert value type into required by configuration
        if($value!==null){
            try{
                $value = $type->applyType($fieldData, $value);
            }catch (\Throwable $e){
                throw new InvalidArgumentException('Invalid value for field '.$fieldName.' '.$e->getMessage());
            }
        }

        if ($value!==null) {
            if(!$type->validateValue($fieldData, $value)){
                throw new InvalidArgumentException('Invalid value for field: ' . $fieldName);
            }
            if($field->hasValidator() && !$field->validate($value)){
                throw new InvalidArgumentException('Invalid value for field: ' . $fieldName);
            }
        }

        // check is it real value update
        // performance patch
        if($value !==null){
            if(!isset($this->updates[$fieldName]) && isset($this->data[$fieldName]) && $this->data[$fieldName]===$value){
                return;
            }
        }else{
            if(!array_key_exists($fieldName, $this->updates) && array_key_exists($fieldName, $this->data) && $this->data[$fieldName]===$value){
                return;
            }
        }

        $this->updates[$fieldName] = $value;
    }

    /**
     *  Commit updates
     */
    public function commitChanges(): void
    {
        foreach ($this->updates as $field => $value) {
            $this->data[$field] = $value;
        }
        $this->updates = [];
    }

    /**
     *  Check for updates
     * @return bool
     */
    public function hasUpdates(): bool
    {
        return !empty($this->updates);
    }

    /**
     *  Get record updates
     * @return array<string, mixed>
     */
    public function getUpdates(): array
    {
        // init default values
        $fields = $this->config->getFields();
        foreach ($fields as $fieldName => $field) {
            if ($field->hasDefault() && !array_key_exists($fieldName, $this->updates) && !array_key_exists($fieldName, $this->data)) {
                $this->setDefault($fieldName);
            }
        }
        return $this->updates;
    }

    /**
     * @param string $fieldName
     * @throws InvalidArgumentException
     */
    public function setDefault(string $fieldName): void
    {
        $value = $this->config->getField($fieldName)->getDefault();
        $this->set($fieldName, $value);
    }

    /**
     * @param string $fieldName
     * @return  mixed
     * @throws  InvalidArgumentException
     */
    public function get(string $fieldName)
    {
        if (!$this->config->fieldExists($fieldName)) {
            throw new InvalidArgumentException('Undefined field ' . $fieldName);
        }

        if (isset($this->updates[$fieldName])) {
            return $this->updates[$fieldName];
        }

        if (isset($this->data[$fieldName])) {
            return $this->data[$fieldName];
        }

        $field = $this->config->getField($fieldName);

        if ($field->hasDefault()) {
            $this->setDefault($fieldName);
            return $this->updates[$fieldName];
        }
        throw new InvalidArgumentException('Trying to get undefined value for field: ' . $fieldName);
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return array_merge($this->data, $this->getUpdates());
    }

    /**
     * @return ValidationResult
     */
    public function validateRequired(): ValidationResult
    {
        $data = $this->getData();
        $fields = $this->config->getFields();
        $errors = [];
        $success = true;
        foreach ($fields as $name => $field) {
            if ($field->isRequired() && !array_key_exists($name, $data)) {
                $errors[$name] = 'Missing value for required  field: ' . $name;
            }
        }
        if (!empty($errors)) {
            $success = false;
        }
        return new ValidationResult($success, $errors);
    }
}