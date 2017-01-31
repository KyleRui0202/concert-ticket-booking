<?php

/*
 |------------------------------------------------------
 | Check if the booking session timeout 
 |------------------------------------------------------
 |
 | Return null if the timeout happen
 |
 */

$curTime = time();

if (session('booking.start_time')) {
    if ($curTime - session('booking.start_time') > config('app.booking_timeout')) {
        session([
            'booking' => null,
            'timeout_flash' => true
        ]);

        return null;    
    }
}

return $curTime;
