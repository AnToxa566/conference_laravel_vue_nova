<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->dropColumn('host_id');
            $table->dropColumn('topic');
            $table->dropColumn('type');
            $table->dropColumn('timezone');
            $table->dropColumn('start_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->string('uuid');
            $table->string('host_id');
            $table->string('topic');
            $table->integer('type');
            $table->string('timezone');
            $table->dateTime('start_time')->default(date('Y-m-d H:i'));
        });
    }
};
