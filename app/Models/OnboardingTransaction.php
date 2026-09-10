<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingTransaction extends Model
{
    use HasFactory;

    protected $table = 'onboarding_transaction';

    protected $primaryKey = 'id';

    public $timestamps = false; // because table doesn't have created_at & updated_at

    protected $fillable = [
        'company_code',
        'date',
        'rec_date',
        'total_leads',
        'total_customers',
        'total_amount',
        'gst_amount',
        'isActive',
        'isDelete',
    ];

    protected $casts = [
        'date' => 'date',
        'rec_date' => 'datetime',
        'isActive' => 'boolean',
        'isDelete' => 'boolean',
    ];
}
