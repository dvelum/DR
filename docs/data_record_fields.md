# Настройки полей Data Record

[<< документация](readme.md)

**type** - тип данных
* int
* float
* bool
* string
* json (принимает на вход строку json или массив)
* date
* datetime
* record - вложенный объект
* enum - один из списка (значения приводятся к строке)
* list* - вариант из списка, отличается от enum, тем что в настройках задаются пары ключ => описание, 
  хранит только ключ в виде строки, возможен множественный выбор тогда результат это массив, хранит только ключи, описание можно достать отдельно

### Общие настройки для всех типов

* **required** - признак обязательности заполнения

* **default** - значение по умолчанию

* **defaultValueAdapter** - адаптер для сложного значения по умолчанию, должен реализовать Dvelum\DR\DefaultValue\DefaultValueInterface, передается имя класса

* **notNull** - не может быть null, указывается bool

* **label** - описание поля понятное для пользователя

* **validator** - адптер валидации значения, передается имя класса не используется интерфейс (для гибкости).

Класс должен реализовывать метод:
```php
    /**
      * @param mixed $value
      */
    public function validate($value) : bool;
```
## Специфические настройки для разных типов
### int
* **minValue** - минимальное значение для int, float, date, datetime (для дат может принимать объект DateTime)

* **maxValue** - максимальное значение для int, float date, datetime для дат может принимать объект DateTime)

### float
* **minValue** - минимальное значение для int, float, date, datetime (для дат может принимать объект DateTime)

* **maxValue** - максимальное значение для int, float date, datetime для дат может принимать объект DateTime)

### string
* **minLength** - минимальная длина строки в символах utf-8  для полей типа string

* **maxLength** - максимальная длина строки в символах utf-8  для полей типа string

* **encoding** - кодировка строки для проверки  minLength, maxLength, по умолчанию utf-8

### date
* **minValue** - минимальное значение для int, float, date, datetime (для дат может принимать объект DateTime)

* **maxValue** - максимальное значение для int, float date, datetime для дат может принимать объект DateTime)

### datetime
* **minValue** - минимальное значение для int, float, date, datetime (для дат может принимать объект DateTime)

* **maxValue** - максимальное значение для int, float date, datetime для дат может принимать объект DateTime)

###  record 
* **recordName** - Alias имени вложенного объекта (как он указан в реестре Records), указывается для типа DataRecord

* **factory** - Alias имени порождающей фабрики для передачи в сложные пользовательские типы данных (для DataRecord по умолчанию DataRecordFactory)

###  enum
* **values** - список допустимых значений в виде массива ['val1', 'val2', ...]

### list
* **multiple** - можно задавать несколько значений
  
* **values** - список допустимых значений в виде массива ['key1' =>'label1', 'key2'=>'label2, ...]






