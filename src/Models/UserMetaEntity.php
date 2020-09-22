<?php

namespace Porteiro\Models;

use App\Models\Model;

/**
 * Class UserMetaEntity.
 *
 * @property int id
 * @property string value
 * @property Collection posts
 * @package  App\Models
 */
class UserMetaEntity extends Model
{
    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $fillable = [
        'value',
    ];

}
