@extends('layouts.app')
@section('title', $title)
@section('content')
  <div class="container-fluid">
    <div class="row mt-3">
      <div class="col-md-2">
        <button type="button" class="btn btn-primary btn-sm" onclick="addData()"><i class="fa fa-plus"></i> Tambah</button>
      </div>
      <div class="col-md-12 mt-5">
        <div class="row">
          <div class="col-md-2">
            <label for="status_filter">Filter Status: </label>
            <select id="status_filter" class="form-control">
              <option value="1">Waiting</option>
              <option value="2">On Process</option>
              <option value="3">Done</option>
            </select>
          </div>
          <div class="col-md-12 mt-3">
            <table class="table table-striped table-responsive" id="todo-table">
              <thead>
              <tr>
                <th scope="col">No</th>
                <th scope="col">Title</th>
                <th scope="col">Detail</th>
                <th scope="col">Status</th>
                <th scope="col">Aksi</th>
              </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="todo-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Tambah</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" method="post" id="form-todo">
            <input type="hidden" id="id" name="id">
            <div class="form-group">
              <label for="title">Title<span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="title" name="title" placeholder="Masukan title">
              <span class="text-danger">
                 <strong id="title-error"></strong>
               </span>
            </div>
            <div class="form-group mt-2">
              <label for="detail">Detail</label>
              <textarea name="detail" id="detail" cols="30" rows="3" class="form-control" placeholder="Masukan detail (optional)"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="submitData()">Save</button>
        </div>
      </div>
    </div>
  </div>
@stop
@push('scripts')
  <script>
      let url, type, table;

      table = $('#todo-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [],
        pagingType: "full_numbers",
        lengthMenu: [
          [6, 25, 50, -1],
          [6, 25, 50, "All"]
        ],
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search...",
        },

        ajax: {
          "url": '{{ route('todo-list.json') }}',
          "type": "Post",
          "headers": {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
          },
          "data": function (d) {
            d.status = $('#status_filter').val()
          }
        },

        columns: [
          {data: "DT_RowIndex", orderable: false, searchable: false},
          {data: 'title'},
          {data: 'detail'},
          {data: 'status'},
          {data: 'action', sClass: 'text-center', orderable: false, searchable: false}
        ],
      });

      $('#status_filter').change(function () {
        table.ajax.reload(false);
      });

      const addData = function () {
        $('#todo-modal').modal('show');
        url = '{{ route('todo-list.store') }}';
        type = 'post';
        $('#title').val('');
        $('#detail').val('');
      }

      const editData = function (id) {
        $('#todo-modal').modal('show');
        url = '{{ route('todo-list.update') }}';
        type = 'put';

        $.ajax({
          headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          type: 'get',
          url: '{{ route('todo-list.edit') }}',
          data: {id: id},
          dataType: 'json',
          success: function (response) {
            $('#title').val(response.data.title);
            $('#detail').val(response.data.detail);
            $('#id').val(response.data.id);
          },
          error: function (xhr, error, status) {
            alert(error + ":" + status);
          },
        });
      }

      const deleteData = function (id) {
        Swal.fire({
          title: 'Are you sure?',
          text: "Data yang sudah dihapus tidak dapat dikembalikan.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
              type: 'delete',
              url: '{{ route('todo-list.destroy') }}',
              data: {id: id},
              dataType: 'json',
              success: function (response) {
                if (response.status === 'success') {
                  Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                  })
                  table.ajax.reload();
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message,
                  })
                }
              },
              error: function (xhr, error, status) {
                alert(error + ":" + status);
              },
            });
          }
        })
      }

      const submitData = function () {
        $.ajax({
          headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          type: type,
          url: url,
          data: $('#form-todo').serialize(),
          dataType: 'json',
          success: function (response) {
            $('#todo-modal').modal('hide');
            if (response.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: response.message,
              })
              table.ajax.reload();
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: response.message,
              })
            }
          },
          error: function (resp) {
            if (_.has(resp.responseJSON, 'errors')) {
              _.map(resp.responseJSON.errors, function (val, key) {
                $('#' + key + '-error').html(val[0]).fadeIn(1000).fadeOut(5000);
              })
            }
            alert(resp.responseJSON.message);
          },
        });
      }

      const markAsOnProcess = function (id) {
        Swal.fire({
          title: 'Apakah anda ingin mengubah status ke on proses?',
          text: "Data yang sudah diubah tidak dapat dikembalikan.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
              type: 'post',
              url: '{{ route('todo-list.mark-as-on-process') }}',
              data: {id: id},
              dataType: 'json',
              success: function (response) {
                if (response.status === 'success') {
                  Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                  })
                  table.ajax.reload();
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message,
                  })
                }
              },
              error: function (xhr, error, status) {
                alert(error + ":" + status);
              },
            });
          }
        })
      }

      const markAsDone = function (id) {
        Swal.fire({
          title: 'Apakah anda ingin mengubah status ke selesai?',
          text: "Data yang sudah diubah tidak dapat dikembalikan.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
              type: 'post',
              url: '{{ route('todo-list.mark-as-done') }}',
              data: {id: id},
              dataType: 'json',
              success: function (response) {
                if (response.status === 'success') {
                  Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                  })
                  table.ajax.reload();
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message,
                  })
                }
              },
              error: function (xhr, error, status) {
                alert(error + ":" + status);
              },
            });
          }
        })
      }
  </script>
@endpush
