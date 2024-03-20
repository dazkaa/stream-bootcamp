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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages'); // membuat foreign key -> integer
            $table->foreignId('user_id')->constrained('users'); // membuat foreign key
            $table->float('amount');
            $table->string('transaction_code');
            $table->string('status'); //failed, success
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
