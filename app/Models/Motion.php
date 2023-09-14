<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motion extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function election() {
        return $this->belongsTo('App\Models\Election');
    }
    public function reponse() {
        return $this->belongsTo('App\Models\Reponse', 'reponses_id');
    }
}
