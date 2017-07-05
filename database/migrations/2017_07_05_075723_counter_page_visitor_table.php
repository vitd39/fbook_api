<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CounterPageVisitorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('counter_page_visitors');
    }
}
