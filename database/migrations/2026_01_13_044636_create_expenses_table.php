<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->nullable();
            $table->string('item_name');

            // BOLEH NULL BIAR IMPORT AMAN
            $table->foreignId('expense_category_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('provider')->nullable();
            $table->integer('quantity')->default(1);
            $table->date('expense_date');

            // RUPIAH AMAN
            $table->bigInteger('amount');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
