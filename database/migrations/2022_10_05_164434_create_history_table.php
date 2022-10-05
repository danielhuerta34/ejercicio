<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTable extends Migration
{

    protected $connection = 'pgsql';

    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->string('usd');
            $table->date('date');
            $table->time('hour');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history');
    }
}
