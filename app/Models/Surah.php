<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbl_surah';

    protected $primaryKey = 'surah_id';

    public function verses()
    {
        return $this->hasMany(Verse::class);
    }
}
