<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formateur extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function formations() {
        return $this->belongsToMany('App\Models\Formation');
    }

    public function personne() {
        return $this->belongsTo('App\Models\Personne');
    }
}
