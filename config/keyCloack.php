<?php

return [
    'reaml_public_key' => env('reaml_public_key'),
    'signature_algorithm' => env('signature_algorithm'),
    'user_provider_credential' => 'public_id',
    'token_principal_attribute' => 'sub',
    'client_id' => 'cometa-leitura',
    'bind_user_key_cloack' => [
        'name' => 'name',
        'public_id' => 'sub',
        'preferred_username' => 'preferred_username',
        'email' => 'email',
    ]
];
