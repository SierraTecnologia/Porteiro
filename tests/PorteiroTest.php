<?php

namespace Porteiro\Tests;

use Porteiro\Models\User;
use Porteiro\Models\Role;
use Porteiro\Models\Permission;
use Porteiro\Models\Activity;
use Illuminate\Support\Facades\Hash;

class PorteiroTest extends TestCase
{
    public function test_user_creation_and_password_hashing()
    {
        $user = User::create([
            'name' => 'Admin Porteiro',
            'email' => 'admin@porteiro.com',
            'password' => Hash::make('secret123'),
            'admin' => 1,
        ]);

        $this->assertDatabaseHas('users', ['email' => 'admin@porteiro.com']);
        $this->assertEquals('Admin Porteiro', $user->name);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_role_creation_and_assignment()
    {
        $role = Role::create([
            'name' => 'superadmin',
        ]);

        $this->assertDatabaseHas('roles', ['name' => 'superadmin']);
        $this->assertEquals('superadmin', $role->name);
    }

    public function test_permission_generation_and_cleanup()
    {
        Permission::generateFor('posts');

        $this->assertDatabaseHas('permissions', ['key' => 'browse_posts', 'table_name' => 'posts']);
        $this->assertDatabaseHas('permissions', ['key' => 'read_posts', 'table_name' => 'posts']);
        $this->assertDatabaseHas('permissions', ['key' => 'edit_posts', 'table_name' => 'posts']);
        $this->assertDatabaseHas('permissions', ['key' => 'add_posts', 'table_name' => 'posts']);
        $this->assertDatabaseHas('permissions', ['key' => 'delete_posts', 'table_name' => 'posts']);

        Permission::removeFrom('posts');
        $this->assertDatabaseMissing('permissions', ['table_name' => 'posts']);
    }

    public function test_activity_logging()
    {
        $activity = Activity::create([
            'causer' => 'user:1',
            'indentifier' => 'session_123',
            'type' => 'auth.login',
            'data' => json_encode(['ip' => '127.0.0.1']),
            'activitable_id' => '1',
            'activitable_type' => User::class,
        ]);

        $this->assertDatabaseHas('activities', ['type' => 'auth.login']);
        $this->assertEquals('session_123', $activity->indentifier);
    }

    public function test_user_role_assignment_and_admin_check()
    {
        $role = Role::firstOrCreate(['name' => 'editor']);
        $user = User::create([
            'name' => 'Editor User',
            'email' => 'editor@porteiro.com',
            'password' => 'secret123',
            'admin' => 0,
            'role_id' => $role->id,
        ]);

        $this->assertEquals($role->id, $user->role_id);
        $this->assertEquals(0, $user->admin);

        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@porteiro.com',
            'password' => 'secret123',
            'admin' => 1,
        ]);
        $this->assertEquals(1, $admin->admin);
    }

    public function test_user_meta_creation()
    {
        $user = User::create([
            'name' => 'Meta User',
            'email' => 'meta@porteiro.com',
            'password' => 'secret123',
        ]);

        \Illuminate\Support\Facades\Schema::create('user_meta', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('activation_token')->nullable();
            $table->boolean('marketing')->default(false);
            $table->string('sitecpayment_id')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card_last_four')->nullable();
            $table->boolean('terms_and_cond')->default(true);
            $table->timestamps();
        });

        $meta = \Porteiro\Models\UserMeta::create([
            'user_id' => $user->id,
            'phone' => '11999998888',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('user_meta', ['user_id' => $user->id]);
        $this->assertEquals('11999998888', $meta->phone);
    }
}
