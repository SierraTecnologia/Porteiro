<?php

namespace Porteiro\Models;

use Illuminate\Database\Eloquent\Model;
use Porteiro;
use Porteiro\Models\Role;

class Permission extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string[]
     *
     * @psalm-var array{0: string}
     */
    protected $fillable = [
        'name'
    ];

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class);
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
