<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Daily_data extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'tbl_daily_data';

    protected $primaryKey = 'dd_id';

    protected $fillable = array('dd_usr_id', 'dd_total_cards', 'dd_performed', 'dd_not_performed', 'dd_status', 'dd_date');






}