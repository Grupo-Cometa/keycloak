<?php

return [
    'realm_public_key' => env('realm_public_key'),
    'signature_algorithm' => env('signature_algorithm'),
    'user_provider_credential' => 'public_id',
    'token_principal_attribute' => 'sub',
    'client_id' => 'cometa-leitura',
    'bind_user_keycloak' => [
        'name' => 'name',
        'public_id' => 'sub',
        'preferred_username' => 'preferred_username',
        'email' => 'email',
    ]
];
