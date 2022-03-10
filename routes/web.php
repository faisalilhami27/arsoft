<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TodoListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthenticatedSessionController::class, 'create']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::group(['prefix' => 'todo-list', 'middleware' => ['auth']], function () {
  Route::get('/', [TodoListController::class, 'index'])->name('todo-list');
  Route::get('/edit', [TodoListController::class, 'edit'])->name('todo-list.edit');
  Route::post('/json', [TodoListController::class, 'datatable'])->name('todo-list.json');
  Route::post('/store', [TodoListController::class, 'store'])->name('todo-list.store');
  Route::post('/mark-as-on-process', [TodoListController::class, 'markAsOnProcess'])->name('todo-list.mark-as-on-process');
  Route::post('/mark-as-done', [TodoListController::class, 'markAsOnDone'])->name('todo-list.mark-as-done');
  Route::put('/update', [TodoListController::class, 'update'])->name('todo-list.update');
  Route::delete('/destroy', [TodoListController::class, 'destroy'])->name('todo-list.destroy');
});
