<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LietotajMajas extends Model
{
    protected $table = 'LietotajMajas';
    protected $primaryKey = 'LietotajMajasID';
    
    protected $fillable = ['LietotajsID', 'SludinajumsID', 'MajasID','statuss'];
    
    public function lietotajs()
    {
        return $this->belongsTo(Lietotajs::class, 'LietotajsID', 'kodsID');
    }
    
    public function maja()
    {
        return $this->belongsTo(Sludinajums::class, 'SludinajumsID', 'SludinajumsID');
    }
}