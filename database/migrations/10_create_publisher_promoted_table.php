<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('publisher_promoted')) {
            Schema::create('publisher_promoted', function (Blueprint $table) {
                $table->foreignId('pages_id')->references('id')->on('publisher_pages')->onUpdate('no action')->onDelete('delete');
                $table->unsignedInteger('position')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publisher_promoted');
    }
}
