<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_history';

    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'changed_by_user_id',
        'note',
    ];
}
