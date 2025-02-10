<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeExplanationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_explanations', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->date('date_issued');
            $table->string('issued_by');
            $table->string('nte_code');
            $table->string('nte_file_path');
            $table->string('nte_file_name');
            $table->string('nte_file_type');
            $table->date('nte_date_upload')->nullable();
            $table->string('violation');
            $table->string('explanation_file_path')->nullable();
            $table->string('explanation_file_name')->nullable();
            $table->string('explanation_file_type')->nullable();
            $table->date('explanation_date')->nullable();
            $table->date('explanation_date_upload')->nullable();
            $table->string('remarks')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('employee_explanations');
    }
}
