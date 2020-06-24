@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Clinic Update</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Clinic Update</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Clinic Update</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="post" enctype="multipart/form-data" action="{{route('clinic.update',['id'=>$clinic->id])}}">
                 @csrf
                <div class="card-body">
                <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" value="{{$clinic->name}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Description</label>
                    <input type="text" name="description" class="form-control" id="exampleInputEmail1" placeholder="Enter Description" value="{{$clinic->description}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Address</label>
                    <input type="text" name="address" class="form-control" id="exampleInputEmail1" placeholder="Enter Address" value="{{$clinic->address}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">City</label>
                    <input type="text" name="city" class="form-control" id="exampleInputEmail1" placeholder="Enter City" value="{{$clinic->city}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">State</label>
                    <input type="text" name="state" class="form-control" id="exampleInputEmail1" placeholder="Enter State" value="{{$clinic->state}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Contact</label>
                    <input type="text"name="contact" class="form-control" id="exampleInputEmail1" placeholder="Enter Contact" value="{{$clinic->contact}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Lat</label>
                    <input type="text"name="lat" class="form-control" id="exampleInputEmail1" placeholder="Enter Lat" value="{{$clinic->lat}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Lang</label>
                    <input type="text"name="lang" class="form-control" id="exampleInputEmail1" placeholder="Enter Lang" value="{{$clinic->lang}}">
                  </div>
                    <div class="form-group">
                        <label>Is Active</label>
                        <select class="form-control" name="isactive" required>
                           <option  selected="selected" value="1" {{$clinic->isactive==1?'selected':''}}>Yes</option>
                            <option value="0" {{$clinic->isactive==0?'selected':''}}>No</option>
                        </select>
                      </div>
                  <div class="form-group">
                    <label for="exampleInputFile">File input</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" name="image" class="custom-file-input" id="exampleInputFile" accept="image/*">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                         
                      </div>
                      <div class="input-group-append">
                        <span class="input-group-text" id="">Upload</span>
                        
                      </div>
                      
                    </div>
                  </div>
                  <image src="{{$clinic->image}}" height="100" width="200">
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  
</div>
<!-- ./wrapper -->
@endsection

