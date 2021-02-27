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

namespace Dvelum\DR\Type;

final class StringType implements TypeInterface
{
    /**
     * @inheritDoc
     */
    public function validateValue(array $fieldConfig, $value): bool
    {
        if(!isset($fieldConfig['minLength']) && !isset( $fieldConfig['maxLength'])){
            return true;
        }

        $encoding = 'utf-8';
        if(isset($fieldConfig['encoding'])){
            $encoding = $fieldConfig['encoding'];
        }

        $length = mb_strlen($value, $encoding);

        if (isset($fieldConfig['minLength']) && $length < $fieldConfig['minLength']) {
            return false;
        }
        if (isset($fieldConfig['maxLength']) && $length > $fieldConfig['maxLength']) {
            return false;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function applyType(array $fieldConfig, $value)
    {
        return  (string) $value;
    }

    /**
     * @inheritDoc
     */
    public function validateType(array $fieldConfig, $value): bool
    {
        // performance patch
        if(is_string($value)){
            return true;
        }

        if (is_array($value)) {
            return false;
        }
        return true;
    }
}

