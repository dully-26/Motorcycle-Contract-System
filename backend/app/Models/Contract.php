<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'user_id','motorcycle_id','contract_request_id','start_date','end_date',
        'total_amount','paid_amount','balance','status'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function motorcycle() { return $this->belongsTo(Motorcycle::class); }
    public function witnesses() { return $this->hasMany(Witness::class); }
    public function guarantors() { return $this->hasMany(Guarantor::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}