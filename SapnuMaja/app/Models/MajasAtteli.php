<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MajasAtteli extends Model
{
    protected $table = 'Majas_atteli';
    protected $primaryKey = 'attels_id';
    public $timestamps = false; // только created_at
    
    protected $fillable = ['attels_fail', 'attelu_kartiba', 'MajasID'];
    
    public function maja()
    {
        return $this->belongsTo(Maja::class, 'MajasID', 'MajasID');
    }
}