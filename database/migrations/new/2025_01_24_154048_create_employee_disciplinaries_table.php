<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDisciplinariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_disciplinaries', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->date('date_issued');
            $table->string('nte_code');
            $table->string('offense_code');
            $table->string('offense');
            $table->string('offense_type');
            $table->string('disciplinary_action');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->date('file_date_upload')->nullable();
            $table->string('series')->nullable();
            $table->string('remarks')->nullable();
            $table->date('transmit_date')->nullable();
            $table->date('return_date')->nullable();
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
        Schema::dropIfExists('employee_disciplinaries');
    }
}
