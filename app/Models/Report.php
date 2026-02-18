<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'name'
    ];

    public function titles()
    {
        return $this->hasMany(ReportTitle::class);
    }

    public function rows()
    {
        return $this->hasMany(ReportRow::class);
    }
}
