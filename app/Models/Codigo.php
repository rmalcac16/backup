<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Codigo extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'codigo', 'user_id', 'expires_at'
    ];

    protected $dates = [
        'created_at',
        'expires_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($codigo) {
            $codigo->expires_at = Carbon::now()->addMinutes(30);
        });
    }
}
