<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorieformation extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $table = 'categories_formations';

    public function formations() {
        return $this->hasMany('App\Models\Formation');
    }
}
