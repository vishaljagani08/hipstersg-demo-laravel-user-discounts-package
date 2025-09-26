<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 8, 2); // percentage (0-100) or fixed amount
            $table->boolean('active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('stacking_priority')->default(0);
            $table->unsignedInteger('per_user_cap')->default(0); // 0 = unlimited
            $table->unsignedInteger('global_cap')->default(0); // 0 = unlimited
            $table->unsignedBigInteger('global_usage')->default(0);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
