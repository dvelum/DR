# Регистрация своего типа данных
[<< документация](README.md)

Для регистрации собственного типа данных нужно:
* создать свой класс реализующий TypeInterface
* зарегистрировать тип в Factory (3 аргумент)
```
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

$factory = Factory($recordRegistry,$exportRegistry,$customTypes);