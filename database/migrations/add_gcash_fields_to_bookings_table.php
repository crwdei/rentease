<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('gcash_reference')->nullable()->after('notes');
            $table->decimal('gcash_amount', 10, 2)->nullable()->after('gcash_reference');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['gcash_reference', 'gcash_amount']);
        });
    }
};