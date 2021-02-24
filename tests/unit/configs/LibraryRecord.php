<?php

return [
    'fields' => [
        'title' => [
            'type' => 'string',
            'required' => true,
        ],
        'user' => [
            'type' => \Dvelum\DR\Type\RecordType::class,
            'recordName' =>'UserRecord',
            'required' => true,
        ]
    ]
];


