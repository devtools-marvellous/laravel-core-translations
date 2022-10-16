<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('group');
            $table->index('group');
            $table->string('key');
            $table->unique(['group', 'key']);
            $table->text('text');
            $table->string('translatable_type')->nullable()->default(NULL);
            $table->string('translatable_id')->nullable()->default(NULL);
            $table->string('translatable_field')->nullable()->default(NULL);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('translations');
    }
}