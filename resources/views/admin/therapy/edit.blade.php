@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Therapy Update</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Therapy Update</li>
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
                <h3 class="card-title">Therapy Update</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="post" enctype="multipart/form-data" action="{{route('therapy.update',['id'=>$therapy->id])}}">
                 @csrf
                <div class="card-body">
                <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" value="{{$therapy->name}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Description</label>
                    <input type="text" name="description" class="form-control" id="exampleInputEmail1" placeholder="Enter Description" value="{{$therapy->description}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grede First Price</label>
                    <input type="text" name="price1" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade1_price}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grede Second Price</label>
                    <input type="text" name="price2" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade2_price}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grede Third Price</label>
                    <input type="text" name="price3" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade3_price}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grede Fourth Price</label>
                    <input type="text"name="price4" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade4_price}}">
                  </div>
                    <div class="form-group">
                        <label>Is Active</label>
                        <select class="form-control" name="isactive" required>
                           <option  selected="selected" value="1" {{$therapy->isactive==1?'selected':''}}>Yes</option>
                            <option value="0" {{$therapy->isactive==0?'selected':''}}>No</option>
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
                  <image src="{{$therapy->image}}" height="100" width="200">
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
    <!--*******************************************************************************************************************-->
            <section class="content">
            <div class="container-fluid">
                <!-- SELECT2 EXAMPLE -->
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Add Document Images</h3>
                    </div>
                    <!-- /.card-header -->
                    <form action="{{route('therapy.document',['id'=>$therapy->id])}}" method="post" enctype="multipart/form-data">
                        @csrf  
                            <div class="card-body">
                            <!-- /.row -->
                            <div class="row">
                                <!-- /.col -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Header Image</label>
                                        <input type="file" class="form-control" name="file_path[]" id="exampleInputEmail1" placeholder="Select image" multiple>
                                        <br>
                                    </div>
                                    <!-- /.form-group -->
                                 </div>
                                <!-- /.col -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">.</label>
                                        <button type="submit" class="btn btn-block btn-primary btn-sm">Add</button>
                                    </div>
                                    <!-- /.form-group -->
                                </div>
                                <!-- /.col -->
                            </div>
                            <div class="row">
                                <!-- /.col -->
                                @foreach($documents as $document) 
                                 <div class="form-group">
                                        <img src="{{$document->file_path}}" height="100" width="200"> &nbsp; &nbsp; <a href="{{route('therapy.delete',['id'=>$document->id])}}">X</a>
                                        &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;          &nbsp; &nbsp; &nbsp; &nbsp;          
                                  </div>
                               @endforeach
                                 <!-- /.form-group -->
                                    <!-- /.form-group -->
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                    </form>
                </div>



            </div>
        </section>
  
  
</div>
<!-- ./wrapper -->
@endsection

