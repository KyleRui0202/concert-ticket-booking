<?php

try {
    $connection = config('database.default');
    $username = '';
    $password = '';
    $options = [];

    switch (config('database.connections.'.$connection.'.driver')) {
        case 'sqlite':
            $dsn = 'sqlite:'.datastore_path(config('database.connections.'.$connection.'.database'));
            break;
        default:
            throw new InvalidArgumentException('Invalid Database Driver: '.
                config('database.connections.'.$connection.'.driver'));
    }

    $conn = new PDO($dsn, $username, $password, $options);

    // Throw PDOException upon errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Disable emulated prepared to prevernt SQL injection
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

return $conn; 
