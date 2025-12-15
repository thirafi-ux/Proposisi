<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogicExpressionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logic_expressions', function (Blueprint $table) {
            $table->id();
            $table->text('expression');
            $table->json('variables')->nullable();
            $table->string('hash')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('variable_count')->default(0);
            $table->timestamps();
            
            $table->index('hash');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logic_expressions');
    }
}
