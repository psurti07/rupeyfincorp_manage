<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebinarOrder extends Model
{
    protected $table = 'webinar_order';

    protected $fillable = [
        'rec_date',
        'webinar_id',
        'userid',
        'amount',
        'paymentid',
        'orderid',
        'isUser',
        'isAttend',
        'isActive',
        'isDelete'
    ];

    public $timestamps = false;

    protected $casts = [
        'rec_date' => 'datetime',
        'registration_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(WebinarRegistration::class, 'userid');
    }

    public function webinar()
    {
        return $this->belongsTo(WebinarEvent::class, 'webinar_id', 'id');
    }
}
