@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Packet Inventory</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">DataTables</li>
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
                                <h3 class="card-title">Sales</h3>

                                <div class="row">
                                    <div class="col-12">

                                        <form class="form-validate form-horizontal"  method="get" action="" enctype="multipart/form-data">

                                            <div class="row">
                                                <div class="col-4">
                                                    <input  id="fullname"  class="form-control" name="search" placeholder=" search product name,order ref No." value="{{request('search')}}"  type="text" />
                                                </div>
                                                <div class="col-4">

                                                    <select id="" name="order_by" class="form-control" >
                                                        <option value="">Order By</option>
                                                        <option value="asc" {{ request('order_by')=='asc'?'selected':''}}>Quantity Asc</option>
                                                        <option value="desc" {{ request('order_by')=='desc'?'selected':''}}>Quantity Desc</option>


                                                    </select>

                                                </div>
                                                <div class="col-4">
                                                    <button type="submit" name="save" class="btn btn-primary">Submit</button>
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
                                        <th>Size</th>
                                        <th>Quantity</th>
                                        <th>Total Cost</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($sizes as $size)
                                        <tr>
                                            <td>{{$size->product->name??''}}</td>
                                            <td>{{$size->size??''}}</td>
                                            <td>{{$size->stock}}</td>
                                            <td>{{$size->stock * $size->price}}</td>
                                        </tr
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        {{$sizes->links()}}
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
        <!-- /.content -->
    </div>
    <!-- ./wrapper -->
@endsection
