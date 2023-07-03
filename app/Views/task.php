

<?= $this->extend('layout/page') ?>

<?= $this->section('content') ?>
   <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>DataTables</h1>
          </div>
          <div class="col-sm-6">
            <div class="row justify-content-end">
                <div class="col-3 align-self-end">
                    <button type="button" class="btn btn-block btn-primary" onClick="openModalAndReset('show');">Tambah Tugas</button>
                </div>
            </div>
        </div>  
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
             
              <!-- /.card-header -->
              <div class="card-body">
                <table id="task-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <td>No</td>
                            <td>Judul</td>
                            <td>Status</td>
                            <td>Aksi</td>
                        </tr>
                    </thead>
                    <tbody id="content">
                    </tbody>
                </table>
                
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    
    <!-- Modal -->
    <div class="modal fade" id="modalTask" tabindex="-1" role="dialog" aria-labelledby="modalTaskLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Task</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
             <label for="judul">Judul</label>
                <input type="text" class="form-control" id="judul" placeholder="Masukan Judul">
                <input type="hidden" class="form-control" id="id">
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select class="custom-select rounded-0" id="status">
                <option value="0">Belum Selesai</option>
                <option value="1">Selesai</option>
              </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onClick="openModalAndReset('hide')">Close</button>
            <button type="button" class="btn btn-primary" id="saveBtn" onClick="create()">Simpan</button>
            <button type="button" class="btn btn-primary" id="updateBtn" onClick="update()">Ubah</button>
        </div>
        </div>
    </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
  $(document).ready(function() {
      $('#task-table').DataTable();
      getAll();
   });

  var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });

  let url = "<?=base_url('task')?>";

  function openModalAndReset(toggle){
      $("#id").val("");
      $("#judul").val("");
      $("#status").val("0")
      $('#updateBtn').hide();
      $('#saveBtn').show();

      // $("#status option[value=1]").attr('selected', 'selected');
      $("#modalTask").modal(toggle); 
  }

  function openModalUpdate(id){
    openModalAndReset('show')
    $('#updateBtn').show();
    $('#saveBtn').hide();

    $.ajax({
        url: url+"/"+id,
        method: "GET",
        success: function(data) {
          $("#id").val(id);
          $("#judul").val(data.judul);
          $("#status").val(data.status).trigger("change");
        },
        error: function(response) {
          console.log(response.responseJSON)
          openModalAndReset('hide')
          Toast.fire({
            icon: 'error',
            title: response.responseJSON
          })
        }
    });
      
  }

  function getAll(){
    $.ajax({
        url: url,
        method: "GET",
        success: function(data) {
            const rows = []
            for (var i = 0; i < data.length; i++) 
            {
                let row = "<tr>"
                    row+="<td>"+(i+1)+"</td>"
                    row+="<td>"+data[i].judul+"</td>"
                    row+="<td><input type='checkbox' id='status_check' name='status_check' onclick='changeStatus(this,"+data[i].id+");' value='"+data[i].status+"'"+(data[i].status==1?'checked':'')+"></td>"
                    row+="<td>"
                    row+="<a class='btn btn-xs' onClick='openModalUpdate("+data[i].id+")'><i class='fas fa-edit'></i></a>"
                    row+=" <a class='btn btn-xs' onClick='delete_task("+data[i].id+")'><i class='fas fa-trash'></i></a>"
                    row+="</td>"
                    row+="</tr>"
                rows.push(row);
            }
            // $('#content').append());
            $("#task-table").DataTable().clear();
            $('#task-table').DataTable().destroy();
            $('#task-table').find('tbody').append(rows.join(''));
            $('#task-table').DataTable().draw();


        },
        error: function(response) {
          Toast.fire({
            icon: 'error',
            title: response.responseJSON
          })
        }
    });
  }

  function create(){
    var formData = new FormData();
    const judul = $("#judul").val()

    if(judul==""){
      Toast.fire({
        icon: 'warning',
        title: 'Isi judul terlebih dahulu.'
      })
      return false;
    }

    formData.append("judul",judul)       
    formData.append("status",$('#status').find(":selected").val())       

    $("#saveBtn").prop('disabled', true);

    $.ajax({
        url: url,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        enctype: 'multipart/form-data',
        success: function(response) {
            $("#saveBtn").prop('disabled', false);
            getAll()
            Toast.fire({
              icon: 'success',
              title: 'Berhasil menambahkan tugas.'
            })
            openModalAndReset('hide');
        },
        error: function(response) {
            // if error
            console.log(response)
            Toast.fire({
              icon: 'error',
              title: response
            })
            
        }
    });
  }

  function update(){
    const judul = $("#judul").val()
    const id =  $("#id").val()

    if(judul==""){
      Toast.fire({
        icon: 'warning',
        title: 'Isi judul terlebih dahulu.'
      })
      return false;
    }

    $("#updateBtn").prop('disabled', true);

    $.ajax({
        url: url+"/"+id,
        method: "PUT",
        data: 'judul='+judul+'&status='+$('#status').find(":selected").val(),            
        dataType: 'json',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        contentType: 'application/x-www-form-urlencoded; charset=utf-8',
        success: function(response) {
            $("#updateBtn").prop('disabled', false);
            getAll()
            Toast.fire({
              icon: 'success',
              title: 'Berhasil mengubah tugas.'
            })
            openModalAndReset('hide');
        },
        error: function(response) {
            // if error
            console.log(response)
            Toast.fire({
              icon: 'error',
              title: response
            })
            
        }
    });
  }

  function changeStatus(cb,id){
    const sts =cb.value==="0"?1:0
    $.ajax({
        url: url+"/"+id,
        method: "PUT",
        data: 'status='+sts,            
        dataType: 'json',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        contentType: 'application/x-www-form-urlencoded; charset=utf-8',
        success: function(response) {
            $("#updateBtn").prop('disabled', false);
            getAll()
            Toast.fire({
              icon: 'success',
              title: 'Berhasil mengubah status menjadi '+$('#status').find(":selected").text()
            })
        },
        error: function(response) {
            // if error
            console.log(response)
            Toast.fire({
              icon: 'error',
              title: response
            })
            
        }
    });
  }

  function delete_task(id){
    console.log(id)
    Swal.fire({
      title: 'Apakah anda yakin?',
      text: "Data yang dihapus tidak dapat dikembalikan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: url+"/"+id,
          method: "DELETE",
          success: function(response) {
              getAll()
              Toast.fire({
                icon: 'success',
                title: 'Berhasil menghapus tugas'
              })
          },
          error: function(response) {
              // if error
              console.log(response)
              Toast.fire({
                icon: 'error',
                title: response
              })
              
          }
      });
      }
    })
  }
</script>
<?= $this->endSection() ?>
