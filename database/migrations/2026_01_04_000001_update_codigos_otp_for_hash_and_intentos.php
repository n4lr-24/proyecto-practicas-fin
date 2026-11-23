<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('codigos_otp', function (Blueprint $table) {
            $table->string('codigo', 255)->change();
            $table->unsignedTinyInteger('intentos')->default(0)->after('codigo');
        });
    }

    public function down(): void
    {
        Schema::table('codigos_otp', function (Blueprint $table) {
            $table->dropColumn('intentos');
            $table->string('codigo', 6)->change();
        });
    }
};
