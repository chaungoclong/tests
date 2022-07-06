<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('permissions.tables.roles', 'roles'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->boolean('status')->default(true);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create(config('permissions.tables.permissions', 'permissions'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('module', 100);
            $table->string('action_name', 100);
            $table->string('action', 255);
            $table->boolean('status')->default(true);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create(
            config('permissions.tables.permission_role', 'permission_role'),
            function (Blueprint $table) {
                $table->unsignedInteger('permission_id');
                $table->unsignedInteger('role_id');

                $table->primary(['permission_id', 'role_id']);
            }
        );

        Schema::create(config('permissions.tables.role_user', 'role_user'), function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('user_id');

            $table->primary(['role_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('permissions.tables.roles', 'roles'));
        Schema::dropIfExists(config('permissions.tables.permissions', 'permissions'));
        Schema::dropIfExists(config('permissions.tables.permission_role', 'permission_role'));
        Schema::dropIfExists(config('permissions.tables.role_user', 'role_user'));
    }
}
