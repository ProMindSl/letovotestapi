<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStudentsTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table)
        {
            $table->string('country', 120)->nullable()->change();;
            $table->string('postal_code', 6)->nullable()->change();;
            $table->string('region', 120)->nullable()->change();;
            $table->string('city', 120)->nullable()->change();;
            $table->string('street', 120)->nullable()->change();;
            $table->string('house', 50)->nullable()->change();;
            $table->string('flat', 50)->nullable()->change();;

            $table->string('city_type', 10)->nullable();
            $table->integer('qc_address');
            $table->string('full_address', 500);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table)
        {
            $table->string('country', 120)->change();;
            $table->string('postal_code', 6)->change();;
            $table->string('region', 120)->change();;
            $table->string('city', 120)->change();;
            $table->string('street', 120)->change();;
            $table->string('house', 50)->change();;
            $table->string('flat', 50)->change();;

            $table->dropColumn('city_type');
            $table->dropColumn('qc_address');
            $table->dropColumn('full_address');
        });
    }
}
