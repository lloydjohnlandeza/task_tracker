<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task');
            $table->integer('parent_id')->default('0');
            $table->integer('task_status_id');
            $table->integer('user_id');
            $table->integer('order_id')->nullable();
            $table->index(['user_id', 'order_id', 'task_status_id', 'parent_id']);
            $table->softDeletes();
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
        Schema::dropIfExists('tasks');
    }
}
