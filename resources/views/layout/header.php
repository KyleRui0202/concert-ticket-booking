<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <title><?php
            echo (isset($title) ? $title.' | ' : '') . 'Concert Ticket Booking';
        ?></title>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link rel="stylesheet" href="/assets/css/main.css">
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
    </head>
    <body>
        <nav>
            <div class="container">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/event">Event</a></li>
                    <?php if (session()): ?>
                        <li>
                        <?php if (session('booking')): ?>
                            <a href="/booking">Reserved Booking</a>
                        <?php else: ?>
                            <span class="inactive">Reserved Booking</span>
                        <?php endif; ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
        
