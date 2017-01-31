<?php

/*
 |------------------------------------------------------
 | Return the Closure to calculate the remaining tickets 
 |------------------------------------------------------
 */

return function(PDO $db_conn) { 
    $totalTicekts = require __DIR__.'/check_total_tickets.php';

    try {
        $soldTickets = (int) $db_conn->query('SELECT SUM(quantity) FROM sold_tickets')->fetchColumn();

        $db_conn->prepare('DELETE FROM ticket_purchase_sessions WHERE start_time < ?')
            ->execute([time() - config('app.booking_timeout')]);

        $stmt = $db_conn->prepare('SELECT SUM(quantity) FROM ticket_purchase_sessions WHERE session_id <> ?');
        $stmt->execute([session_id()]);
        $heldTickets = (int) $stmt->fetchColumn();    
     
    } catch (PDOException $e) {
        die("Database Query Error: " . $e->getMessage());
    }

    return $totalTickets - $soldTickets - $heldTickets;
};
