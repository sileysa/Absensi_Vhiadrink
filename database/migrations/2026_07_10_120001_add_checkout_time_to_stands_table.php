<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->string('checkout_time')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->dropColumn('checkout_time');
        });
    }
};
