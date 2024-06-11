<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stats extends Model
{
    use HasFactory;

/*
State 1 - 10 mins (poor), 1 day (fair), 2 days (good)
State 2 - 10 mins (poor), 2 day (fair), 4 days (good)
State 3 - 10 mins (poor), 4 days (fair), 8 days (good)
State 4 - 10 mins (poor), 8 days (fair), 16 days (good)
State 5 - 10 mins (poor), 16 days (fair), 30 days (good)
State 6 - 10 mins (poor), 30 days (fair), 60 days (good)
*/
}
