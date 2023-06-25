<?php
return [
    'adminPanel' => [
        'type' => 2,
        'description' => 'Админ панель',
    ],
    'event_organizer' => [
        'type' => 1,
        'description' => 'Event Organizer',
        'ruleName' => 'userRole',
        'children' => [
            'adminPanel',
        ],
    ],
    'event_processor' => [
        'type' => 1,
        'description' => 'Event Processor',
        'ruleName' => 'userRole',
        'children' => [
            'adminPanel',
        ],
    ],
    'user' => [
        'type' => 1,
        'description' => 'User',
        'ruleName' => 'userRole',
        'children' => [
            'adminPanel',
        ],
    ],
    'admin' => [
        'type' => 1,
        'description' => 'Administrator',
        'ruleName' => 'userRole',
        'children' => [
            'user',
        ],
    ],
];
