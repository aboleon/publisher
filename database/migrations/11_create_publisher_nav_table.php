<?php declare(strict_types = 1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('publisher_nav')) {
            Schema::create('publisher_nav', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pages_id')->references('id')->on('publisher_pages')->onUpdate('no action')->onDelete('delete');
                $table->string('is_primary', 1)->nullable(true)->index();
                $table->unsignedInteger('position')->index();
                $table->string('logged', 1)->nullable(true)->default(null);
                $table->unsignedBigInteger('parent')->nullable(true)->default(null);
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
        Schema::dropIfExists('publisher_nav');
    }
}
