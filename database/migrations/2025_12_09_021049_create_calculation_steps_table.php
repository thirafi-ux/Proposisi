<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalculationStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calculation_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truth_table_id')->constrained()->onDelete('cascade');
            $table->integer('step_number');
            $table->string('sub_expression');
            $table->boolean('result');
            $table->string('description')->nullable();
            $table->json('variables_values')->nullable();
            $table->timestamps();
            
            $table->index(['truth_table_id', 'step_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calculation_steps');
    }
}
