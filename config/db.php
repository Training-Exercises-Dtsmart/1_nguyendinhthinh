<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => env('DB_DRIVER') . ':host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'charset' => env('DB_CHARSET'),

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
