<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maja extends Model
{
    protected $table = 'Majas';
    protected $primaryKey = 'MajasID';
    
    protected $fillable = [
        'pilseta', 'rajons', 'adrese', 'cena', 'platiba', 'zemes_platiba',
        'istabu_skaits', 'stavu_skaits', 'celsanas_gads', 'stavoklis',
        'virsraksts', 'apraksts', 'ipasibas', 'KategorijasID', 'Majas_plansID'
    ];
    
    // Связи
    public function kategorija()
    {
        return $this->belongsTo(Kategorijas::class, 'KategorijasID', 'ID');
    }
    
    public function lietotajs()
    {
        return $this->belongsToMany(Lietotajs::class, 'LietotajMajas', 'MajasID', 'LietotajsID')
                    ->withPivot('statuss');
    }
    
    public function sludinajums()
    {
        return $this->hasOne(Sludinajums::class, 'MajasID', 'MajasID');
    }
    
    public function atteli()
    {
        return $this->hasMany(MajasAtteli::class, 'MajasID', 'MajasID')
                    ->orderBy('attelu_kartiba');
    }
    
    public function plans()
    {
        return $this->belongsTo(LietotajMajas::class, 'LietotajmajasID', 'LietotajMajasID');
    }
}