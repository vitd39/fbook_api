<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteAllViewCounterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('counter_page_visitors');
        Schema::drop('counter_visitors');
        Schema::drop('counter_pages');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('counter_pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('page')->unique();
        });

        Schema::create('counter_visitors', function (Blueprint $table) {
           $table->bigIncrements('id');
           $table->string('visitor')->unique();
       });

       Schema::create('counter_page_visitors', function (Blueprint $table) {
            $table->bigInteger('page_id')->unsigned()->index();
            $table->foreign('page_id')->references('id')->on('counter_pages')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('visitor_id')->unsigned()->index();
            $table->foreign('visitor_id')->references('id')->on('counter_visitors')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->timestamp('created_at');
        });
    }
}
