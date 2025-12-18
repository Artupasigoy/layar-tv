<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('processed_path')->nullable()->after('file_path');
            $table->string('thumbnail_path')->nullable()->after('processed_path');
            $table->string('display_path')->nullable()->after('thumbnail_path');
            $table->integer('duration')->nullable()->after('display_path');
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])
                ->default('completed')->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['processed_path', 'thumbnail_path', 'display_path', 'duration', 'processing_status']);
        });
    }
};
