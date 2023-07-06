<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function ur() {
        return $this->belongsTo('App\Models\Ur', 'urs_id');
    }

    public function utilisateurs() {
        return $this->hasMany('App\Models\Utilisateur', 'clubs_id');
    }

    public function adresse() {
        return $this->belongsTo('App\Models\Adresse', 'adresses_id');
    }
}
