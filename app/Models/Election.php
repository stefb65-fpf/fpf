<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function vote() {
        return $this->belongsTo('App\Models\Vote');
    }

    public function candidats() {
        return $this->hasMany('App\Models\Candidat');
    }

    public function motions() {
        return $this->hasMany('App\Models\Motion');
    }
}
