<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('discount_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('discount_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount_before', 12, 2);
            $table->decimal('amount_after', 12, 2);
            $table->string('idempotency_key')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('success')->default(true);
            $table->timestamps();


            $table->unique(['user_id', 'idempotency_key']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('discount_audits');
    }
};
