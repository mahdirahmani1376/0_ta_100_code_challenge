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
        Schema::create('offline_transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('bank_account_id');
            $table->string('status');
            $table->string('payment_method');
            $table->string('tracking_code')->nullable();
            $table->string('mobile')->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_transactions');
    }
};
