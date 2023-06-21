<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personne extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function adresses() {
        return $this->belongsToMany('App\Models\Adresse');
    }

    public function utilisateurs() {
        return $this->hasMany('App\Models\Utilisateur');
    }

    public function abonnements() {
        return $this->hasMany('App\Models\Abonnement');
    }

    public function historiques() {
        return $this->hasMany('App\Models\Historique');
    }

    public function historiquemails() {
        return $this->hasMany('App\Models\Historiquemail');
    }
}
