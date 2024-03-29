<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fonction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function utilisateurs() {
        return $this->belongsToMany('App\Models\Utilisateur');
    }

    public function droits() {
        return $this->belongsToMany('App\Models\Droit');
    }
}
