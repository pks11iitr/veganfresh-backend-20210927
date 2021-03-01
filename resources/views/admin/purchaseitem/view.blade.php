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
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-6">
                                        <a href="{{route('purchase.create')}}" class="btn btn-primary">Add Purchase Item</a>
                                        <a class="btn btn-warning" href="{{route('purchase.list')}}?{{Request::getQueryString()}}&export=1">Export</a>
                                    </div><br><br>

                                    <div class="col-12">
                                        <form class="form-validate form-horizontal"  method="get" action="" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-3">
                                                    <input  id="fullname"  class="form-control" name="search" placeholder=" search name" value="{{request('search')}}"  type="text" />
                                                </div>
                                                <div class="col-3">
                                                    <input   class="form-control" name="fromdate" placeholder=" search name" value="{{request('fromdate')}}"  type="date" />
                                                </div>
                                                <div class="col-3">
                                                    <input  class="form-control" name="todate" placeholder=" search name" value="{{request('todate')}}"  type="date" />
                                                </div>
                                                <div class="col-3">
                                                    <button type="submit" name="save" class="btn btn-primary">Submit</button>
                                                    <a href="{{route('purchase.list')}}" class="btn btn-danger">Reset Filters</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($purchases as $purchase)
                                        <tr>
                                            <td>{{$purchase->name}}</td>
                                            <td>{{$purchase->price}}</td>
                                            <td>{{$purchase->quantity}}</td>
                                            <td>{{$purchase->create_date}}</td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Date</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        {{$purchases->links()}}
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

        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
@endsection

