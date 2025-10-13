<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('user_guides')) {
            Schema::create('user_guides', function (Blueprint $table) {
                $table->id();
                $table->foreignId('module_id')->constrained('user_guide_modules')->onDelete('cascade');
                $table->string('name', 256);
                // $table->text('description')->nullable();
                $table->longText('description')->nullable();
                $table->json('files')->nullable();
                $table->json('urls')->nullable();
                $table->timestamps();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('user_guides');
    }
};
