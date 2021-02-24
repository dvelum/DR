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

namespace Dvelum\DR\Config;

use Dvelum\DR\Type\TypeInterface;
use InvalidArgumentException;

class Field
{
    /**
     * @var array<string,array> $data
     */
    private array $data;
    private string $name;
    private TypeInterface $type;

    /**
     * Field constructor.
     * @param string $name
     * @param array<string,mixed> $data
     */
    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->data = $data;
        $this->type = $data['type'];
    }

    /**
     * Get field configuration
     * @return array<string,mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return TypeInterface
     */
    public function getType(): TypeInterface
    {
        return $this->type;
    }

    /**
     *  Проверить поле на обязательность
     * @return bool
     */
    public function isRequired(): bool
    {
        if (isset($this->data['required']) && $this->data['required']) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function hasDefault(): bool
    {
        return array_key_exists('default', $this->data) || isset($this->data['defaultValueAdapter']);
    }

    /**
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getDefault()
    {
        if (isset($this->data['defaultValueAdapter'])) {
            $default = (new $this->data['defaultValueAdapter'])->getValue();
        } elseif (array_key_exists('default', $this->data)) {
            $default = $this->data['default'];
        } else {
            throw new InvalidArgumentException('Default value for field ' . $this->getName() . ' is not set');
        }
        return $default;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        if (isset($this->data['notNull']) && $this->data['notNull']) {
            return false;
        }
        return true;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool
    {
        if (isset($this->data['validator'])) {
            /**
             * @var Object $validator
             */
            $validator = $this->data['validator'];
            if (method_exists($validator, 'validate')) {
                if (!$validator->validate($value)) {
                    return false;
                }
            }
        }
        return $this->getType()->validateValue($this->data, $value);
    }
}
