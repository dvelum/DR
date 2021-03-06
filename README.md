[![PHP Version](https://img.shields.io/badge/php-7.4%2B-blue.svg)](https://packagist.org/packages/dvelum/dr)
[![Total Downloads](https://img.shields.io/packagist/dt/dvelum/dr.svg?style=flat-square)](https://packagist.org/packages/dvelum/dr)
[![Build and Test](https://github.com/dvelum/DR/actions/workflows/build_and_test.yml/badge.svg?branch=main)](https://github.com/dvelum/DR/actions/workflows/build_and_test.yml)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/19085497d8fa41689b0c7da4bb1318be)](https://www.codacy.com/gh/dvelum/DR/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=dvelum/DR&amp;utm_campaign=Badge_Grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/19085497d8fa41689b0c7da4bb1318be)](https://www.codacy.com/gh/dvelum/DR/dashboard?utm_source=github.com&utm_medium=referral&utm_content=dvelum/DR&utm_campaign=Badge_Coverage)

# Data Record - Инструмент для создания и валидации структур данных

Позволяет создавать и валидировать структуры данных, без привязки к БД / ActiveRecord / ORM. 

Можно использовать как замену DTO.

Можно использовать как замену валидатора Active Record или ORM в проектах, где нет желания использовать
массивные ORM.

Конечную структуру можно экспортировать в массив, далее сохранить в БД.

Кроме стандартных типов данных и валидаторов, позволяет регистрировать свои.

[Документация](docs/readme.md)

## Преимущества
* не нужно создавать обилие классов под каждый тип, заполнять их полями, геттерами и сеттерами
* экономит время на разработку, количество кода в рантайме
* стандартные проверки полей типа minVal, maxVal, maxLength, defaultValue, isNullable делаются одной настройкой в файле конфигурации
* есть возможность проверить всели ли required поля заполнены
* есть слежение за состоянием изменения (получить список полей которые изменились)
* можно создавать собственные (custom) типы данных (достаточно просто)
* использует ленивую загрузку, экономит оперативную память
* значительно меньшее потребление памяти относительно DTO
* автоматическая конвертация типов, например DateTime 

```php 
$record->set('dateTimeField', '2021-01-01 00:00:00');
/**
 * @var \DateTime $result
 */
$result = $record->get('dateTimeField');
```


## Установка

`composer require dvelum/dr`

## Упрощенный пример использования

Файл настроек реестра Records registry.php
```php
<?php
use Dvelum\DR\Type\StringType;
use Dvelum\DR\Type\DateTimeType;
use Dvelum\DR\Record\DefaultValue\CurrentDateTimeString;
$registry =  [
   'records'=> [
        'ClientData' => [
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
                    'defaultValueAdapter' => CurrentDateTimeString::class
                ]
            ]
        ]
    ]
];
```
[Подробный пример настроек](docs/registry_example.md)

Использование: 

```php
<?php
use Dvelum\DR\Factory;

// получаем данные POST из запроса
$params = $psr7Request->getParsedBody();
// получаем настройки реестра
$registry = include 'registry.php';
// создаем фабрику DR
$factory =  new Factory($registry);

//=== Пример 1 ================================
$record = $factory->create('ClientData');

try{
  $record->setData($params);
}catch(\InvalidArgumentException $e){
  // переданы невалидные данные
}

// можно проверить все ли обязательные поля переданы
if(!$record->validateRequired()->isSuccess()){
  // не все обязательные поля заданы
}


//=== Пример 2 ================================

// получаем id записи из параметров запроса
$id = $psr7Request->getQueryParams()['id'];
/**
 * Загружаем данные из нашего хранилища
 * @var array $clientData 
 */
$clientData = $someStorage->load($id);
$record = $factory->create('ClientData');
// помещаем данные из хранилища в структуру
$record->setData($clientData);
// помечаем изменения как принятые
$record->commitChanges();

try{
 // сетим данные из запроса
  $record->setData($params);
}catch(\InvalidArgumentException $e){
  // переданы невалидные данные
}

// Для удобства экспорта данных в хранилище, например в БД, есть подготовленный класс экспорта, 
// который конвертирует поля в нужный вид (например json поле в строку, DateTime в строку формата 'Y-m-d H:i:s')
$export = new \Dvelum\DR\Export\Database();
// получить все данные
$data = $export->exportRecord($record);
// или получить только обновления
$data = $export->exportUpdates($record);

// Export можно получить и из объекта $factory->getExport('ExportAlias'), тогда его нужно будет зарегистрировать при создании $factory
// и получить командой:
/**
 * @var ExportInterface $export
 */
$export = $factory->getExport('Database');
```

Отсутствие подсветки полей в IDE можно компенсировать UI пакетом [dvelum\dr-ui](https://github.com/dvelum/DR-ui)

Экспериментальный пакет, позволяет просматривать ваши структуры данных.

Пока доступен только просмотр, возможно будет добавлено и редактирование.

Запускается одной командой на локальном хосте linux/macos

![](docs/screen2.png)
![](docs/screen1.png)

## Производительность

Сравнение DataRecord с DTO, тест на заполнение 50,000 объектов. 

PHP 7.4, xdebug отключен, cli opcache включен.


`php test/tests/performance/create.php` - создать рандомный датасет

`php test/tests/performance/dto.php`  - заполнение DTO

`php test/tests/performance/record.php` - заполнение Record

`php test/tests/performance/record.php -r 1` - заполнение Record при помощи setRawData

|                     | Время, c       | RAM, mb      |
|:----------|----------:|----------:|
| DTO             | 0.025           |  13.177        |
| DR                | 0.146          |  2.016           |
| DR raw         | 0.036           |  2.016          |


Data Record потребляет значительно меньше (6.5 раз) оперативной памяти за счет оптимизаций, но по времени выполнения естественно уступает нативным иструментам (5.5 раз), так как валидаций больше и выполняются они в коде. 

Для повышения производительности заполнения Data Record есть метод setRawData, который заполняет Record без валидаций. Этим методом можно воспользоваться когда данные приходят из бд и мы им доверяем (уверенны, что они провалидированы перед записью). В этом случае разница в скорости выполнения минимальна. 

Валидации "из коробки" больше, кода писать нужно значительно меньше, вполне конкурентное решение имеющее право на жизнь.

