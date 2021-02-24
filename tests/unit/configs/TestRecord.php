<?php

use Dvelum\DR\DefaultValue\CurrentDateTimeString;

return [
    'fields' => [
        'int_field' => [
            'type' => 'int'
        ],
        'float_field' => [
            'type' => 'float'
        ],
        'bool_field' => [
            'type' => 'bool'
        ],
        'string_field' => [
            'type' => 'string',
            'encoding'=>'utf-8'
        ],
        'string_field_date' => [
            'type' => 'string',
            'defaultValueAdapter' =>CurrentDateTimeString::class
        ],
        'string_feld_required' => [
            'type' => 'string',
            'required' => true
        ],
        'string_field_limit' => [
            'type' => 'string',
            'required' => true,
            'maxLength' => 5,
            'minLength' => 3
        ],
        'string_field_email' => [
            'type' => 'string',
            'required' => true,
            'validator' => \Dvelum\DR\UnitTest\Validator::class
        ],
        'int_field_limit'=>[
            'type' => 'int',
            'minValue'=>1,
            'maxValue' =>10
        ],
        'float_field_limit'=>[
            'type' => 'float',
            'minValue'=>1,
            'maxValue' =>10
        ],
        'enum'=>[
            'type' => 'enum',
            'values' => [1, 'first', 'second' ,'last', 15, 2.5]
        ],
        'json_field' => [
            'type' => 'json'
        ],
        'string_default' => [
            'type' => 'string',
            'default' => 'default'
        ],
        'datetime_default' => [
            'type' => 'datetime',
            'default' => '2021-01-01 00:00:00'
        ],
        'datetime' => [
            'type' => 'datetime',
            'default' => null
        ],
        'datetime_min' => [
            'type' => 'datetime',
            'default' => '2021-01-01',
            'minValue'=>'2021-01-01',
        ],
        'datetime_max' => [
            'type' => 'datetime',
            'default' => null,
            'maxValue'=>'2021-01-01 12:00:00',
        ],
        'date' => [
            'type' => 'date',
            'default' => null
        ],

    ]
];