<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card_history extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'tbl_usr_card_history';

    protected $primaryKey = 'uch_id';

    protected $fillable = array('card_status', 'card_id', 'usr_id', 'state_id', 'timely_performed','due_date','created_at','updated_at');


}
