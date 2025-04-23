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
        Schema::create('log_activity', function (Blueprint $table) {
            $table->char('method', 10);
            $table->string('agent', 255);
            $table->char('ip', 25);
            $table->dateTime('tanggal')->useCurrent();
            $table->text('list');
        });

        Schema::create('log_database', function (Blueprint $table) {
            $table->char('method', 10);
            $table->string('agent', 255);
            $table->char('ip', 25);
            $table->dateTime('tanggal')->useCurrent();
            $table->text('list');
            $table->string('table', 80);
            $table->json('data');
            $table->integer('id_table');
        });

        Schema::create('log_error', function (Blueprint $table) {
            $table->char('method', 10);
            $table->string('agent', 255);
            $table->char('ip', 25);
            $table->dateTime('tanggal')->useCurrent();
            $table->text('path');
            $table->text('list');
            $table->text('error');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_error');
        Schema::dropIfExists('log_database');
        Schema::dropIfExists('log_activity');
    }
};
