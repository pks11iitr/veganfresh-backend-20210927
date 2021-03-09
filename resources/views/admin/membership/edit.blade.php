@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Membership</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{route('membership.list')}}">Membership</a></li>
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
                                <h3 class="card-title">Membership Edit</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('membership.update',['id'=>$membership->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Name</label>
                                                <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" value="{{$membership->name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Price</label>
                                                <input type="number" name="price" class="form-control" id="exampleInputEmail1" placeholder="Enter Price" min="0" value="{{$membership->price}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Validity</label>
                                                <input type="number" name="validity" class="form-control" id="exampleInputEmail1" placeholder="Enter Validity" min="0" value="{{$membership->validity}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Percentage</label>
                                                <input type="number" name="cashback" class="form-control" id="exampleInputEmail1" placeholder="Enter Percentage" min="0" value="{{$membership->cashback}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Isactive</label>
                                                <select class="form-control" name="isactive" required>
                                                    <option value="1" {{$membership->isactive=='1'?'Selected':''}}>Active</option>
                                                    <option value="0" {{$membership->isactive=='0'?'Selected':''}}>Inactive</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label for="exampleInputtitle">Sub Category</label>
                                                    <select class="form-control select2" multiple
                                                            data-placeholder="Select a subcategory" style="width: 100%;"
                                                            name="sub_categories[]">

                                                        <option value="">Please Select Category</option>
                                                        @foreach($subcategories as $subcategory)

                                                            <option
                                                                value="{{$subcategory->id}}" @foreach($membership->categories as $s) @if($s->id==$subcategory->id){{'selected'}}@endif @endforeach >{{$subcategory->name}}</option>


                                                        @endforeach
                                                    </select>
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
@section('scripts')
    <script>
        $(document).ready(function () {
            $('.select2').select2();
            $('#category_id_sel').select2();
        });
    </script>
@endsection

