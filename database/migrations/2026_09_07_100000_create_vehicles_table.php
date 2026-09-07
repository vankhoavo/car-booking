<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->string('model');
            $table->string('type')->nullable();
            $table->unsignedTinyInteger('seats');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->enum('status', ['available', 'maintenance', 'inactive'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
