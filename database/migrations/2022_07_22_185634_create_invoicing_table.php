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
        Schema::create('invoicing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customerid');
            $table->foreign('customerid')->references('id')->on('customers');
            $table->enum('type', ['ordered', 'invoice']);
            $table->integer('vat');
            $table->json('products');
            $table->longtext('notes');
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
        Schema::dropIfExists('invoicing');
    }
};
