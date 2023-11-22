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
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->double('amount')->comment('can be negative');
            $table->text('description')->nullable();

            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('wallet_id')->references('id')->on('wallets');
            $table->foreign('profile_id')->references('id')->on('profiles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
