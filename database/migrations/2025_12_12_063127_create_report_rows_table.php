<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->integer('row_number'); // baris ke berapa
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_rows');
    }
};
