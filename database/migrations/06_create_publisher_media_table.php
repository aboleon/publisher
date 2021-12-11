<?php declare(strict_types = 1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('publisher_media')) {
            Schema::create('publisher_media', function (Blueprint $table) {

                $table->id();
                $table->foreignId('node_id')->references('id')->on('publisher_structure')->onUpdate('no action')->onDelete('delete');
                $table->string('content')->nullable(true);
                $table->unsignedInteger('position')->default(0)->index();

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
        Schema::dropIfExists('publisher_media');
    }
}
