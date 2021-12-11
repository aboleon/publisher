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
        if (!Schema::hasTable('publisher_meta')) {
            Schema::create('publisher_meta', function (Blueprint $table) {

                $table->id();
                $table->foreignId('pages_id')->references('id')->on('publisher_pages')->onUpdate('no action')->onDelete('delete');
                $table->string('title', 512)->nullable(true)->index();
                $table->text('meta_title')->nullable(true);
                $table->text('meta_keywords')->nullable(true);
                $table->text('meta_description')->nullable(true);
                $table->text('nav_title')->nullable(true);
                $table->string('url', 512)->nullable(true)->index();;
                $table->string('locale', 2)->default('fr')->index();
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
        Schema::dropIfExists('publisher_meta');
    }
}

