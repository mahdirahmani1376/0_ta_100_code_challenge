<?php

use App\Models\BankAccount;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('title');
            $table->string('status')->default(BankAccount::STATUS_ACTIVE);
            $table->integer('display_order')->default(0);
            $table->string('sheba_number')->nullable();
            $table->string('account_number')->nullable();
            $table->string('card_number')->nullable();
            $table->unsignedBigInteger('rahkaran_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
