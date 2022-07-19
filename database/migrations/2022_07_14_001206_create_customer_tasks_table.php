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
        Schema::create('customer_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customerid');
            $table->foreign('customerid')->references('id')->on('customers');
            $table->unsignedBigInteger('attributedto');
            $table->foreign('attributedto')->references('id')->on('users')->nullable();
            $table->longtext('content');
            $table->enum('status', ['ended', 'in_progress', 'pending']);
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
        Schema::dropIfExists('customer_tasks');
    }
};
