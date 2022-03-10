<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoListRequest;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TodoListController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
   */
  public function index()
  {
    $title = 'To Do List';
    return view('todo-list.index', compact('title'));
  }

  /**
   * Show to do list data using datatable.
   *
   * @return \Illuminate\Http\Response
   * @throws \Exception
   */
  public function datatable(Request $request)
  {
    $status = $request->status;
    $todoLists = TodoList::query();

    if (!empty($status)) {
      $todoLists->where('status', $status);
    }

    $todoLists->get();
    return DataTables::of($todoLists)
      ->addIndexColumn()
      ->addColumn('status', function ($query) {
        if ($query->status == 1) {
          $status = '<span class="btn text-white btn-info btn-sm">Waiting</span>';
        } elseif ($query->status == 2) {
          $status = '<span class="btn text-white btn-warning btn-sm">On Process</span>';
        } else {
          $status = '<span class="btn text-white btn-primary btn-sm">Done</span>';
        }

        return $status;
      })
      ->addColumn('action', function ($query) {
        if ($query->status == 1) {
          return '<a class="btn btn-primary btn-sm" onclick="editData(' . $query->id . ')"><i class="fa fa-pencil"></i></a> |
                  <a class="btn btn-warning btn-sm" onclick="markAsOnProcess(' . $query->id . ')"><i class="fa fa-refresh"></i></a> |
                  <a class="btn btn-danger btn-sm" onclick="deleteData(' . $query->id . ')"><i class="fa fa-trash"></i></a>';
        } else if ($query->status == 2) {
          return '<a class="btn btn-primary btn-sm" onclick="editData(' . $query->id . ')"><i class="fa fa-pencil"></i></a> |
                  <a class="btn btn-info btn-sm" onclick="markAsDone(' . $query->id . ')"><i class="fa-solid fa-circle-check"></i></a> |
                  <a class="btn btn-danger btn-sm" onclick="deleteData(' . $query->id . ')"><i class="fa fa-trash"></i></a>';
        } else {
          return '<a class="btn btn-primary btn-sm" onclick="editData(' . $query->id . ')"><i class="fa fa-pencil"></i></a> |
                  <a class="btn btn-secondary btn-sm" disabled title="Tidak bisa mengubah data kembali"><i class="fa fa-circle-check"></i></a> |
                  <a class="btn btn-danger btn-sm" onclick="deleteData(' . $query->id . ')"><i class="fa fa-trash"></i></a>';
        }
      })
      ->rawColumns(['status', 'action'])
      ->make(true);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param TodoListRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(TodoListRequest $request)
  {
    $data = $request->all();
    $insert = TodoList::create([
      'user_id' => Auth::id(),
      'title' => $data['title'],
      'detail' => (empty($data['detail'])) ? '-' : $data['detail'],
      'status' => 1
    ]);

    if ($insert) {
      return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
    } else {
      return response()->json(['status' => 'failed', 'message' => 'Data gagal disimpan'], 400);
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   */
  public function edit(Request $request)
  {
    $id = $request->id;
    $data = TodoList::where('id', $id)->first();

    if (is_null($data)) {
      return response()->json([
        'status' => 'success',
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    return response()->json([
      'status' => 'success',
      'data' => $data
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(TodoListRequest $request)
  {
    $data = $request->all();
    $todoList = TodoList::where('id', $data['id'])->first();

    if (is_null($todoList)) {
      return response()->json([
        'status' => 'success',
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $update = $todoList->update([
      'title' => $data['title'],
      'detail' => (empty($data['detail'])) ? '-' : $data['detail']
    ]);

    if ($update) {
      return response()->json(['status' => 'success', 'message' => 'Data berhasil diupdate']);
    } else {
      return response()->json(['status' => 'failed', 'message' => 'Data gagal diupdate'], 400);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Request $request)
  {
    $id = $request->id;
    $data = TodoList::where('id', $id)->first();

    if (is_null($data)) {
      return response()->json([
        'status' => 'success',
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $delete = $data->delete();
    if ($delete) {
      return response()->json([
        'status' => 'success',
        'message' => 'Data berhasil dihapus'
      ]);
    } else {
      return response()->json([
        'status' => 'error',
        'message' => 'Data gagal dihapus'
      ], 400);
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function markAsOnProcess(Request $request)
  {
    $id = $request->id;
    $todoList = TodoList::where('id', $id)->first();

    if (is_null($todoList)) {
      return response()->json([
        'status' => 'success',
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $update = $todoList->update([
      'status' => 2
    ]);

    if ($update) {
      return response()->json(['status' => 'success', 'message' => 'Status berhasil diupdate ke on process']);
    } else {
      return response()->json(['status' => 'failed', 'message' => 'Status gagal diupdate ke on process'], 400);
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function markAsOnDone(Request $request)
  {
    $id = $request->id;
    $todoList = TodoList::where('id', $id)->first();

    if (is_null($todoList)) {
      return response()->json([
        'status' => 'success',
        'message' => 'Data tidak ditemukan'
      ], 404);
    }

    $update = $todoList->update([
      'status' => 3
    ]);

    if ($update) {
      return response()->json(['status' => 'success', 'message' => 'Status berhasil diupdate ke selesai']);
    } else {
      return response()->json(['status' => 'failed', 'message' => 'Status gagal diupdate ke selesai'], 400);
    }
  }
}
