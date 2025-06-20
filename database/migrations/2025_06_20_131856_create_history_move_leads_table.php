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
        Schema::create('history_move_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leads_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('from_sales_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('to_sales_id')->constrained('sales')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_move_leads');
    }
};
