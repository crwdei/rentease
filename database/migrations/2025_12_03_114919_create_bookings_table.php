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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Which rental is being booked
            $table->foreignId('rental_id')
                ->constrained('rentals')
                ->cascadeOnDelete();

            // Client user (from users table, role = 'client')
            $table->foreignId('client_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->dateTime('start_date');
            $table->dateTime('end_date');

            $table->decimal('total_price', 10, 2)->default(0);

            // pending, active, completed, cancelled
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])
                  ->default('pending');

            $table->text('notes')->nullable();

            // <── this is the missing column
            $table->string('valid_id_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
