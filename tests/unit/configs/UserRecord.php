<?php

return [
    'fields' => [
        'name' => [
            'type' => 'string',
            'required' => true,
        ],
        'age' => [
            'type' => 'int',
            'required' => true,
            'minValue' => 18
        ]
    ]
];