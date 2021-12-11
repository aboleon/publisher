<?php declare(strict_types = 1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('publisher_configs')) {
            Schema::create('publisher_configs', function (Blueprint $table) {

                $table->id();
                $table->string('type')->index();
                $table->binary('config');
                $table->timestamps();
                $table->index('created_at');
                $table->index('deleted_at');

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
        Schema::dropIfExists('publisher_configs');
    }
}
