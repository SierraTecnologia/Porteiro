<?php

namespace Porteiro\Models;

use Illuminate\Database\Eloquent\Model;
use Porteiro;

class Permission extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string[]
     *
     * @psalm-var array{0: 'name'}
     */
    protected $fillable = [
        'name'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     *
     * @psalm-return \Illuminate\Database\Eloquent\Relations\BelongsToMany<empty>
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Porteiro::modelClass('Role'));
    }

    public static function generateFor($table_name): void
    {
        self::firstOrCreate(['key' => 'browse_'.$table_name, 'table_name' => $table_name]);
        self::firstOrCreate(['key' => 'read_'.$table_name, 'table_name' => $table_name]);
        self::firstOrCreate(['key' => 'edit_'.$table_name, 'table_name' => $table_name]);
        self::firstOrCreate(['key' => 'add_'.$table_name, 'table_name' => $table_name]);
        self::firstOrCreate(['key' => 'delete_'.$table_name, 'table_name' => $table_name]);
    }

    public static function removeFrom($table_name): void
    {
        self::where(['table_name' => $table_name])->delete();
    }
}
