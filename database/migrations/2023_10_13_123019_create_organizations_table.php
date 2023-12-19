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
            $table->integer('workspace_slots')->index()->default(config('discreteapiorganizations.limit.workspaces'));
            $table->integer('member_slots')->index()->default(config('discreteapiorganizations.limit.members'));
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->boolean('is_personal')->index()->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
