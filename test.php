<?php
$b = \App\Models\Booking::whereNull('user_id')->selectRaw('customer_name, customer_email, customer_phone')->groupBy('customer_name', 'customer_email', 'customer_phone')->get();
echo json_encode($b);
