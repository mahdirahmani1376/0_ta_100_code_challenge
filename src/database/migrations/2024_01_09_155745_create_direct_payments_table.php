<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direct_payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('profile_id');
            $table->string('status')->default(\App\Models\DirectPayment::STATUS_INIT);
            $table->string('provider');
            $table->json('config')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_payments');
    }
};
