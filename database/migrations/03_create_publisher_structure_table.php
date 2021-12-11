<?php declare(strict_types = 1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('publisher_structure')) {
            Schema::create('publisher_structure', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pages_id')->references('id')->on('publisher_pages')->onUpdate('no action')->onDelete('delete');
                $table->string('uuid', 4)->index();
                $table->text('title')->nullable(true);
                $table->unsignedInteger('position');
                $table->string('type')->index();
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
        Schema::dropIfExists('publisher_structure');
    }
}

