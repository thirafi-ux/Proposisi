<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTruthTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('truth_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expression_id')->constrained('logic_expressions')->onDelete('cascade');
            $table->json('table_data');
            $table->text('simplified_form')->nullable();
            $table->boolean('is_tautology')->default(false);
            $table->boolean('is_contradiction')->default(false);
            $table->boolean('is_contingent')->default(true);
            $table->integer('true_count')->default(0);
            $table->integer('false_count')->default(0);
            $table->float('truth_percentage')->default(0);
            $table->timestamps();
            
            $table->index('expression_id');
            $table->index('is_tautology');
            $table->index('is_contradiction');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('truth_tables');
    }
}
