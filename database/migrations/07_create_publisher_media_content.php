<?php declare(strict_types = 1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaContentDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('publisher_media_content')) {
            Schema::create('publisher_media_content', function (Blueprint $table) {

                $table->id();
                $table->foreignId('media_id')->references('id')->on('publisher_media')->onUpdate('no action')->onDelete('delete');
                $table->text('content')->nullable(true);
                $table->string('locale', 2)->default('fr')->index();

                $table->foreign('media_content_id')->references('id')->on('publisher_media_content')->onDelete('cascade');

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
        Schema::dropIfExists('publisher_media_content');
    }
}
