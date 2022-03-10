<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodoListsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('todo_lists', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnUpdate()
        ->cascadeOnDelete();
      $table->string('title', 100);
      $table->text('detail')->nullable();
      $table->tinyInteger('status')->comment("1 = waiting, 2 = on process, 3 = done");
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('todo_lists');
  }
}
