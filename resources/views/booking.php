<?php

use Http\Request;

$request = new Request($_SERVER, $_GET, $_POST);

/*
 |------------------------------------------------------
 | Check if this view is accessed by 'GET'/'POST' method
 |------------------------------------------------------
 */
if (!$request->isMethod('get') && !$request->isMethod('POST')) {
    echo "Invalid Access Method: " . $request->getMethod();
    
    exit();
}

// Start a new or resume the existing session
session_start();

// Check if the booking session has timed out
$curTime = require __DIR__.'/utils/check_booking_timeout.php';
if (is_null($curTime)) {
    header('Location: ' . url('/event'));
    exit();
}

// Connect to the database
$db_conn = require __DIR__.'/../../utils/setup_database_connection.php';


/*
 |-----------------------------------------------------
 | Process 'POST' request
 |-----------------------------------------------------
 */
if ($request->isMethod('post')) {
    $remainingTickets = call_user_func(require __DIR__.
        '/utils/check_remaining_tickets.php', $db_conn);

    // Check if the submitted ticket quantity is valid
    $filteredTicketQuantity = filter_var(session('booking.ticket_quantity'),
        FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
                'max_range' => $remainingTickets
            ]
        ]
    );

    if ($filteredTicketQuantity === false) {
        session(['invalid_input_flash' => true]);
        session(['old_input_flash' => session('booking.ticket_quantity')]);
        
        // Reset booking session upon invalid purchase submission 
        session(['booking' => null]); 
            
        header('Location: ' . url('/event'));
    } else {
        $inputs = $request->getPostInput(['first_name', 'last_name', 'address', 'phone']);

        // Check if other fields are valid
        if ($invalid_inputs = array_filter($inputs,
            function ($input) { return empty($input); })) {
            session(['invalid_input_flash' => $invalid_inputs]);
            session(['old_input_flash' => $inputs]);
            
            header('Location: ' . url('/booking'));
        } else {
            // Store the new sold ticket record
            try {
                $db_conn->prepare('INSERT INTO sold_tickets(first_name, last_name, address, phone, quantity)'.
                    'VALUES (?, ?, ?, ?, ?)')->execute(array_merge(array_values($inputs), [$filteredTicketQuantity]));
            } catch (PDOException $e) {
                die("Database Insertion Error: " . $e->getMessage());
            }

            // Cleanup the session
            try {
                $db_conn->prepare('DELETE FROM ticket_purchase_sessions WHERE session_id = ?')
                    ->execute([session_id()]);
            } catch (PDOException $e) {    
                die("Database Deletion Error: " . $e->getMessage());
            }
            session(['booking' => null]);
            session(['purchased_ticket_quantity_flash' => $filteredTicketQuantity]);
            
            header('Location: ' . url('/purchase'));
        }
    }

    exit();    
}


/*
 |-----------------------------------------------------
 | Process 'GET' request
 |-----------------------------------------------------
 */
if (!session('booking.start_time')) {
    if (session('booking.ticket_quantity')) {
        session(['booking.start_time' => $curTime]);

        // Insert a new booking session to database to hold the tickets
        try {        
            $db_conn->prepare('REPLACE INTO ticket_purchase_sessions VALUES (?, ?, ?)')
                ->execute([session_id(), session('booking.ticket_quantity'), $curTime]);    
        } catch (PDOException $e) {
            die("Database Insertion Error: " . $e->getMessage());
        }
    } else {
        session(['invalid_input_flash' => true]);
        
        header('Location: ' . url('/event'));
        exit();
    }
} else {
    $oldBookingSessionExists = true;
}


// Build html header
$title = 'Booking';
require __DIR__.'/layout/header.php';

?>

<script>
    var restTime = 1000 * <?php echo session('booking.start_time') + config('app.booking_timeout'); ?> - Date.now();
    setTimeout(function() {
        var redirect = confirm('Your booking session timed out! Please go back to the event page.');
        if (redirect == true) {
             location.href = "<?php echo htmlentities(url('/event')); ?>";
        }
    }, (restTime > 0 ? restTime : 0));
</script>

<div id="content">
    <div class="container">
        <h1 class="content-title text-center">Fill Personal Information</h1>
        <div class="notice text-center">
            <?php
                if (isset($oldBookingSessionExists) && $oldBookingSessionExists) {
                    echo '<p class="info">The booking with reserved tickets (shown as below) for you was resumed. Please complete it before timeout.</p>';
                } 

                if (count(session('invalid_input_flash')) > 0) {
                    echo '<p class="alert">Please provide the valid data for: ' .
                        implode(', ', array_keys(session('invalid_input_flash'))) . '</p>';
                    session(['invalid_input_flash' => null]);
                }
            ?>
        </div>
        <p class="text-center">Ticket Quantity: <?php echo htmlentities(session('booking.ticket_quantity')); ?></p>
        <form class="text-center" action="<?php echo htmlentities($request->getPathInfo()); ?>" method="post">
            <div class="form-group">    
                <label for="first-name">First Name</label>
                <input id="first-name" type="text" name="first_name" value="<?php echo htmlentities(session('old_input_flash.first_name')); ?>" required>
            </div>
            <div class="form-group">    
                <label for="last-name">Last Name</label>
                <input id="last-name" type="text" name="last_name" value="<?php echo htmlentities(session('old_input_flash.last_name')); ?>" required>
            </div>
            <div class="form-group">    
                <label for="address">Address</label>
                <input id="address" type="text" name="address" value="<?php echo htmlentities(session('old_input_flash.address')); ?>" required>
            </div>
            <div class="form-group">    
                <label for="phone">Phone Number</label>
                <input id="phone" type="tel" name="phone" value="<?php echo htmlentities(session('old_input_flash.phone')); ?>" required>
            </div>
            <div class="form-group submit">
                <input class="btn" type="submit" name="submit" value="Submit">
            </div>
        </form>              
    </div>
</div>

<?php

// Build html footer
require __DIR__.'/layout/footer.php';

session(['old_input_flash' => null]);

?>
