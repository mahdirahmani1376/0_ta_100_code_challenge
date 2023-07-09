<?php

use App\Models\ClientCashout;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_cashouts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('client_bank_account_id');
            $table->unsignedBigInteger('zarinpal_payout_id')->nullable();
            $table->unsignedInteger('admin_id');
            $table->float('amount');
            $table->text('admin_note')->nullable();
            $table->string('status')->default(ClientCashout::STATUS_PENDING);
            $table->boolean('rejected_by_bank')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_cashouts');
    }
};
