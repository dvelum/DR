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

use Dvelum\DR\Export\ExportInterface;
use Dvelum\DR\Type\EnumType;
use Dvelum\DR\Type\RecordType;
use Dvelum\DR\Type\TypeInterface;
use InvalidArgumentException;
use RuntimeException;


class Factory
{
    /**
     * @var array<string,callable|Config> $registeredRecords
     */
    protected array $registeredRecords;
    /**
     * @var array <string, string|TypeInterface>
     */
    protected array $registeredTypes;
    /**
     * @var array<string, string|ExportInterface>
     */
    protected array $registeredExports;
    /**
     * @var array<string, string|Object>
     */
    protected array $registeredValidators;
    /**
     * @var array<string,string|object>
     */
    protected array $registeredFactories;
    /**
     * @var string
     */
    private string $selfAlias = 'DataRecordFactory';

    /**
     *  Create record factory from configuration array
     * @see https://github.com/dvelum/DR/blob/main/docs/registry_example.md
     * @param array <string,array>$registry
     * @return Factory
     */
    public static function fromArray(array $registry): Factory
    {
        $records = $registry['records'] ?? null;
        if ($records === null) {
            throw new InvalidArgumentException('No records in registry');
        }
        $factory = new self();
        // Register user Records
        foreach ($records as $name => $configLoader) {
            $factory->registerRecord($name, $configLoader);
        }

        // Register exports
        $exports = $registry['exports'] ?? null;
        if ($exports !== null) {
            foreach ($exports as $alias => $class) {
                $factory->registerExport($alias, $class);
            }
        }
        $types = $registry['types'] ?? null;
        // Register user Types
        if ($types !== null) {
            foreach ($types as $alias => $class) {
                $factory->registerDataType($alias, $class);
            }
        }

        $factories = $registry['factories'] ?? null;
        // register custom factories
        if ($factories !== null) {
            foreach ($factories as $alias => $class) {
                $factory->registerFactory($alias, $class);
            }
        }

        return $factory;
    }

    /**
     * Simple wrapper for fromArray method
     * @param array $records
     * @return Factory
     */
    public static function fromRecordsArray(array $records): Factory
    {
        return self::fromArray(['records' => $records]);
    }

    public function __construct()
    {
        // Register STD data types
        $stdType = DataType::ALIASES;
        foreach ($stdType as $alias => $class) {
            $this->registerDataType($alias, $class);
        }
        // self registration
        $this->registerFactory($this->selfAlias, $this);
    }

    /**
     * @param string $name
     * @param callable $configLoader - return configuration array, lazy loading
     */
    public function registerRecord(string $name, callable $configLoader): void
    {
        $this->registeredRecords[$name] = $configLoader;
    }

    /**
     *  Register custom data type
     * @param string $alias
     * @param string $className - instance of DataTypeInterface (lazy loading checks)
     */
    public function registerDataType(string $alias, string $className): void
    {
        $this->registeredTypes[$alias] = $className;
    }

    /**
     * Register custom data export
     * @param string $alias
     * @param string $className - instance of ExportInterface (lazy loading checks)
     */
    public function registerExport(string $alias, string $className): void
    {
        $this->registeredExports[$alias] = $className;
    }

    /**
     * Register custom factory for complex data types
     * @param string $alias
     * @param string|object $factory
     */
    public function registerFactory(string $alias, $factory): void
    {
        $this->registeredFactories[$alias] = $factory;
    }

    /**
     * @param string $recordName
     * @return Record
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function create(string $recordName): Record
    {
        if (!isset($this->registeredRecords[$recordName])) {
            throw new InvalidArgumentException('Undefined Data Record ' . $recordName);
        }

        $config = $this->getRecordConfig($recordName);

        return new Record($recordName, $config);
    }

    /**
     * @param string $recordName
     * @return Config
     * @throws RuntimeException
     */
    public function getRecordConfig(string $recordName): Config
    {
        if ($this->registeredRecords[$recordName] instanceof Config) {
            return $this->registeredRecords[$recordName];
        }

        if (!is_callable($this->registeredRecords[$recordName])) {
            throw new RuntimeException('Wrong config loader for Record :' . $recordName . ', expects callable');
        }

        $config = $this->registeredRecords[$recordName]();
        if (!is_array($config)) {
            throw new RuntimeException('Wrong config from loader for Record :' . $recordName . ', expects array');
        }

        foreach ($config['fields'] as $fieldName => &$fieldConfig) {
            if (!isset($fieldConfig['type'])) {
                throw new RuntimeException(
                    'Undefined field  type  for Record :' . $recordName . ',  field:' . $fieldName
                );
            }
            // inject type
            $fieldConfig['type'] = $this->getType($fieldConfig['type']);

            // inject validator
            if (isset($fieldConfig['validator'])) {
                $fieldConfig['validator'] = $this->getValidator($fieldConfig['validator']);
            }

            if ($fieldConfig['type'] instanceof EnumType) {
                $fieldConfig['values'] = array_flip(array_map('strval', $fieldConfig['values']));
            }

            // register default factory for RecordType
            if ($fieldConfig['type'] instanceof RecordType && (!isset($fieldConfig['factory']) || empty($fieldConfig['factory']))) {
                $fieldConfig['factory'] = $this->selfAlias;
            }

            // inject factory
            if (isset($fieldConfig['factory']) && !empty($fieldConfig['factory'])) {
                $fieldConfig['factory'] = $this->getFactory($fieldConfig['factory']);
            }
        }
        unset($fieldConfig);

        $this->registeredRecords[$recordName] = new Config($config);

        return $this->registeredRecords[$recordName];
    }

    /**
     * Get type Object
     * @param string $typeAlias
     * @return TypeInterface
     */
    public function getType(string $typeAlias): TypeInterface
    {
        if (!isset($this->registeredTypes[$typeAlias])) {
            // if type passed into configuration without registering
            $type = null;
            if (class_exists($typeAlias)) {
                $type = new $typeAlias();
                if (!$type instanceof TypeInterface) {
                    $type = null;
                }
            }
            if ($type === null) {
                throw new RuntimeException('Undefined field type "' . $typeAlias);
            }
            $this->registeredTypes[$typeAlias] = $type;
        }
        if (is_string($this->registeredTypes[$typeAlias])) {
            $this->registeredTypes[$typeAlias] = new $this->registeredTypes[$typeAlias];
        }
        return $this->registeredTypes[$typeAlias];
    }

    /**
     * @param string $alias
     * @return object
     * @throws InvalidArgumentException
     */
    public function getValidator(string $alias): object
    {
        if (!isset($this->registeredValidators[$alias])) {
            $this->registeredValidators[$alias] = $alias;
        }

        if (is_string($this->registeredValidators[$alias])) {
            /**
             * @var Object $validator
             */
            $validator = new $alias;
            if (!method_exists($validator, 'validate')) {
                throw new InvalidArgumentException(
                    'Invalid validator class ' . $alias . ' should implement validate($value) method'
                );
            }
            $this->registeredValidators[$alias] = $validator;
        }
        return $this->registeredValidators[$alias];
    }

    /**
     * @param string $name
     * @return Object
     */
    public function getFactory(string $name): object
    {
        if (!isset($this->registeredFactories[$name])) {
            throw new InvalidArgumentException('Undefined Data Factory  ' . $name);
        }

        if (is_string($this->registeredFactories[$name])) {
            $this->registeredFactories[$name] = new $this->registeredFactories[$name]();
        }

        return $this->registeredFactories[$name];
    }

    /**
     * @param string $name
     * @return ExportInterface
     */
    public function getExport(string $name): ExportInterface
    {
        if (!isset($this->registeredExports[$name])) {
            throw new InvalidArgumentException('Undefined Data Export  ' . $name);
        }
        if (is_string($this->registeredExports[$name])) {
            $this->registeredExports[$name] = new $this->registeredExports[$name]();
        }
        return $this->registeredExports[$name];
    }
}
