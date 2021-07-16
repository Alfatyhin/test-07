<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casts', function (Blueprint $table) {
            $table->id();
            $table->string('imdb_name_id')->unique();
            $table->string('name');
            $table->string('height');
            $table->text('bio');
            $table->string('date_of_birth');
            $table->text('place_of_birth');
            $table->string('children');
            $table->boolean('is_usa')->default(false);
            $table->boolean('is_europe')->default(false);
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
        Schema::dropIfExists('casts');
    }
}
