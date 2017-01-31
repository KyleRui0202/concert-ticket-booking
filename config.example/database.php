<?php

return [

    /*
    |----------------------------------------------------------------
    | Default Database Connection
    |----------------------------------------------------------------
    | Specify which of the database connections below to use as the
    | the default connection for all database work.
    |
    */
    'default' => 'sqlite',
    
    /*
    |----------------------------------------------------------------
    | Available Database Connections
    |----------------------------------------------------------------
    | Specify all the available database connections setup.
    |
    */
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => 'database.sqlite',
            'prefix' => '',
        ],

        'mysql' => [
            //...
        ],
    ],

];
