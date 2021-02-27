<?php
$size = 50000;
$data = [];
function RandomString($len)
{
    $characters = ' abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < $len; $i++) {
        $randstring.= $characters[rand(0, mb_strlen($characters)-1)];
    }
    return $randstring;
}
for ($i=0; $i<$size; $i++){
    $data[] = [
        'name' => RandomString(7),
        'address' => RandomString(20),
        'count' => rand(0,100),
        'age' => rand(18,20),
        'price' => rand(100, 10000)
    ];
}

file_put_contents(__DIR__.'/data.php',  '<?php return '.var_export($data, true).';');