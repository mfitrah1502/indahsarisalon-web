<?php
$b = \App\Models\User::where('role', 'pelanggan')->get();
echo json_encode($b->pluck('name'));
