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
        if (class_exists(\MakeIT\DiscreteApi\Profile\Models\Profile::class)) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->foreignUuid('organization_id')->nullable()->index()->references('id')->on('organizations')->nullOnDelete();
                $table->foreignUuid('workspace_id')->nullable()->index()->references('id')->on('workspaces')->nullOnDelete();
            });
        } else {
            Schema::create('profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->foreignUuid('organization_id')->nullable()->index()->references('id')->on('organizations')->nullOnDelete();
                $table->foreignUuid('workspace_id')->nullable()->index()->references('id')->on('workspaces')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (class_exists(\MakeIT\DiscreteApi\Profile\Models\Profile::class)) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->dropColumn('organization_id');
                $table->dropColumn('workspace_id');
            });
        } else {
            Schema::dropIfExists('profiles');
        }
    }
};
