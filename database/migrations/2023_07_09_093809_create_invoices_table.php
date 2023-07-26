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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('rahkaran_id')->nullable();
            $table->string('payment_method');
            $table->double('balance')->default(0);
            $table->double('total')->default(0);
            $table->double('sub_total')->default(0);
            $table->unsignedInteger('tax_rate')->default(0);
            $table->double('tax')->default(0);
            $table->string('status');
            $table->boolean('is_mass_payment')->default(false);
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->boolean('is_credit')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
