<?php

/*
 |-----------------------------------------------------
 | Get the total ticket number
 |-----------------------------------------------------
 */

$ticketFilename = datastore_path(config('app.ticket_repository'));

$ticketFile = fopen($ticketFilename, "r") or
    die("Unable to access the ticket repository!");

$totalTickets = (int) fread($ticketFile, filesize($ticketFilename));

fclose($ticketFile);

return $totalTickets;
