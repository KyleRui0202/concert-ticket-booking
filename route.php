<?php

/*
 |-----------------------------------------------------
 | Register routes for the application.
 |-----------------------------------------------------
 */

route_register(['method' => 'GET', 'uri' => '/'], view_path('home.php'));
route_register(['method' => ['GET', 'POST'], 'uri' => '/event'], view_path('event.php'));
route_register(['method' => ['GET', 'POST'], 'uri' => '/booking'], view_path('booking.php'));
route_register(['method' =>'GET', 'uri' => '/purchase'], view_path('purchase.php'));
