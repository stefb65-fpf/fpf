<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reversement extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function reglements()
    {
        return $this->hasMany('App\Models\Reglement');
    }
}
