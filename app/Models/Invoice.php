<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function personne() {
        return $this->belongsTo('App\Models\Personne', 'personne_id');
    }

    public function club() {
        return $this->belongsTo('App\Models\Club', 'club_id');
    }

    public function ur() {
        return $this->belongsTo('App\Models\Ur', 'ur_id');
    }

    public function getStorageDir() {
        return storage_path().'/app/public/uploads/invoices/'.ceil($this->id / 100);
    }

}
