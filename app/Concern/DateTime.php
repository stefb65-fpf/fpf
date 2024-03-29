<?php


namespace App\Concern;

use Carbon\Carbon;

trait DateTime {
    public function getCreatedAtAttribute($value) {
        if ($value == null) return '';
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y H:i');
    }

    public function getUpdatedAtAttribute($value) {
        if ($value == null) return '';
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y H:i');
    }
}
