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
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('bank_account_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->double('amount');
            $table->string('status');
            $table->string('payment_method');
            $table->string('tracking_code')->nullable();
            $table->string('mobile')->nullable();
            $table->text('description')->nullable();

            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
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
