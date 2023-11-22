<?php

use App\Models\BankGateway;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bank_gateways', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->string('name_fa')->nullable();
            $table->string('status')->default(BankGateway::STATUS_ACTIVE);
            $table->json('config')->nullable();
            $table->unsignedBigInteger('rahkaran_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_gateways');
    }
};
