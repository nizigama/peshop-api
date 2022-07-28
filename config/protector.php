<?php

return [

    /*
    |
    |
    |-------------------------------------------------------------------------
    |JWT_TOKEN Configs
    |-------------------------------------------------------------------------
    |
    |These are configurations used to tweak the jwt token for user authentication
    |
    */
    'jwt_expires_after_minutes' => env("JWT_TOKEN_DURATION_IN_MINUTES", 60),


    /*
    |
    |
    |-------------------------------------------------------------------------
    |Protector middleware roles
    |-------------------------------------------------------------------------
    |
    |These are the roles supported by the protector middleware
    |
    */
    'roles' => [
        'admin',
        'customer'
    ]
];