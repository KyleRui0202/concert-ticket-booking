<?php

use Http\Request;

require_once __DIR__.'/../bootstrap.php';

$request = new Request($_SERVER, $_GET, $_POST);
$route = route_register();

if ($route->isRegistered($request->getMethod(), $request->getPathInfo())) {
    $route->resolve($request->getMethod(), $request->getPathInfo());
} else {
    echo "Sorry! The page you are looking for could not be found.";
}
