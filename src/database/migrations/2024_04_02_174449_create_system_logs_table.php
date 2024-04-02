<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('method');
            $table->string('endpoint');
            $table->string('request_url');
            $table->text('request_body');
            $table->text('request_header')->nullable();
            $table->string('provider');
            $table->text('response_header')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('response_status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
