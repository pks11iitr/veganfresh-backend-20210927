@extends('layouts.admin')
@section('content')
    <link rel="stylesheet" href="{{asset('../admin-theme/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('../admin-theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
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
                    <div class="row">
                        <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" value="{{$products->name}}">
                  </div>
                  </div>
                        <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Description</label>
                      <textarea type="text" name="description" class="form-control" id="exampleInputEmail1" placeholder="Enter Description" >{{$products->description}}</textarea>
                  </div>
                  </div>
                        <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Company</label>
                    <input type="text" name="company" class="form-control" id="exampleInputEmail1" placeholder="Enter Company" value="{{$products->company}}">
                  </div>
                  </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Rating</label>
                                <input type="text" name="ratings" class="form-control" id="exampleInputEmail1" placeholder="Enter Rating" value="{{$products->ratings}}">
                            </div>
                        </div>
                            <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Min Qty</label>
                    <input type="text" name="min_qty" class="form-control" id="exampleInputEmail1" placeholder="Enter Qty" value="{{$products->min_qty}}">
                  </div>
                  </div>
                        <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Max Qty</label>
                    <input type="text" name="max_qty" class="form-control" id="exampleInputEmail1" placeholder="Enter Qty" value="{{$products->max_qty}}">
                  </div>
                  </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Stock</label>
                                <input type="text" name="stock" class="form-control" id="exampleInputEmail1" placeholder="Enter Stock" value="{{$products->stock}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                   <div class="form-group">
                        <label>Is Offer</label>
                        <select class="form-control" name="is_offer" required>
                           <option  selected="selected" value="1" {{$products->is_offer==1?'selected':''}}>Yes</option>
                            <option value="0" {{$products->is_offer==0?'selected':''}}>No</option>
                        </select>
                      </div>
                      </div>

                        <div class="col-md-6">
                    <div class="form-group">
                        <label>Is Active</label>
                        <select class="form-control" name="isactive" required>
                           <option  selected="selected" value="1" {{$products->isactive==1?'selected':''}}>Yes</option>
                            <option value="0" {{$products->isactive==0?'selected':''}}>No</option>
                        </select>
                      </div>
                      </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="exampleInputtitle">Category</label>
                            <select name="category_id[]"  class="form-control select2" id="exampleInputistop" data-placeholder="Select a Category" multiple>
                                <option value="">Please Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}" @foreach($products->category as $s) @if($s->id==$category->id){{'selected'}}@endif @endforeach >{{$category->name}}</option>

                                @endforeach
                            </select>
                        </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputtitle">Sub Category</label>
                                {{--                            <select name="sub_cat_id" class="form-control" id="exampleInputistop" placeholder="">--}}
                                <select class="form-control select2" multiple data-placeholder="Select a subcategory" style="width: 100%;" name="sub_cat_id[]">

                                    <option value="">Please Select Category</option>
                                    @foreach($subcategories as $subcategory)

                                        <option value="{{$subcategory->id}}" @foreach($products->subcategory as $s) @if($s->id==$subcategory->id){{'selected'}}@endif @endforeach >{{$subcategory->name}}</option>


                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
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
                  <img src="{{$products->image}}" height="100" width="200">
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
     <!--*******************************************************************************************************************-->
            <section class="content">
            <div class="container-fluid">
                <!-- SELECT2 EXAMPLE -->
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Add Document Images</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Add Images</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('product.document',['id'=>$products->id])}}">
                                @csrf
                                <div class="card-body">

                                    <div class="form-group">
                                        <label for="exampleInputimage">Product Image</label>
                                        <input type="file" name="file_path[]" class="form-control" id="exampleInputimage"
                                               placeholder="" multiple>

                                    </div>

                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div><br>

                                    <div class="row">
                                        <!-- /.col -->
{{--                                        @foreach($products as $document)--}}
{{--                                            <div class="form-group">--}}
{{--                                                <img src="{{$document->file_path}}" height="100" width="200"> &nbsp; &nbsp; <a href="{{route('product.delete',['id'=>$document->id])}}">X</a>--}}
{{--                                                &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;          &nbsp; &nbsp; &nbsp; &nbsp;--}}
{{--                                            </div>--}}
{{--                                    @endforeach--}}
                                    <!-- /.form-group -->
                                        <!-- /.form-group -->
                                        <!-- /.col -->
                                    </div>
                                </div>

                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                <!--/.col (right) -->
            </div>

            </div>
        </section>
{{--      <***********************************************************************************--}}

      <section class="content">
          <div class="container-fluid">
              <div class="row">
                  <!-- left column -->
                  <div class="col-md-12">
                      <!-- general form elements -->
                      <div class="card card-primary">
                          <div class="card-header">
                              <h3 class="card-title">Add Size Price</h3>
                          </div>
                          <!-- /.card-header -->
                          <!-- form start -->
                          <form role="form" method="post" enctype="multipart/form-data" action="{{route('product.sizeprice',['id'=>$products->id])}}">
                              @csrf
                              <div class="card-body">
                                  <div class="row">
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="exampleInputEmail1">Product Size</label>
                                              <input type="text" name="size" class="form-control" id="exampleInputEmail1" placeholder="Enter size" >
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="exampleInputEmail1">Price</label>
                                              <input type="number" name="price" min="0" class="form-control" id="exampleInputEmail1" placeholder="Enter price" >
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="exampleInputEmail1">Cut Price</label>
                                              <input type="number" min="0" name="cut_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" >
                                          </div>
                                      </div>

                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label>Is Active</label>
                                              <select name="isactive" class="form-control" id="exampleInputistop" placeholder="">
                                                  <option value="1">Yes</option>
                                                  <option value="0">No</option>
                                              </select>
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
{{--      *************************************************************************************--}}
      <section class="content">
          <div class="container-fluid">
              <div class="row">
                  <div class="col-12">
                      <div class="card">
                          <div class="card-header">
                              <div class="row">
                                  <div class="col-3">
                                    List Size Price</div>

                              </div>
                          </div>
                          <!-- /.card-header -->
                          <div class="card-body">
                              <table id="example2" class="table table-bordered table-hover">
                                  <thead>
                                  <tr>
                                      <th>Size</th>
                                      <th>Price</th>
                                      <th>Cut Price</th>
                                      <th>Isactive</th>
                                      <th>Action</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  @foreach($sizeprice as $size)
                                      <tr>
                                          <td>{{$size->size}}</td>
                                          <td>{{$size->price}}</td>
                                          <td>{{$size->cut_price}}</td>

                                          <td>
                                              @if($size->isactive==1){{'Yes'}}
                                              @else{{'No'}}
                                              @endif
                                          </td>
                                          <td><a href="{{route('product.delete',['id'=>$size->id])}}" class="btn btn-danger">Delete</a></td>
                                      </tr>
                                  @endforeach
                                  </tbody>
                                  <tfoot>
                                  <tr>
                                      <th>Size</th>
                                      <th>Price</th>
                                      <th>Cut Price</th>
                                      <th>Isactive</th>
                                      <th>Action</th>
                                  </tr>
                                  </tfoot>
                              </table>
                          </div>
                      <!-- /.card-body -->
                      </div>
                      <!-- /.card -->
                      <!-- /.card -->
                  </div>
                  <!-- /.col -->
              </div>
              <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
      </section>

      <!-- /.content -->


{{--      <script src="{{asset('../admin-theme/plugins/jquery/jquery.min.js')}}"></script>--}}


      {{--      ****************************************************************************************--}}

</div>
<!-- ./wrapper -->
@endsection
@section('scripts')
    <script src="{{asset('admin-theme/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('admin-theme/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')}}"></script>
    <script>
        $(function () {
            // Summernote
            $('.textarea').summernote()
        })
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('select[name="category_id"]').on('change', function() {
                var catID = $(this).val();
                if(catID) {
                    $.ajax({
                        url: '../subcat/ajax/'+catID,
                        type: "GET",
                        dataType: "json",
                        success:function(data) {


                            $('select[name="sub_cat_id"]').empty();
                            $.each(data, function(key, value) {
                                $('select[name="sub_cat_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                            });


                        }
                    });
                }else{
                    $('select[name="sub_cat_id"]').empty();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
            $('#category_id_sel').select2();
        });
    </script>
@endsection
