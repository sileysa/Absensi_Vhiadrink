<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->time('checkout_start')->nullable()->after('checkin_end');
            $table->time('checkout_end')->nullable()->after('checkout_start');
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn([
                'checkout_start',
                'checkout_end',
            ]);
        });
    }
};