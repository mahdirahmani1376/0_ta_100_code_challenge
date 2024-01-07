<?php

use App\Models\MoadianLog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moadian_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('invoice_id');
            $table->string('status')->default(MoadianLog::STATUS_INIT);
            $table->text('reference_code')->nullable();
            $table->string('tax_id')->nullable();
            $table->text('error')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moadian_logs');
    }
};
