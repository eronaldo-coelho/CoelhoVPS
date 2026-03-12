<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sistemas', function (Blueprint $table) {
            $table->id();
            $table->uuid('image_id')->unique();
            $table->string('tenant_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('url')->nullable();
            $table->integer('size_mb')->nullable();
            $table->integer('uploaded_size_mb')->nullable();
            $table->string('os_type')->nullable();
            $table->string('version')->nullable();
            $table->string('format')->nullable();
            $table->string('status')->nullable();
            $table->boolean('standard_image')->default(false);
            $table->timestamp('creation_date')->nullable();
            $table->timestamp('last_modified_date')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sistemas');
    }
};
