<?php
$b = \App\Models\Booking::all();
echo json_encode($b->pluck('customer_name')->unique());
