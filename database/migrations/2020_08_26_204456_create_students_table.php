<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table)
        {
            $table->id();
            $table->string('last_name', 100);
            $table->string('name', 100);
            $table->string('patronymic', 100)->nullable();
            $table->string('email', 100)->unique();
            $table->string('phone_number', 50)->unique();
            $table->string('country', 120);
            $table->string('postal_code', 6);
            $table->string('region', 120);
            $table->string('area', 120)->nullable();
            $table->string('city', 120);
            $table->string('street', 120);
            $table->string('house', 50);
            $table->string('block', 50)->nullable();
            $table->string('flat', 50);
            $table->string('geo_lat', 12)->nullable();
            $table->string('geo_lon', 12)->nullable();
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
        Schema::dropIfExists('students');
    }
}
