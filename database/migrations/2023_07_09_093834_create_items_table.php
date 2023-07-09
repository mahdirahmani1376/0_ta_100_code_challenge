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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('invoiceable_id');
            $table->string('invoiceable_type');
            $table->float('amount')
                ->comment('can be negative');
            $table->float('discount')
                ->nullable()
                ->comment('non-computation field, serves only as to show on reports');
            $table->timestamp('from_date')->nullable();
            $table->timestamp('to_date')->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
