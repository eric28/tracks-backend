<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GPX extends Model
{
    const COLUMN_ID = 'id';
    const COLUMN_NAME = 'name';
    const COLUMN_GPX_JSON = 'gpx_json';
    const COLUMN_CENTER = 'center_json';

    protected $table = 'gpx';

    protected $fillable = [
        self::COLUMN_NAME,
        self::COLUMN_GPX_JSON,
        self::COLUMN_CENTER
    ];
}