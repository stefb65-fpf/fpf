<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function sessions() {
        return $this->hasMany('App\Models\Session');
    }

    public function formateurs() {
        return $this->belongsToMany('App\Models\Formateur', 'formation_formateur');
    }

    public function interests() {
        return $this->hasMany('App\Models\Interest');
    }

    public function demandes() {
        return $this->hasMany('App\Models\Demande');
    }

    public function categorie() {
        return $this->belongsTo('App\Models\Categorieformation', 'categories_formation_id');
    }
}
