<?php

return [

    /*
    |----------------------------------------------------------------
    | Timeout for Ticket Booking Process
    |----------------------------------------------------------------
    |
    | The tickets during the booking process will be reserved
    | for the user until it passed the specified timeout (in seconds).
    |
    */
    'booking_timeout' => 300,

    /*
    |----------------------------------------------------------------
    | Ticket Repository
    |----------------------------------------------------------------
    |
    | This files inside "datastore" directory presents the total
    | number of tickets inside.
    |
    */
    'ticket_repository' => 'total_tickets.txt',
];
