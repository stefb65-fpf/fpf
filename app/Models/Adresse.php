<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adresse extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function personnes() {
        return $this->belongsToMany('App\Models\Personne');
    }
}
