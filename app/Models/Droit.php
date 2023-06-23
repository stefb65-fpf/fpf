<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Droit extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function utilisateurs() {
        return $this->belongsToMany('App\Models\Utilisateur');
    }

    public function fonctions() {
        return $this->belongsToMany('App\Models\Fonction');
    }
}
