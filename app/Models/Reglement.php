<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reglement extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function utilisateurs() {
        return $this->belongsToMany('App\Models\Utilisateur', 'reglementsutilisateurs', 'reglements_id', 'utilisateurs_id');
    }
}
