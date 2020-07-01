<?php

return [
    /**
     * List of allowed username keys
     */
    'username_fields' => [
        'email',
        'mobile'
    ],

    'username_registration_validation' => [
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'mobile' => ['required', 'string', 'min:10', 'unique:users'],
    ]
];
