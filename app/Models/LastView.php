<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastView extends Model
{
    use HasFactory;

    protected $table = 'last_view';
    protected $guarded = [];

    public static $lastViewType = 'last_view';

    public function surah()
    {
        return $this->belongsTo(Surah::class, 'surah_id', 'surah_id');
    }

    public function verse()
    {
        return $this->belongsTo(Verse::class, 'verse_id', 'verse_id');
    }

}
