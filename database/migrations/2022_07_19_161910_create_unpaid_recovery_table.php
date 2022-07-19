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
        Schema::create('unpaid_recovery', function (Blueprint $table) {
            $table->id();
            $table->integer('customerid');
            $table->string('ref');
            $table->decimal('amount', 10, 2);
            $table->longtext('attachment')->nullable();
            $table->longtext('notes')->nullable();
            $table->enum('process', ['0', '1', '2', '3']);
            $table->enum('status', ['ended', 'in_progress', 'pending']);
            $table->timestamp('factured_at');
            $table->timestamp('echance_at');
            $table->timestamp('last_relaunch')->nullable();
            $table->timestamp('next_relaunch')->nullable();
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
        Schema::dropIfExists('unpaid_recovery');
    }
};
