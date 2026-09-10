<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebinarEvent extends Model
{
    protected $table = 'webinar_event';

    protected $fillable = [
        'event_name',
        'event_datetime',
        'event_main_price',
        'event_offer_price',
        'event_title',
        'event_desc_1',
        'event_image',
        'mentor_name',
        'language',
        'program_type',
        'link',
        'community_link',
        'isActive',
        'isDelete'
    ];

    public $timestamps = false;

    // Relations
    public function details()
    {
        return $this->hasMany(WebinarEventDetail::class, 'event_id');
    }

    public function registrations()
    {
        return $this->hasMany(WebinarRegistration::class, 'event_id', 'id');
    }

    public function order()
    {
        return $this->hasMany(WebinarOrder::class, 'program_id', 'id');
    }
}
