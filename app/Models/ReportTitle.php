<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportTitle extends Model
{
    protected $fillable = [
        'report_id',
        'title',
        'order'
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function rows()
    {
        return $this->hasMany(ReportRow::class);
    }
}
