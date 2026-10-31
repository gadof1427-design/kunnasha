<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_mentions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quote_id')
                ->constrained('quotes')
                ->cascadeOnDelete();

            $table->foreignId('mentioned_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'quote_id',
                'mentioned_user_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_mentions');
    }
};
