Настройки полей Data Record
===
[<< документация](readme.md)

**type** - тип данных
* int
* float
* bool
* string
* json (принимает на вход строку json или массив)
* date
* datetime

**required** - признак обязательности заполнения

**minValue** - минимальное значение для int, float, date, datetime (для дат может принимать объект DateTime)

**maxValue** - максимальное значение для int, float date, datetime для дат может принимать объект DateTime)

**minLength** - минимальная длина строки в символах utf-8  для полей типа string

**maxLength** - максимальная длина строки в символах utf-8  для полей типа string

**encoding** - кодировка строки для проверки  minLength, maxLength, по умолчанию utf-8

**default** - значение по умолчанию

**defaultValueAdapter** - адаптер для сложного значения по умолчанию, должен реализовать Dvelum\DR\DefaultValue\DefaultValueInterface, передается имя класса

**notNull** - не может быть null

**validator** - адптер валидации значения, передается имя класса не используется интерфейс (для гибкости). 
Класс должен реализовывать метод:
```php
    /**
      * @param mixed $value
      */
    public function validate($value) : bool;
```