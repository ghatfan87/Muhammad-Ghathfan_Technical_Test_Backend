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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_name');
            $table->string('email');    
            $table->string('phone_number');
            $table->enum('survey_status', ['Requested', 'Approved', 'Rejected']);
            $table->enum('sales_type', ['residential', 'commercial', 'both']);
            $table->text('notes');
            $table->unsignedBigInteger('assigned_to');
            $table->string('image')->nullable();
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
        Schema::dropIfExists('leads');
    }
};
