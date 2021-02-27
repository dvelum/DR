<?php
include 'vendor/autoload.php';
$data = include __DIR__.'/data.php';

$s = microtime(true);
$m = memory_get_usage();

$str = [
    'records' => [
        'user' => static function(){
            return [
                'fields' => [
                    'name' => ['type'=>'string', 'required'=>true],
                    'address'=> ['type'=>'string', 'required'=>true],
                    'count'=> ['type'=>'int', 'required'=>true,'minValue'=>0, 'maxValue'=>100],
                    'age'=>['type'=>'int', 'required'=>true,'minValue'=>18, 'maxValue'=>20],
                    'price'=> ['type'=>'int', 'required'=>true,'minValue'=>100, 'maxValue'=>10000],
                    // empty fields emulate not required fields
                    'field1' => ['type' => 'string'],
                    'field2' => ['type' => 'string'],
                    'field3' => ['type' => 'string'],
                    'field4' => ['type' => 'int'],
                    'field5' => ['type' => 'int'],
                ]
            ];
        }
    ]
];
$factory = \Dvelum\DR\Factory::fromArray($str);
$isRaw =$argv[2] ?? false;
if($isRaw){
    foreach ($data as $k=>$v){
        $user = $factory->create('user');
        $data[$k] = $user->setRawData($v);
    }
}else{
    foreach ($data as $k=>$v){
        $user = $factory->create('user');
        $data[$k] = $user->setData($v);
    }
}


echo (microtime(true) - $s) . 's. '.PHP_EOL;
echo ((memory_get_usage()- $m) / 1024 / 1024) . 'Mb. '.PHP_EOL;