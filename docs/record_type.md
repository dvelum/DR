# Использование вложенных Data Record

[<< документация](readme.md)

```php
<?php

use Dvelum\DR\Type\RecordType;
use Dvelum\DR\Factory;
use Dvelum\DR\Record;

// регистрируем Data Record, который содержит вложенные Record
$recordRegistry = [
  // Record "Событие"
  'Event' => static function(){
     return [
        'fields' => [
           'date' => ['type' => 'datetime', 'required' => true],
           'planner' =>[
              'required' => true,
              'type' => RecordType::class, // указываем тип вложенного Record
              'recordName' => 'Planner' // название вложенного Record
           ]
        ]
     ];
  },
  // Record "Организатор"
  'Planner' => static function(){
     return [
        'fields' => [
           'name' => ['type' => 'string', 'required' => true],
           'age' => ['type' => 'int', 'required' => true, 'minValue' => 18]
        ]
     ]
  }
];

// инстанцируем фабрику Data Record
$factory = new Factory($recordRegistry);
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

if($object->get('planer') instanceof Record){
  // все работает
}
```