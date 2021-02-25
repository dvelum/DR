# Пример конфигурации реестра Data Record 

```php 
<?php
use Dvelum\DR\Type\StringType;
use Dvelum\DR\Type\DateTimeType;
use Dvelum\DR\Record\DefaultValue\CurrentDateTimeString;
return [
    // список Record
    'records' =>  [
          // лучше делать ленивую загрузку конфигов и каждый объект хранить в отдельном конфигурационном файле типа:
          'MyRecordName' => static function(): array{  return  inclue 'myRecord.php';  } 
          // для упрощения
          'User' => static function(){
             return [
                'fields' => [
                   'firstName' => [
                        'type' => 'string', // можно StringType::class
                        'minLength' => 2,
                        'required' => true,
                    ],
                    'age' => [
                        'type' => 'int',
                        'minValue' => 18,
                        'default' => 18,
                    ],
                    'date' => [
                        'type' => DateTimeType::class,
                        'minValue' => '2021-01-01',
                        'notNull' => true,
                        'defaultValueAdapter' => CurrentDateTimeString::class
                    ],
                    'active' => [
                        'type' => 'bool'
                    ]
                    'status' => [
                        'type' => 'enum',
                        'values' => ['new', 'gold', 'premium']
                    ],
                    'options' => [
                        'type' => 'json',
                        'required' => true
                    ]
                ]
             ];
          },
    ],
    // Адаптеры экспорта данных
    'exports' => [
        'Database' => Dvelum\DR\Export\Database::class
    ],
    // Пользовательские типы данных
    'types' => [
        'myCustomType' => CustomType::class
    ],
    // Пользовательские фабрики объектов, для сложных типов данных
    'factories' => [
        'myCustomFactory' => CustomFactory::class
    ]
];
```