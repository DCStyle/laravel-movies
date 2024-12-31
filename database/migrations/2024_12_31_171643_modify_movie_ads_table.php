<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('movie_ads', function (Blueprint $table) {
            $table->timestamp('last_shown_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('movie_ads', function (Blueprint $table) {
            $table->dropColumn('last_shown_at');
        });
    }
};
