<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TodoList extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'todo_lists';
  protected $fillable = ['title', 'detail', 'status', 'user_id'];
}
