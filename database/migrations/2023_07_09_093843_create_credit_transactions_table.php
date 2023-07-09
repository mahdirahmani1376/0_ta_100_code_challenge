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
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('wallet_id');
            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('admin_id')->nullable();
            $table->float('amount')->comment('can be negative');
            $table->text('description')->nullable();
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
