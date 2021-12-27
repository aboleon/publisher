<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publisher_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publisher_id')->constrained('publisher')->onDelete('cascade');
            $table->string('name')->index();
            $table->json('title')->nullable(true);
            $table->json('text')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meta_forms');
    }
}
