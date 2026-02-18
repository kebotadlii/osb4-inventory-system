<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportRow extends Model
{
    protected $fillable = [
        'report_id',
        'report_title_id',
        'value'
    ];

    public function title()
    {
        return $this->belongsTo(ReportTitle::class);
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
