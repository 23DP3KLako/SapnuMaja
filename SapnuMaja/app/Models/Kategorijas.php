<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategorijas extends Model
{
    protected $table = 'Kategorijas';
    protected $primaryKey = 'ID';
    
    protected $fillable = ['nosaukums', 'slogans'];
    
    public function majas()
    {
        return $this->hasMany(Maja::class, 'KategorijasID', 'ID');
    }
}