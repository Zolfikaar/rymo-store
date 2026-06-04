<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->change();
        });

        DB::table('orders')
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->update(['status' => 'completed']);

        DB::table('orders')
            ->where('status', 'cancelled')
            ->update(['status' => 'canceled']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('orders')
            ->where('status', 'completed')
            ->update(['status' => 'delivered']);

        DB::table('orders')
            ->where('status', 'canceled')
            ->update(['status' => 'cancelled']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])
                ->default('pending')
                ->change();
        });
    }
};
