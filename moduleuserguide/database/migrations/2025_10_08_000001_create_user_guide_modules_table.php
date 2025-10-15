<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('user_guide_modules')) {
            Schema::create('user_guide_modules', function (Blueprint $table) {
                $table->id();
                $table->string('name', 256);
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('user_guide_modules');
    }
};
