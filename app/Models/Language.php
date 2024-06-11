<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $table = 'tbl_languages';

    protected $primaryKey = 'language_id';

    protected $fillable = array('language_name', 'flag_img');
}
