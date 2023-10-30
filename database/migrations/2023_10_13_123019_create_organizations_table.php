<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MakeIT\Utils\Sorter;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer(Sorter::FIELD)->index()->default(1);
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->boolean('is_personal')->index()->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('organizations_members', function (Blueprint $table) {
            $table->foreignUuid('organization_id')->index()->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreignUuid('user_id')->index()->references('id')->on('users')->cascadeOnDelete();
            $table->foreignUuid('invited_by')->index()->references('id')->on('users')->cascadeOnDelete();
            $table->timestamp('invited_at')->index()->nullable();
            $table->integer('invite_role')->index()->nullable();
            $table->timestamp('invite_confirmed_at')->index()->nullable();
            $table->timestamp('updated_at')->index()->nullable();
            $table->foreignUuid('updated_by')->index()->references('id')->on('users')->cascadeOnDelete();
            $table->integer('role')->index()->nullable();
            $table->unique(['organization_id', 'user_id']);
        });
        Schema::create('workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer(\MakeIT\Utils\Sorter::FIELD)->index()->default(0);
            $table->foreignUuid('organization_id')->index()->references('id')->on('organizations')->cascadeOnDelete();
            $table->string('title')->index();
            $table->boolean('is_default')->index()->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('profiles', function (Blueprint $table) {
            $table->foreignUuid('organization_id')->nullable()->index()->references('id')->on('organizations')->nullOnDelete();
            $table->foreignUuid('workspace_id')->nullable()->index()->references('id')->on('workspaces')->nullOnDelete();
        });
        Schema::create('user_organization_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->index()->references('id')->on('users')->cascadeOnDelete();
            $table->foreignUuid('organization_id')->index()->references('id')->on('organizations')->cascadeOnDelete();
            $table->integer('slots')->index()->default(config('discreteapiorganizations.limit.organizations'));
            $table->timestamps();
        });
        Schema::create('organization_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->index()->references('id')->on('organizations')->cascadeOnDelete();
            $table->integer('workspace_slots')->index()->default(config('discreteapiorganizations.limit.workspaces'));
            $table->integer('member_slots')->index()->default(config('discreteapiorganizations.limit.members'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_slots');
        Schema::dropIfExists('user_organization_slots');
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('organization_id');
            $table->dropColumn('workspace_id');
        });
        Schema::dropIfExists('workspaces');
        Schema::dropIfExists('organizations_members');
        Schema::dropIfExists('organizations');
    }
};
