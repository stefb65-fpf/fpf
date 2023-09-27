<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function session() {
        return $this->belongsTo('App\Models\Session');
    }

    public function pesonne() {
        return $this->belongsTo('App\Models\Personne');
    }
}
