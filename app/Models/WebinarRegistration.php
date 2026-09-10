<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebinarRegistration extends Model
{
    protected $table = 'user_webinar_registration';

    protected $fillable = [
        'rec_date',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'pincode',
        'city',
        'state',
        'isUser',
        'process_step',
        'occupation',
        'earning_goal',
        'is_joincommunity',
        'isActive',
        'isDelete',
        'isDnd'
    ];

    public $timestamps = false;

    protected $casts = [
        'rec_date' => 'datetime',
    ];

    // (Optional) if you add event_id later
    public function event()
    {
        return $this->belongsTo(WebinarEvent::class, 'event_id');
    }
}
