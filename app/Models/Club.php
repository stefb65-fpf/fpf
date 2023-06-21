<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function ur() {
        return $this->belongsTo('App\Models\Ur');
    }

    public function utilisateurs() {
        return $this->hasMany('App\Models\Utilisateur');
    }
}
