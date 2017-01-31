<?php

$viewPath = view_path();

route_register(['method' => 'GET', 'uri' => '/'], $viewPath.DIRECTORY_SEPARATOR.'home.php');
route_register(['method' => ['GET', 'POST'], 'uri' => '/event'], $viewPath.DIRECTORY_SEPARATOR.'event.php');
route_register(['method' => ['GET', 'POST'], 'uri' => '/booking'], $viewPath.DIRECTORY_SEPARATOR.'booking.php');
route_register(['method' =>'GET', 'uri' => '/purchase'], $viewPath.DIRECTORY_SEPARATOR.'purchase.php');
