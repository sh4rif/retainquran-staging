<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfflineLog extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'offline_logs';

    protected $primaryKey = 'id';

    protected $fillable = array('request_json', 'current_datetime', 'status', 'created_at', 'updated_at');
}
