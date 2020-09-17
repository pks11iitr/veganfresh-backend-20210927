@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Home Section</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Home Section</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="card card-primary">
                                        <a href="{{route('homesection.bannercreate')}}" class="btn btn-primary">Add Banner</a>
                                        </div>
                                    </div>
                                <div class="col-4">
                                    <div class="card card-primary">
                                        <a href="{{route('homesection.productcreate')}}" class="btn btn-primary">Add Product</a>
                                    </div>
                                </div>
                                    <div class="col-4">
                                        <div class="card card-primary">
                                            <a href="{{route('homesection.subcategorycreate')}}" class="btn btn-primary">Add SubCategory</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Sequence No</th>
                                        <th>Image</th>
                                        <th>Type</th>
                                        <th>Isactive</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($homesections as $homesection)
                                        <tr>
                                            <td>{{$homesection->name}}</td>
                                            <td>{{$homesection->sequence_no}}</td>
                                            <td><img src="{{$homesection->image}}" height="80px" width="80px"/></td>
                                            <td>{{$homesection->type}}</td>
                                            <td>
                                                @if($homesection->isactive==1){{'Yes'}}
                                                @else{{'No'}}
                                                @endif
                                            </td>

                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        {{$homesections->links()}}
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
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
@endsection

