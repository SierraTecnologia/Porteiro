<?php

namespace Porteiro\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * @var string[]
     *
     * @psalm-var array{0: 'causer', 1: 'indentifier', 2: 'type', 3: 'data'}
     */
    protected $fillable = ['causer', 'indentifier', 'type', 'data'];
}
// public $table = "activities";

// public $primaryKey = "id";

// public $timestamps = true;

// public $fillable = [
//     'user_id',
//     'description',
//     'request',
// ];

// public $rules = [
//     'request' => 'required',
// ];