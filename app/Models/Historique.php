<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historique extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function personne() {
        return $this->belongsTo('App\Models\Personne');
    }
}
