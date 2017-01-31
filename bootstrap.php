<?php

require_once __DIR__.'/vendor/autoload.php';

config()->setBasePath(realpath(__DIR__));
config()->load();

require_once __DIR__.'/route.php';
