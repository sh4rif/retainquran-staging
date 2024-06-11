<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Card extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'tbl_cards';

    protected $primaryKey = 'card_id';

    protected $fillable = array('card_name', 'due_at', 'is_performed', 'usr_id', 'state_id', 'surah_id','verse_id','deck_id','created_at','updated_at');
}
