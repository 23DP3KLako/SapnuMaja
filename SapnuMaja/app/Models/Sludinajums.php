<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sludinajums extends Model
{
    protected $table = 'Sludinajums';
    protected $primaryKey = 'SludinajumsID';
    
    protected $fillable = ['attels', 'LietotajsID', 'MajasID'];

    public $timestamps = true;
    
    public function lietotajs()
    {
        return $this->belongsTo(Lietotajs::class, 'LietotajsID', 'kodsID');
    }
    
    public function maja()
    {
        return $this->belongsTo(Maja::class, 'MajasID', 'MajasID');
    }
}