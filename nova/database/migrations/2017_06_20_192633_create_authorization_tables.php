<?php

use Nova\Authorize\Role;
use Nova\Authorize\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorizationTables extends Migration
{
	public function up()
	{
		Schema::create('roles', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->unique();
			$table->timestamps();
		});

		Schema::create('permissions', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('key')->unique();
			$table->timestamps();
		});

		Schema::create('users_roles', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('role_id');

			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('role_id')->references('id')->on('roles');
		});

		Schema::create('permissions_roles', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('permission_id');
			$table->unsignedInteger('role_id');

			$table->foreign('permission_id')->references('id')->on('permissions');
			$table->foreign('role_id')->references('id')->on('roles');
		});

		$this->seed();
	}

	public function down()
	{
		Schema::dropIfExists('permissions_roles');
		Schema::dropIfExists('users_roles');
		Schema::dropIfExists('permissions');
		Schema::dropIfExists('roles');
	}

	public function seed()
	{
		$this->permissions();

		$this->roles();

		$this->roleAssignments();
	}

	protected function permissions()
	{
		// Create permissions
		$permissions = [
			['name' => "Create roles", 'key' => "role.create"],
			['name' => "Update roles", 'key' => "role.update"],
			['name' => "Delete roles", 'key' => "role.delete"],

			['name' => "Create permissions", 'key' => "permission.create"],
			['name' => "Update permissions", 'key' => "permission.update"],
			['name' => "Delete permissions", 'key' => "permission.delete"],

			['name' => "Create users", 'key' => "user.create"],
			['name' => "Update users", 'key' => "user.update"],
			['name' => "Delete users", 'key' => "user.delete"],

			['name' => "Create departments", 'key' => "dept.create"],
			['name' => "Update departments", 'key' => "dept.update"],
			['name' => "Delete departments", 'key' => "dept.delete"],
		];

		foreach ($permissions as $permission) {
			Permission::create($permission);
		}
	}

	protected function roles()
	{
		// Create roles
		$roles = [
			['name' => 'System Admin'],
			['name' => 'Active User']
		];

		foreach ($roles as $role) {
			Role::create($role);
		}
	}

	protected function roleAssignments()
	{
		$assignments = [
			"System Admin" => ['role.create', 'role.update', 'role.delete', 'permission.create', 'permission.update', 'permission.delete', 'user.create', 'user.update', 'user.delete', 'dept.create', 'dept.delete', 'dept.update'],
			"Active User" => [],
		];

		foreach ($assignments as $roleName => $permissionKeys) {
			// Find the role
			$role = Role::name($roleName)->first();

			if ($role) {
				// Find the permission IDs
				$permissionIds = Permission::whereIn('key', $permissionKeys)
					->get()
					->map(function ($p) {
						return $p->id;
					});

				if ($permissionIds->count() > 0) {
					// Associate the permissions with the role
					$role->permissions()->attach($permissionIds->all());
				}
			}
		}
	}
}
