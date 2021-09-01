<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/r/bs-3.3.5/jq-2.1.4,dt-1.10.8/datatables.min.css"/>

  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="https://cdn.datatables.net/r/bs-3.3.5/jqc-1.11.3,dt-1.10.8/datatables.min.js"></script>

</head>
  <style>
  .alert-message {
    color: red;
  }
</style>
<body>

<div class="container">
    <br>
    <p>{{ csrf_token() }}</p>
     <div class="row">
       <div class="col-12 text-right">
         <a href="javascript:void(0)" class="btn btn-success mb-3" id="create-new-post" onclick="addPost()">Add Post</a>
       </div>
    </div>
    <div class="row" style="clear: both;margin-top: 18px;">
        <div class="col-12">
          <table id="laravel_crud" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr id="row_{{$student->id}}">
                   <td>{{ $student->id  }}</td>
                   <td>{{ $student->nama }}</td>
                   <td>{{ $student->kelas }}</td>
                   <td><a href="javascript:void(0)" data-id="{{ $student->id }}" onclick="editPost(event.target)" class="btn btn-info">Edit</a></td>
                   <td>
                    <a href="javascript:void(0)" data-id="{{ $student->id }}" class="btn btn-danger" onclick="deletePost(event.target)">Delete</a></td>
                </tr>
                @endforeach
            </tbody>
          </table>
       </div>
    </div>
</div>

<div class="modal fade" id="post-modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
            <form name="userForm" class="form-horizontal">
              @csrf
               <input type="hidden" name="siswa_id" id="siswa_id">
                <div class="form-group">
                    <label for="name" class="col-sm-2">Nama</label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Enter Name">
                        <span id="namaError" class="alert-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2">Kelas</label>
                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="kelas" name="kelas" placeholder="Enter Class">
                        {{-- <textarea class="form-control" id="kelas" name="kelas" placeholder="Enter Class" rows="4" cols="50">
                        </textarea> --}}
                        <span id="kelasError" class="alert-message"></span>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" onclick="createPost()">Save</button>
        </div>
    </div>
  </div>
</div>

<script>
  $('#laravel_crud').DataTable();

  function addPost() {
    $("#siswa_id").val('');
    $('#post-modal').modal('show');
  }

  function editPost(event) {
    var id  = $(event).data("id");
    let _url = `/students/${id}`;
    $('#namaError').text('');
    $('#kelasError').text('');
    
    $.ajax({
      url: _url,
      type: "GET",
      success: function(response) {
          if(response) {
            $("#siswa_id").val(response.id);
            $("#nama").val(response.nama);
            $("#kelas").val(response.kelas);
            $('#post-modal').modal('show');
          }
      }
    });
  }

  function createPost() {
    var nama = $('#nama').val();
    var kelas = $('#kelas').val();
    var id = $('#siswa_id').val();

    let _url     = '/students';
    let _token   = $('meta[name="csrf-token"]').attr('content');

    if (id != "") {
      $.ajax({
        url:`/students/${id}`,
        type: "PUT",
        data: {
          // id: id,
          nama: nama,
          kelas: kelas,
          _token: _token
        },
        success: function(response) {
            if(response.code == 200) {
              // if(id != ""){
                $("#row_"+id+" td:nth-child(2)").html(response.data.nama);
                $("#row_"+id+" td:nth-child(3)").html(response.data.kelas);
              // } else {
                // $('table tbody').prepend('<tr id="row_'+response.data.id+'"><td>'+response.data.id+'</td><td>'+response.data.nama+'</td><td>'+response.data.kelas+'</td><td><a href="javascript:void(0)" data-id="'+response.data.id+'" onclick="editPost(event.target)" class="btn btn-info">Edit</a></td><td><a href="javascript:void(0)" data-id="'+response.data.id+'" class="btn btn-danger" onclick="deletePost(event.target)">Delete</a></td></tr>');
              // }
              $('#nama').val('');
              $('#kelas').val('');

              $('#post-modal').modal('hide');
            }
        },
        error: function(response) {
          $('#namaError').text(response.responseJSON);
          $('#kelasError').text(response.responseJSON);
        }
      });
    }else{
      $.ajax({
        url: _url,
        type: "POST",
        data: {
          // id: id,
          nama: nama,
          kelas: kelas,
          _token: _token
        },
        success: function(response) {
            if(response.code == 200) {
              // if(id != ""){
              //   $("#row_"+id+" td:nth-child(2)").html(response.data.nama);
              //   $("#row_"+id+" td:nth-child(3)").html(response.data.kelas);
              // } else {
                $('table tbody').prepend('<tr id="row_'+response.data.id+'"><td>'+response.data.id+'</td><td>'+response.data.nama+'</td><td>'+response.data.kelas+'</td><td><a href="javascript:void(0)" data-id="'+response.data.id+'" onclick="editPost(event.target)" class="btn btn-info">Edit</a></td><td><a href="javascript:void(0)" data-id="'+response.data.id+'" class="btn btn-danger" onclick="deletePost(event.target)">Delete</a></td></tr>');
              // }
              $('#nama').val('');
              $('#kelas').val('');

              $('#post-modal').modal('hide');
            }
        },
        error: function(response) {
          $('#namaError').text(response.responseJSON);
          $('#kelasError').text(response.responseJSON);
        }
      });
    }
  }

  function deletePost(event) {
    var id  = $(event).data("id");
    let _url = `/students/${id}`;
    let _token   = $('meta[name="csrf-token"]').attr('content');

      $.ajax({
        url: _url,
        type: 'DELETE',
        data: {
          _token: _token
        },
        success: function(response) {
          $("#row_"+id).remove();
        }
      });
  }

</script>
</body>
</html>