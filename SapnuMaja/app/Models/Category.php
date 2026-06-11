<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name', 'slug'];
    
    // Связь: категория имеет много домов
    public function majas()
    {
        return $this->hasMany(Maja::class, 'category_id');
    }
}