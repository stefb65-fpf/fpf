<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ur extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function clubs() {
        return $this->hasMany('App\Models\Club', 'urs_id');
    }

    public function utilisateurs() {
        return $this->hasMany('App\Models\Utilisateur', 'urs_id');
    }

    public function adresse() {
        return $this->belongsTo('App\Models\Adresse', 'adresses_id');
    }
}
