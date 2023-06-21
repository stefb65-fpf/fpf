<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ur extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function clubs() {
        return $this->hasMany('App\Models\Club');
    }

    public function utilisateurs() {
        return $this->hasMany('App\Models\Utilisateur');
    }
}
