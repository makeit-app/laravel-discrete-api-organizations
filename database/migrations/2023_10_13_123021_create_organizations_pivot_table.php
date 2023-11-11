<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pivot_organizations_users', function (Blueprint $table) {
            $table->foreignUuid('organization_id')->index()->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreignUuid('user_id')->index()->references('id')->on('users')->cascadeOnDelete();
            $table->integer('role')->index()->nullable();
            $table->unique(['organization_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_organizations_users');
    }
};
