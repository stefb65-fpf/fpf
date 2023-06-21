<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historiquemail extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function personne() {
        return $this->belongsTo('App\Models\Personne');
    }
}
