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
        if (!Schema::hasTable('publisher_content')) {
            Schema::create('publisher_content', function (Blueprint $table) {

                $table->id();
                $table->foreignId('node_id')->references('id')->on('publisher_structure')->onUpdate('no action')->onDelete('delete');
                $table->text('content')->nullable(true);
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
        Schema::dropIfExists('publisher_content');
    }
}

