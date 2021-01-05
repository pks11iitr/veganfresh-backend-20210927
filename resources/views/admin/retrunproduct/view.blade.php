@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Return Product</h1>
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
                                    <h3 class="card-title">Retrun Product</h3>

                                <div class="row">
                                    <div class="col-12">

                                        <form class="form-validate form-horizontal"  method="get" action="" enctype="multipart/form-data">

                                            <div class="row">
                                                <div class="col-4">
                                                    <input  id="fullname"  class="form-control" name="search" placeholder=" search product name,order ref No." value="{{request('search')}}"  type="text" />
                                                </div>
                                                <div class="col-4">

                                                    <select id="store_id" name="store_id" class="form-control" >
                                                        <option value="" {{ request('store_id')==''?'selected':''}}>Select Store</option>
                                                        @foreach($stores as $store)
                                                            <option value="{{$store->id}}" {{request('store_id')==$store->id?'selected':''}}>{{ $store->name }}</option>                                    @endforeach

                                                    </select>

                                                </div>
                                                <div class="col-4">
                                                <select id="rider_id" name="rider_id" class="form-control" >
                                                    <option value="" {{ request('store_id')==''?'selected':''}}>Select Rider</option>
                                                    @foreach($riders as $rider)
                                                        <option value="{{$rider->id}}" {{request('rider_id')==$rider->id?'selected':''}}>{{ $rider->name }}</option>                                    @endforeach

                                                </select>
                                                </div><br><br>
                                                <div class="col-4">
                                                    <input  id="fullname"  class="form-control" name="fromdate" placeholder=" search name" value="{{request('fromdate')}}"  type="date" />
                                                </div><br><br>
                                                <div class="col-4">
                                                    <input  id="fullname"  class="form-control" name="todate" placeholder=" search name" value="{{request('todate')}}"  type="date" />
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
                                        <th>OrderID</th>
                                        <th>Store</th>
                                        <th>Rider</th>
                                        <th>Item</th>
                                        <th>Size</th>
                                        <th>Cost</th>
                                        <th>Returned Quantity</th>
                                        <th>Reason</th>
                                        <th>Time</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($returnproducts as $returnproduct)
                                        <tr>
                                            <td>{{$returnproduct->order->refid??''}}</td>
                                            <td>{{$returnproduct->storename->name??''}}</td>
                                            <td>{{$returnproduct->rider->name??''}}</td>
                                            <td>{{$returnproduct->name}}</td>
                                            <td>{{$returnproduct->size->size??''}}</td>
                                            <td>{{$returnproduct->price}}</td>
                                            <td>{{$returnproduct->quantity}}</td>
                                            <td>{{$returnproduct->reason}}</td>
                                            <td>{{$returnproduct->created_at}}</td>
                                        </tr
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        {{$returnproducts->links()}}
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
