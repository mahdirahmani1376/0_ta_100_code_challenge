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
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('zarinpal_bank_account_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('sheba_number')->nullable();
            $table->string('account_number')->nullable();
            $table->string('card_number')->nullable();
            $table->string('status')->default(ClientBankAccount::STATUS_PENDING);

            $table->foreign('profile_id')->references('id')->on('profiles');
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
