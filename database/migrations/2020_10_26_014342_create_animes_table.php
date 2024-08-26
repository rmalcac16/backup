<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 220);
            $table->string('name_alternative', 350)->nullable();
            $table->string('slug', 220)->unique();
            $table->string('banner', 220)->nullable();
            $table->string('poster', 220)->nullable();
            $table->text('overview')->nullable();
            $table->date('aired')->nullable();
            $table->string('type', 7)->default('Tv');
            $table->boolean('status')->default(0);
            $table->string('premiered', 6)->nullable();
            $table->char('broadcast', 1)->nullable();
            $table->string('genres')->nullable();
            $table->string('rating', 150)->nullable();
            $table->integer('popularity')->default(0);
            $table->double('vote_average')->default(0);
            $table->string('slug_flv', 220)->nullable();
			$table->timestamp('created_at')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('animes');
    }
}
