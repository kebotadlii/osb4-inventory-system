<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_cells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_row_id')->constrained()->onDelete('cascade');
            $table->foreignId('report_title_id')->constrained()->onDelete('cascade');
            $table->text('value')->nullable(); // isi sel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_cells');
    }
};
