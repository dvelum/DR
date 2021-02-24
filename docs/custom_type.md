# Регистрация своего типа данных
[<< документация](readme.md)

### Пример создания пользовательского типа данных

Для регистрации собственного типа данных нужно:
* создать свой класс реализующий TypeInterface
* зарегистрировать тип в Factory (3 аргумент)
```php
<?php
use Dvelum\DR\Factory;
use  Dvelum\DR\Export\Database;
// регистрируем наш тип
$customTypes = [
    'MyType' => MyType::class
];

// подключаем экспорт
$exportRgistry = [
    'Database' => Database::class
];

$factory = Factory($recordRegistry, $exportRegistry, $customTypes);
```


## Пример создания сложного пользовательского типа данных

Представим у нас есть класс:

```php
<?php
// Пример умышленно упрощен, такие данные можно хранить во вложенном типе RecordType
class User
{
    private string $name;
    private int $age
    public function __construct(){}
    public function getName():string;
    public function setName(string $name);
    public function getAge():int;
    public function setAge(int $name);
}

```
Мы хотим чтобы Data Record по ключу planer содержал экземпляр этого объекта.

Мы получаем данные в виде массива:

 ```['name' => 'Иван', 'age' => 18]```

Зарегистрируем пользовательский тип, который умеет конвертироваться из массива в объект определенного типа.

1. Нужно создать фабрику для такого объекта.

```php
<?php
class UserFactory
{
    public funciton createUser(array $data):User
    {
      $user = new User();
      $user->setName($data['name']);
      $user->setAge($data['age']);
      return $user;
    }
}

```

2. Нужно реализовать интерфейс для типа данных.

```php
<?php

use Dvelum\DR\Factory;
use Dvelum\DR\Record;

final class UserType implements TypeInterface
{
    /**
     * Метод проверки, может ли тип быть конвертирован
     */
    public function validateType(array $fieldConfig, $value): bool
    {
        // умеем конвертировать массив
        if(is_array($value)){
            return true;
        }
        // принимаем объект User
        if($value instanceof User){
            return true;
        }

        return false;
    }

    /**
     * Метод используемый для конвертации типа
     */
    public function applyType(array $fieldConfig, $value)
    {
        if(is_array($value)){
            /**
             * Экземпляр вашей фабрики будет передан в $fieldConfig['factory']
             * @var UserFactory $factory
             */
            $factory = $fieldConfig['factory'];
            return $factory->createUser($value);
        }

       return $value;
    }

    /**
     * Метод для валидации данных на уровне настроек поля, например minValue
     * @inheritDoc
     */
    public function validateValue(array $fieldConfig, $value): bool
    {
        // можно организовать свою проверку, по любым настройкам поля
        // настройки можно добавлять свои
    }
}

```

3. Регистрируем наш тип в фабрике

В примере рассмотрим придуманный Data Record с названием "Событие" Event, cо вложенным объектом "Пользователь" User

```php
<?php
// регистрируем Data Record, который содержит наш тип
$recordRegistry = [
  'Event' => static function(){
     return [
        'fields' => [
           'date' => ['type'=>'datetime', 'required'=>true],
           // наше сложное поле
		   'planner' =>[
              'type' => UserType::class, // можно писать имя класса или имя алиаса 'UserTypeAlias'
              'required' => true,
               // алиас для нашей фабрики пользователей, любая строка
              'factory' => 'UserFactoryAlias'
           ]
        ]
     ];
  }
];
// Регистрируем пользовательский тип 
$typeRegistry = [
  // алиас           имя класса реализующего тип
  'UserTypeAlias' => UserType::class
];
// регистрируем пользовательсую фабрику объектов
$factoryRegistry =[
  'UserFactoryAlias' => UserFactory::class
  // или
  'UserFactoryAlias' => new UserFactory()
];

// инстанцируем фабрику Data Record
$factory = new \Dvelum\DR\Factory($recordRegistry, null, $typeRegistry, $factoryRegistry);

// начинаем использовать

$requestData = [
  'date'=>'2021-01-01 00:00:00', 
  'planer'=>[
    'name' =>'Tony', 
    'age'=>18
  ]
];

$object = $factory->create('Event');
$object->setData($requestData);

if($object->get('planer') instanceof User){
  // все работает
}
```

Необходимо обратить внимание, что для такой структуры нужно будет написать собственный экспорт данных.







