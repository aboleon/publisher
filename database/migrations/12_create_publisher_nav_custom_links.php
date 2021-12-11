<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavCustomLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publisher_nav_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nav_id')->references('id')->on('publisher_nav')->onDelete('cascade');
            $table->string('locale',2)->index();
            $table->string('title');
            $table->string('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publisher_nav_links');
    }
}
