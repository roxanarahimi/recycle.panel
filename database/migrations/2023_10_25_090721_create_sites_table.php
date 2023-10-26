<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->integer('paper')->default(0);
            $table->integer('cardboard')->default(0);
            $table->integer('paper_cardboard_weight')->default(0);
            $table->integer('paper_cardboard_price')->default(0);
            $table->integer('glass')->default(0);
            $table->integer('plastic')->default(0);
            $table->integer('glass_plastic_weight')->default(0);
            $table->integer('glass_plastic_price')->default(0);
            $table->integer('slider_delay')->default(0);
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
        Schema::dropIfExists('sites');
    }
};
