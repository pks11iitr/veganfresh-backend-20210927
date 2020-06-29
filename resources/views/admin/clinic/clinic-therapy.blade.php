@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Clinic Therapy Update</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{route('clinic.edit', ['id'=>$therapy->clinic_id])}}">Back To Clinic</a></li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Edit {{$therapy->clinic->name}} Therapy</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('clinic.therapyedit',['id'=>$therapy->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Therapy Name</label>
                                                <select name="therapy_id" class="form-control" id="exampleInputistop" placeholder="">
                                                        <option value="{{$therapy->therapy->id??''}}">{{$therapy->therapy->name??''}} </option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Grade1 Price</label>
                                                <input type="text" name="grade1_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade1_price}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Grade1 Old Price</label>
                                                <input type="text" name="grade1_original_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade1_original_price}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Grade2 Price</label>
                                                <input type="text" name="grade2_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade2_price}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Grade2 Old Price</label>
                                                <input type="text" name="grade2_original_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade2_original_price}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Grade3 Price</label>
                                                <input type="text" name="grade3_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade3_price}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Grade3 Old Price</label>
                                                <input type="text" name="grade3_original_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade3_original_price}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Grade4 Price</label>
                                                <input type="text" name="grade4_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade4_price}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Grade4 Old Price</label>
                                                <input type="text" name="grade4_original_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" value="{{$therapy->grade4_original_price}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Is Active</label>
                                                <select name="isactive" class="form-control" id="exampleInputistop" placeholder="">
                                                    <option value="1" {{$therapy->isactive==1?'selected':''}}>Yes</option>
                                                    <option value="0" {{$therapy->isactive==0?'selected':''}}>No</option>
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
        <!--****************************************************************************************************************-->



    </div>
    <!-- ./wrapper -->
@endsection

