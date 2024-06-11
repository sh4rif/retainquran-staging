<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reciter extends Model
{
    use HasFactory;

    protected $table = 'tbl_reciters';

    protected $primaryKey = 'reciter_id';


    public function translations()
    {
        
    }
}
