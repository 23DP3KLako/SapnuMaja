<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Lietotajs extends Authenticatable
{
    protected $table = 'Lietotajs';
    protected $primaryKey = 'kodsID';
    
    protected $fillable = [
        'lietotajvards', 'epasts', 'parole', 'statuss', 'loma'
    ];
    
    protected $hidden = ['parole'];

    public $timestamps = true;

    protected $attributes = [
        'loma' => 'viesis',  // По умолчанию все новые пользователи - гости
        'statuss' => 'aktivs'
    ];
    
    public function getAuthPassword()
    {
        return $this->parole;
    }
    
    // Связи
    public function majas()
    {
        return $this->belongsToMany(Maja::class, 'LietotajMajas', 'LietotajsID', 'MajasID')
                    ->withPivot('statuss');
    }
    
    public function sludinajumi()
    {
        return $this->hasMany(Sludinajums::class, 'LietotajsID', 'kodsID');
    }
}