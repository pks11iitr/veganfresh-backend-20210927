@extends('layouts.admin')
@section('content')
    <link rel="stylesheet" href="{{asset('../admin-theme/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet"
          href="{{asset('../admin-theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
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
                            <form role="form" method="post" enctype="multipart/form-data"
                                  action="{{route('product.update',['id'=>$products->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Name</label>
                                                <input type="text" name="name" class="form-control"
                                                       id="exampleInputEmail1" placeholder="Enter Name"
                                                       value="{{$products->name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Description</label>
                                                <textarea type="text" name="description" class="form-control"
                                                          id="exampleInputEmail1"
                                                          placeholder="Enter Description">{{$products->description}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Company</label>
                                                <input type="text" name="company" class="form-control"
                                                       id="exampleInputEmail1" placeholder="Enter Company"
                                                       value="{{$products->company}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Rating</label>
                                                <input type="text" name="ratings" class="form-control"
                                                       id="exampleInputEmail1" placeholder="Enter Rating"
                                                       value="{{$products->ratings}}">
                                            </div>
                                        </div>
                                        {{--                            <div class="col-md-6">--}}
                                        {{--                  <div class="form-group">--}}
                                        {{--                    <label for="exampleInputEmail1">Min Qty</label>--}}
                                        {{--                    <input type="text" name="min_qty" class="form-control" id="exampleInputEmail1" placeholder="Enter Qty" value="{{$products->min_qty}}">--}}
                                        {{--                  </div>--}}
                                        {{--                  </div>--}}
                                        {{--                        <div class="col-md-6">--}}
                                        {{--                  <div class="form-group">--}}
                                        {{--                    <label for="exampleInputEmail1">Max Qty</label>--}}
                                        {{--                    <input type="text" name="max_qty" class="form-control" id="exampleInputEmail1" placeholder="Enter Qty" value="{{$products->max_qty}}">--}}
                                        {{--                  </div>--}}
                                        {{--                  </div>--}}
                                        {{--                        <div class="col-md-6">--}}
                                        {{--                            <div class="form-group">--}}
                                        {{--                                <label for="exampleInputEmail1">Stock</label>--}}
                                        {{--                                <input type="text" name="stock" class="form-control" id="exampleInputEmail1" placeholder="Enter Stock" value="{{$products->stock}}">--}}
                                        {{--                            </div>--}}
                                        {{--                        </div>--}}

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Is Offer</label>
                                                <select class="form-control" name="is_offer" required>
                                                    <option selected="selected"
                                                            value="1" {{$products->is_offer==1?'selected':''}}>Yes
                                                    </option>
                                                    <option value="0" {{$products->is_offer==0?'selected':''}}>No
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Is Active</label>
                                                <select class="form-control" name="isactive" required>
                                                    <option selected="selected"
                                                            value="1" {{$products->isactive==1?'selected':''}}>Yes
                                                    </option>
                                                    <option value="0" {{$products->isactive==0?'selected':''}}>No
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputtitle">Category</label>
                                                <select name="category_id[]" class="form-control select2"
                                                        id="exampleInputistop" data-placeholder="Select a Category"
                                                        multiple>
                                                    <option value="">Please Select Category</option>
                                                    @foreach($categories as $category)
                                                        <option
                                                            value="{{$category->id}}" @foreach($products->category as $s) @if($s->id==$category->id){{'selected'}}@endif @endforeach >{{$category->name}}</option>

                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputtitle">Sub Category</label>
                                                <select class="form-control select2" multiple
                                                        data-placeholder="Select a subcategory" style="width: 100%;"
                                                        name="sub_cat_id[]">

                                                    <option value="">Please Select Category</option>
                                                    @foreach($subcategories as $subcategory)

                                                        <option
                                                            value="{{$subcategory->id}}" @foreach($products->subcategory as $s) @if($s->id==$subcategory->id){{'selected'}}@endif @endforeach >{{$subcategory->name}}</option>


                                                    @endforeach
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
                            <form role="form" method="post" enctype="multipart/form-data"
                                  action="{{route('product.document',['id'=>$products->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputtitle">Select Size</label>
                                        <select name="size_id" class="form-control" id="exampleInputistop"
                                                placeholder="">
                                            @foreach($sizeprice as $size)
                                                <option
                                                    value="{{$size->id}}" {{$products->size_id==$size->id?'selected':''}}>{{$size->size}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputimage">Product Image</label>
                                        <input type="file" name="image[]" class="form-control" id="exampleInputimage"
                                               placeholder="" multiple>

                                    </div>

                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                    <br>

                                    <div class="row">

                                        @foreach($documents as $documenta)
                                            @foreach($documenta->images as $document)
                                                                                <div class="form-group">
                                                                                    <img src="{{$document->image}}" height="100" width="200"> <span>{{$documenta->size}} </span>&nbsp; &nbsp; <a href="{{route('product.delete',['id'=>$document->id])}}">X</a>
                                                                                    &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;          &nbsp; &nbsp; &nbsp; &nbsp;
                                                                                </div>
                                                                        @endforeach
                                    @endforeach

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
                            <form role="form" method="post" enctype="multipart/form-data"
                                  action="{{route('product.sizeprice',['id'=>$products->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Product Size</label>
                                                <input type="text" name="size" class="form-control"
                                                       id="exampleInputEmail1" placeholder="Enter size">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Price</label>
                                                <input type="number" name="price" min="0" class="form-control"
                                                       id="exampleInputEmail1" placeholder="Enter price">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Cut Price</label>
                                                <input type="number" min="0" name="cut_price" class="form-control"
                                                       id="exampleInputEmail1" placeholder="Enter price">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Min QTY</label>
                                                <input type="number" name="min_qty" class="form-control" min="0"
                                                       id="exampleInputEmail3" placeholder="Enter Qty">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Max QTY</label>
                                                <input type="number" name="max_qty" class="form-control" min="0"
                                                       id="exampleInputEmail3" placeholder="Enter qty">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Stock</label>
                                                <input type="number" name="stock" class="form-control" min="0"
                                                       id="exampleInputEmail3" placeholder="Enter Stock">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile">Product Image</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="image" class="custom-file-input"
                                                               id="exampleInputFile" accept="image/*" required>
                                                        <label class="custom-file-label" for="exampleInputFile">Choose
                                                            file</label>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="">Upload</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Is Active</label>
                                                <select name="isactive" class="form-control" id="exampleInputistop"
                                                        placeholder="">
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
                                        List Size Price
                                    </div>

                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="tbl" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th>Price</th>
                                        <th>Cut Price</th>
                                        <th>Min. QTY</th>
                                        <th>Max QTY</th>
                                        <th>Stock</th>
                                        <th>Image</th>
                                        <th>Isactive</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($sizeprice as $size)
                                        <tr id="row{{$size->id}}">
                                            <td id="size{{$size->id}}">{{$size->size}}</td>
                                            <td id="price{{$size->id}}">{{$size->price}}</td>
                                            <td id="cut_price{{$size->id}}">{{$size->cut_price}}</td>
                                            <td id="min_qty{{$size->id}}">{{$size->min_qty}}</td>
                                            <td id="max_qty{{$size->id}}">{{$size->max_qty}}</td>
                                            <td id="image{{$size->id}}"><img src="{{$size->image}}" height="80px" width="80px"/><input type="file" style='width:80px; margin-left: 5px;' id="sel_image{{$size->id}}"></td>
                                            <td id="stock{{$size->id}}">{{$size->stock}}</td>

                                            <td id="isactive{{$size->id}}">
                                                @if($size->isactive==1){{'Yes'}}
                                                @else{{'No'}}
                                                @endif
                                            </td>
                                            <td>
                                                {{--                                          <td><a href="{{route('product.delete',['id'=>$size->id])}}" class="btn btn-danger">Delete</a>--}}
                                                <input type="button" id="edit_button{{$size->id}}" value="Edit"
                                                       class="btn btn-success" onclick="edit_row({{$size->id}})">
                                                <input type="button" id="save_button{{$size->id}}" value="Save"
                                                       class="btn btn-success" onclick="save_row({{$size->id}})">
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Size</th>
                                        <th>Price</th>
                                        <th>Cut Price</th>
                                        <th>Min. QTY</th>
                                        <th>Max QTY</th>
                                        <th>Image</th>
                                        <th>Stock</th>
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
{{--    <script type="text/javascript">--}}
{{--        $(document).ready(function () {--}}
{{--            $('select[name="size_id"]').on('change', function () {--}}
{{--                var catID = $(this).val();--}}
{{--                var data = 'size_id=' + catID;--}}
{{--                $.ajax({--}}
{{--                    url: "{{route('product.size.images')}}",--}}
{{--                    type: "GET",--}}
{{--                    dataType: "json",--}}
{{--                    data: data,--}}
{{--                    success: function (data) {--}}

{{--                        $.each(data, function(key, value) {--}}
{{--                            $('select[name="image"]').append('<option value="'+ key +'">'+ value +'</option>');--}}
{{--                        });--}}
{{--                    }--}}

{{--                });--}}
{{--            });--}}
{{--        });--}}

{{--    </script>--}}

    <script>
        $(document).ready(function () {
            $('.select2').select2();
            $('#category_id_sel').select2();
        });
    </script>

    <script>
        function edit_row(no) {
            document.getElementById("edit_button" + no).style.display = "none";
            document.getElementById("save_button" + no).style.display = "block";

            var size = document.getElementById("size" + no);
            var price = document.getElementById("price" + no);
            var cut_price = document.getElementById("cut_price" + no);
            var min = document.getElementById("min_qty" + no);
            var max = document.getElementById("max_qty" + no);
            var image = document.getElementById("image" + no);
            var stock = document.getElementById("stock" + no);
            var isactive = document.getElementById("isactive" + no);

            var size_data = size.innerHTML;
            var price_data = price.innerHTML;
            var cut_price_data = cut_price.innerHTML;
            var min_data = min.innerHTML;
            var max_data = max.innerHTML;
            var image_data = image.src;
            var stock_data = stock.innerHTML;
            var isactive_data1 = isactive.innerHTML
            if (isactive_data1.trim() === "Yes") {
                var isactive_data = '1';
            } else {
                var isactive_data = '0';
            }


            size.innerHTML = "<input type='text' style='width:80px;' id='size_text" + no + "' value='" + size_data + "'>";
            price.innerHTML = "<input type='text' style='width:80px;' id='price_text" + no + "' value='" + price_data + "'>";
            cut_price.innerHTML = "<input type='text' style='width:80px;' id='cut_price_text" + no + "' value='" + cut_price_data + "'>";
            min.innerHTML = "<input type='text' style='width:80px;'  id='min_text" + no + "' value='" + min_data + "'>";
            max.innerHTML = "<input type='text' style='width:80px;' id='max_text" + no + "' value='" + max_data + "'>";
            stock.innerHTML = "<input type='text' style='width:80px;' id='stock_text" + no + "' value='" + stock_data + "'>";
            isactive.innerHTML = "<input type='text' style='width:80px;' id='isactive_text" + no + "' value='" + isactive_data + "'>";
        }

        function save_row(no) {


            var size_val = document.getElementById("size_text" + no).value;
            var price_val = document.getElementById("price_text" + no).value;
            var cut_price_val = document.getElementById("cut_price_text" + no).value;
            var min_val = document.getElementById("min_text" + no).value;
            var max_val = document.getElementById("max_text" + no).value;
            var stock_val = document.getElementById("stock_text" + no).value;
            var isactive_val = document.getElementById("isactive_text" + no).value;
            // var data = 'price=' + price_val + '&cut_price=' + cut_price_val + '&min_qty=' + min_val + '&c=' + max_val + '&stock=' + stock_val + '&isactive=' + isactive_val + '&size_id=' + no;
            formdata = new FormData();
            formdata.append('size', size_val)
            formdata.append('price', price_val)
            formdata.append('cut_price', cut_price_val)
            formdata.append('min_qty', min_val)
            formdata.append('max_qty', max_val)
            formdata.append('stock', stock_val)
            formdata.append('isactive', isactive_val)
            formdata.append('size_id', no)
            var files = $('#sel_image'+no)[0].files[0];
            formdata.append('file',files);

            $.ajax({
                url: "{{route('product.size.update')}}",
                type: "POST",
                data: formdata,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {

                   // alert(data)
                    window.location.reload();
                    $('#message').html("<h2>Current balance has been updated!</h2>")
                }

            });
        }
    </script>
@endsection
