<?php

use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('rahkaran_id')->nullable();
            $table->double('amount');
            $table->string('status')->default(Transaction::STATUS_PENDING);
            $table->string('payment_method')->nullable();
            $table->text('description')->nullable();
            $table->string('ip')->nullable();
            $table->string('tracking_code')->nullable();
            $table->string('reference_id')->nullable();

            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('profile_id')->references('id')->on('profiles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
