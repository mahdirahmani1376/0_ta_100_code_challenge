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
            $table->unsignedInteger('client_id');
            $table->unsignedBigInteger('rahkaran_id')->nullable();
            $table->string('payment_method');
            $table->float('total');
            $table->float('sub_total');
            $table->unsignedInteger('tax_rate')->default(0);
            $table->float('tax')->default(0);
            $table->string('status');
            $table->boolean('is_mass_payment')->default(false);
            $table->unsignedInteger('admin_id')->nullable();
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
