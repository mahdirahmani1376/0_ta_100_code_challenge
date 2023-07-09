<?php

use App\Models\InvoiceNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_numbers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('invoice_id')->nullable();
            $table->string('invoice_number');
            $table->string('fiscal_year');
            $table->string('status')->default(InvoiceNumber::STATUS_UNUSED);
            $table->string('type')->default(InvoiceNumber::TYPE_PAID);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_numbers');
    }
};
