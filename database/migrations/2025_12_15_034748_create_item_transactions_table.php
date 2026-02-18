<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');

            // ⬇️ UBAH DI SINI
            $table->bigInteger('price')->default(0);
            $table->bigInteger('total')->default(0);

            $table->date('tanggal');
            $table->string('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_transactions');
    }
};
