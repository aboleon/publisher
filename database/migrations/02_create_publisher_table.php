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
        if (!Schema::hasTable('publisher')) {
            Schema::create('publisher', function (Blueprint $table) {

                $table->id();
                $table->foreignId('type')->references('id')->on('publisher_configs')->onUpdate('no action')->onDelete('no action');
                $table->string('taxonomy')->nullable(true)->index();
                $table->unsignedInteger('parent')->nullable(true)->index();
                $table->unsignedInteger('position')->nullable(true)->index();
                $table->string('published', 1)->nullable(true)->index();
                $table->string('access_key')->nullable(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('updated_at');
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
        Schema::dropIfExists('publisher');
    }
}
