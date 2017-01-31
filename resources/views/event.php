<?php

use Http\Request;

// Build the request
$request = new Request($_SERVER, $_GET, $_POST);

// Check if this view is accessed by 'GET'/'POST' method
if (!$request->isMethod('get') && !$request->isMethod('POST')) {
    echo "Invalid Access Method: " . $accessMethod;

    exit();
}

// Start a new or resume an existing session
session_start();

require __DIR__.'/utils/check_booking_timeout.php';


/*
 |-----------------------------------------------------
 | Get the remaining ticket number
 |-----------------------------------------------------
 */
// Connect to the database
$db_conn = require __DIR__.'/../../utils/setup_database_connection.php';
$remainingTickets = call_user_func(require __DIR__.
    '/utils/check_remaining_tickets.php', $db_conn);


/*
 |-----------------------------------------------------
 | Process 'POST' request
 |-----------------------------------------------------
 */
if ($request->isMethod('post')) {
    // Check if the requret is a 'POST' resquest and
    if (session('booking')) {
        header('Location: ' . url('/booking'));
        exit();
    }

    // the submitted ticket quantity is validate
    $filteredTicketQuantity = filter_var($request->getPostInput('quantity'),
        FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
                'max_range' => $remainingTickets
            ]
        ]
    );

    if ($filteredTicketQuantity === false) {
        session(['invalid_input_flash' => true]);
        session(['old_input_flash' => $request->getPostInput('quantity')]);

        header('Location: ' . url('/event'));
    } else {
        session(['booking.ticket_quantity' => $filteredTicketQuantity]);

        header('Location: ' . url('/booking'));
    }

    exit();
} 

/*
 |-----------------------------------------------------
 | Process 'GET' request
 |-----------------------------------------------------
 */

// Build html header
$title = 'Event';
require __DIR__.'/layout/header.php';

?>

<div id="content">
    <div class="container"> 
        <h1 class="content-title text-center">Select Ticket Quantity</h1>
        <div class="notice text-center">
            <?php
                if (session('invalid_input_flash')) {
                    echo '<p class="alert">Please submit a valid ticket quantity for purchase.</p>';
                    session(['invalid_input_flash' => null]);
                }

                if (session('timeout_flash')) {
                    echo '<p class="alert">Timeout! Please start over your booking.</p>';
                    session(['timeout_flash' => null]);
                }
            ?>
        </div>
        <?php if ($remainingTickets > 0): ?>
            <p class ="text-center">Only <?php echo htmlentities($remainingTickets); ?> ticket(s) left.</p>
            <form class="text-center" action="<?php echo htmlentities($request->getPathInfo()); ?>" method="post">
                <div class="form-group">
                    <label for="ticket-quantity">Ticket Quantity</label>
                    <input id="ticket-quantity" type="number" name="quantity" value="<?php echo htmlentities(session('old_input_flash')); ?>" min="1" max="<?php echo htmlentities($remainingTickets); ?>">
                </div>
                <div class="form-group submit">
                    <input class="btn" type="submit" name="submit" value="Continue">
                </div>
            </form>
        <?php else: ?>
                <p class="text-center">Sorry! There are no available tickets at this moment.</p>
        <?php endif; ?>                
    </div>
</div>

<?php

// Build html footer
require __DIR__.'/layout/footer.php';

session(['old_input_flash' => null]);

?>
