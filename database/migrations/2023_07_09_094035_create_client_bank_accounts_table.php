<?php

use App\Models\ClientBankAccount;
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
        Schema::create('client_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('client_id');
            $table->unsignedBigInteger('zarinpal_bank_acount_id')->nullable();
            $table->string('bank_name');
            $table->string('owner_name');
            $table->string('sheba_number');
            $table->string('account_number')->nullable();
            $table->string('card_number')->nullable();
            $table->string('status')->default(ClientBankAccount::STATUS_PENDING);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_bank_accounts');
    }
};
