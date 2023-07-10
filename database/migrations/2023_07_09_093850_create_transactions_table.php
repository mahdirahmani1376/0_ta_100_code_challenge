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
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('invoice_id');
            $table->unsignedBigInteger('rahkaran_id')->nullable();
            $table->float('amount')->comment('can be negative');
            $table->string('status')->default(Transaction::STATUS_PENDING);
            $table->text('description')->nullable();
            $table->string('ip')->nullable();
            $table->string('tracking_code')->nullable();
            $table->string('reference_id')->nullable();
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
