## Introduction
This is a simple concert ticket booking system, and you can follow the following process to complete the booking

1. Enter the home page ("/") to click "Buy Tickets".
2. Select to ticket quantity to book at the event page ("/event").
3. Continue to fill the personal information to complete the booking at the booking page ("/booking").
4. As soon as you are directed to the booking page, the reserved tickets will held for you to purchase within a specific time (configurable).
5. Upon successful ticket booking, you will be directed to the purchase page ("/purchase").

## Server Requirements
The ticket booking system has a few system requirements for your server as follows:
* PHP >= 5.5.9
* PDO PHP Extension and PDO_SQLITE Driver

## Installation
[Composer](https://getcomposer.org) is used to for code autoloading. So, make sure you have Composer installed on your machine before using this system.

### Setup the system using Composer
Run the below command to build the autoloader and setup the `config` directory for configuration. 

```
composer create-project
```

### Configuration
### Public Directory
You should configure your web server's document/web root to be the `public` directory. The `index.php` in this directory serves as the front controller for all HTTP requests entering the ticket booking system.

### Configuration Files
All of the configuration files for the system are stored in the `config` directory. Each option is documented, so feel free to look through the files and modify them if necessary.

### Ticket Repository
Only the ticket quantity are considered for the ticket booking system at this moment. So you probably need to create a new file within the ``datastore` directory to store the total tickets and modify `ticket_repository` to the new file name within `config/app.php`

### Database Configuration
[SQlite](https://sqlite.org) is used here to store sold ticket records as well as the temporary booking sessions. You may need to configure it in the 'config/database.php' file and create the database(file) within the `datastore` directory.

The database schema can be checked out in the `datastore/setup.php`. Plesse run it to build the schema:
```
php datastore/setup.php
``` 

Altough SQLite is used here, other RDBMS (e.g., MySQL, Postgres, etc.) may be used by adding their configuration in 'config/database.php', create the corresponding 'utils/setup_database_connection.php'. (Notice: some additional queries in the code may be modified if necessary according to their own syntax)

## Web Server Configuration
Semantic URL is used here, so you may need to modify your web server configuration if necessady. 

### Nginx
If you are using Nginx, the following directive in your site configuration will direct all requests to the `index.php` front controller:
```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```
