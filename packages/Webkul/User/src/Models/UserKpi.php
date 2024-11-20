<?php

namespace Webkul\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Webkul\User\Contracts\UserKpi as UserKpiContract;

class UserKpi extends Authenticatable implements UserKpiContract
{
    use HasApiTokens, Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'user_id',
        'date',
        'kpi',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
