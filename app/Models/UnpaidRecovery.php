<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UnpaidRecovery extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'unpaid_recovery';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customerid',
        'ref',
        'amount',
        'attachment',
        'notes',
        'process',
        'status',
        'factured_at',
        'echance_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'factured_at',
        'echance_at',
        'last_relaunch',
        'next_relaunch',
    ];
}
