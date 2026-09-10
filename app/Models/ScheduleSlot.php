<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSlot extends Model
{
    use HasFactory;

    protected $table = 'schedule_slots';

    // Language constants
    const HINDI = 1;
    const ENGLISH = 2;
    const GUJARATI = 3;

    // Status constants
    const SCHEDULED = 1;
    const COMPLETED = 2;
    const CANCELLED = 3;
    const NOT_REACHABLE = 4;

    protected $fillable = [
        'user_id',
        'date',
        'time',
        'language',
        'remarks',
        'status',
        'is_deleted',
        'created_at',
        'updated_at'
    ];

    public static function getLanguages()
    {
        return [
            self::HINDI => 'Hindi',
            self::ENGLISH => 'English',
            self::GUJARATI => 'Gujarati',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::SCHEDULED => 'Scheduled',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::NOT_REACHABLE => 'Not Reachable',
        ];
    }

    public function getLanguageTextAttribute()
    {
        return self::getLanguages()[$this->language] ?? 'Unknown';
    }

    public function getStatusTextAttribute()
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }
}
