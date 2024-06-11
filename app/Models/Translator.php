<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translator extends Model
{
    use HasFactory;

    protected $table = 'tbl_translators';

    protected $primaryKey = 'trans_id';
}
