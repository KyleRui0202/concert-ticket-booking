<?php

session_start();

if (!session('purchased_ticket_quantity_flash')) {
    header('Location: '. url('/'));
    exit();
}

$title = 'Purchase';
require __DIR__.'/layout/header.php';

?>

<div id="content">
    <div class="container">
    <p>Congratulations! You have successfully purchased <?php echo session('purchased_ticket_quantity_flash') ?> ticket(s).</p>
    </div>
</div>

<?php

require __DIR__.'/layout/footer.php';

session(['purchased_ticket_quantity_flash' => null]);

?>
