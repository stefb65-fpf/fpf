<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Histoevent extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function event() {
        return $this->belongsTo('App\Models\Event', 'event_id');
    }

    public function personne() {
        return $this->belongsTo('App\Models\Personne', 'personne_id');
    }

    public function utilisateur() {
        return $this->belongsTo('App\Models\Utilisateur', 'utilisateur_id');
    }

    public function club() {
        return $this->belongsTo('App\Models\Club', 'club_id');
    }
}
