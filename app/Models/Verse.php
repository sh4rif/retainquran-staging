<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verse extends Model
{
    use HasFactory;


    protected $table = 'tbl_verses';

    protected $primaryKey = 'verse_id';


    public function Surah()
    {
        return $this->belongsTo(Surah::class);
    }
}
