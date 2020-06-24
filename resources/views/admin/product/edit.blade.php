@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Product</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Product Update</li>
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
                <h3 class="card-title">Product Update</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="post" enctype="multipart/form-data" action="{{route('product.update',['id'=>$products->id])}}">
                 @csrf
                <div class="card-body">
                <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" value="{{$products->name}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Description</label>
                    <input type="text" name="description" class="form-control" id="exampleInputEmail1" placeholder="Enter Description" value="{{$products->description}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Company</label>
                    <input type="text" name="company" class="form-control" id="exampleInputEmail1" placeholder="Enter Company" value="{{$products->company}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Cut Price</label>
                    <input type="text" name="price" class="form-control" id="exampleInputEmail1" placeholder="Enter Price" value="{{$products->price}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Cut Price</label>
                    <input type="text" name="cut_price" class="form-control" id="exampleInputEmail1" placeholder="Enter Price" value="{{$products->cut_price}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Rating</label>
                    <input type="text" name="ratings" class="form-control" id="exampleInputEmail1" placeholder="Enter Rating" value="{{$products->ratings}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Top Deal</label>
                    <input type="text"name="top_deal" class="form-control" id="exampleInputEmail1" placeholder="Enter Top Deal" value="{{$products->top_deal}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Best Seller</label>
                    <input type="text"name="best_seller" class="form-control" id="exampleInputEmail1" placeholder="Enter Seller" value="{{$products->best_seller}}">
                  </div>
                    <div class="form-group">
                        <label>Is Active</label>
                        <select class="form-control" name="isactive" required>
                           <option  selected="selected" value="1" {{$products->isactive==1?'selected':''}}>Yes</option>
                            <option value="0" {{$products->isactive==0?'selected':''}}>No</option>
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
                  <image src="{{$products->image}}" height="100" width="200">
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

