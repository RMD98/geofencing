<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        //
        Schema::create('koord_point', function (Blueprint $table) {
            $table->string('id');
            $table->string('user_id');
            $table->string('latitude');
            $table->string('longitude');
            // $table->geography('coordinates', subtype: 'point', srid: 4326);
            $table->string('status')->default('not checked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
