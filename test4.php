<?php
$b = \App\Models\Booking::whereIn('customer_name', ['Shania', 'Sayangku', 'Ajeng Elsa', 'Christy', 'Ella', 'Han So Hee'])->select('customer_name', 'user_id', 'cashier_id')->get();
echo json_encode($b);
