@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Product Add</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Product Add</li>
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
                <h3 class="card-title">Product Add</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="post" enctype="multipart/form-data" action="{{route('product.store')}}">
                 @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name">
                  </div>
                  </div>
                        <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Description</label>
                    <input type="text" name="description" class="form-control" id="exampleInputEmail1" placeholder="Enter Description">
                  </div>
                  </div>
                        <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Company</label>
                    <input type="text" name="company" class="form-control" id="exampleInputEmail2" placeholder="Enter Name">
                  </div>
                  </div>
                        <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Rating</label>
                    <input type="text" name="ratings" class="form-control" min="0" id="exampleInputEmail3" placeholder="Enter rating">
                  </div>
                  </div>
                        <div class="col-md-6">
                  <div class="form-group">
                        <label>Is Offer</label>
                        <select class="form-control" name="is_offer" required>
                           <option value="1">Yes</option>
                           <option value="0">No</option>
                        </select>
                    </div>
                    </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Min QTY</label>
                                <input type="number" name="min_qty" class="form-control" min="0" id="exampleInputEmail3" placeholder="Enter Qty">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Max QTY</label>
                                <input type="number" name="max_qty" class="form-control" min="0" id="exampleInputEmail3" placeholder="Enter qty">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Stock</label>
                                <input type="number" name="stock" class="form-control" min="0" id="exampleInputEmail3" placeholder="Enter Stock">
                            </div>
                        </div>
                        <div class="col-md-6">
                   <div class="form-group">
                        <label>Is Active</label>
                        <select class="form-control" name="isactive" required>
                           <option value="1">Yes</option>
                           <option value="0">No</option>
                        </select>
                    </div>
                    </div>
                        <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputFile">Product Image</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" name="image" class="custom-file-input" id="exampleInputFile" accept="image/*" required>
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                      <div class="input-group-append">
                        <span class="input-group-text" id="">Upload</span>
                      </div>
                    </div>
                  </div>
                </div>
                </div>

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

