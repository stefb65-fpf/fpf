<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluationsitem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function evaluationstheme() {
        return $this->belongsTo('App\Models\Evaluationstheme');
    }
}
