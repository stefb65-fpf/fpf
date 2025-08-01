<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function personne() {
        return $this->belongsTo('App\Models\Personne');
    }

    public function club() {
        return $this->belongsTo('App\Models\Club', 'clubs_id');
    }

    public function ur() {
        return $this->belongsTo('App\Models\Ur', 'urs_id');
    }

    public function fonctions() {
        return $this->belongsToMany('App\Models\Fonction');
    }

    public function droits() {
        return $this->belongsToMany('App\Models\Droit');
    }
    public function histoevents() {
        return $this->hasMany('App\Models\Histoevent', 'event_id');
    }
}
