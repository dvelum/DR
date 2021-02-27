<?php
$loader = include 'vendor/autoload.php';
$loader->setPsr4("DTO\\", __DIR__ );
$data = include __DIR__.'/data.php';

$s = microtime(true);
$m = memory_get_usage();

foreach ($data as $k=>$v){
    $user = new \DTO\DtoClass();
    $user->setName($v['name']);
    $user->setAddress($v['address']);
    $user->setAge($v['age']);
    $user->setCount($v['count']);
    $user->setPrice($v['price']);
    $data[$k] = $user;
}

echo (microtime(true) - $s) . 's. '.PHP_EOL;
echo ((memory_get_usage()- $m) / 1024 / 1024) . 'Mb. '.PHP_EOL;