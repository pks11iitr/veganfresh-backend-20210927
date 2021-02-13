@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Purchase Item</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Purchase Item</li>
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
                                <h3 class="card-title">Purchase Item Add</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('purchase.store')}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Name</label>
                                                <select type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name">
                                                    <option value="">Select</option>
                                                    @foreach($products as $product)
                                                        @if($product->stock_type=='packet')
                                                            @foreach($product->sizeprice as $size)
                                                            <option value="{{$product->name}}--{{$size->size}}">{{$product->name}}--{{$size->size}}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="{{$product->name}}">{{$product->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Price</label>
                                                <input type="text" name="price" class="form-control" id="exampleInputEmail1" placeholder="Enter Price" min="0" value="{{old('price')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">MRP</label>
                                                <input type="text" name="mrp" class="form-control" id="exampleInputEmail1" placeholder="Enter Price" min="0" value="{{old('mrp')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Quantity</label>
                                                <input type="text" name="quantity" class="form-control" id="exampleInputEmail1" placeholder="Enter Quantity" min="0" {{old('quantity')}}>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Date</label>
                                                <input type="date" name="create_date" class="form-control" id="exampleInputEmail1" placeholder="Enter Date" value="{{old('created_at')??date('Y-m-d')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Expiry</label>
                                                <input type="date" name="expiry" class="form-control" id="exampleInputEmail1" placeholder="Enter Date" value="{{old('expiry')??date('Y-m-d')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Manufacturing</label>
                                                <input type="text" name="manufacturer" class="form-control" id="exampleInputEmail1" placeholder="Enter Manufactorer" value="{{old('manufacturer')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Vendor</label>
                                                <input type="text" name="vendor" class="form-control" id="exampleInputEmail1" placeholder="Enter Vendor" min="0" value="{{old('vendor')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Remark</label>
                                                <textarea name="remarks" class="form-control" id="exampleInputEmail1" placeholder="Enter Remarks">{{old('remarks')}}</textarea>
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

