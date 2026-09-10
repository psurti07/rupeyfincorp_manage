<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebinarEventDetail extends Model
{
    protected $table = 'webinar_event_detail';

    protected $fillable = [
        'event_id',
        'event_title',
        'event_desc_1',
        'event_desc_2',
        'event_image',
        'mentor_name',
        'language',
        'isActive',
        'isDelete'
    ];

    public $timestamps = false;

    public function event()
    {
        return $this->belongsTo(WebinarEvent::class, 'event_id');
    }
}
