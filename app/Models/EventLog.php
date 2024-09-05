<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    use HasFactory;
    protected $table='event_logs';

    protected $fillable=[
        'user_id',
        'event_type',
        'event_data'
    ];
}
