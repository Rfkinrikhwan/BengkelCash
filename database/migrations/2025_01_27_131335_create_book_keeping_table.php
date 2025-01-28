<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_keepings', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->text('note');
            $table->bigInteger('debit')->default(0);
            $table->bigInteger('credit')->default(0);
            $table->bigInteger('saldo')->default(0);
            $table->string('method_payment');
            $table->enum('type', ['debit', 'credit']);
            $table->timestamp('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_keepings');
    }
};
