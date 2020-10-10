@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Sales</h1>
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

                                                    <select id="store_id" name="store_id" class="form-control" >
                                                        <option value="" {{ request('store_id')==''?'selected':''}}>Select Store</option>
                                                        @foreach($stores as $store)
                                                            <option value="{{$store->id}}" {{request('store_id')==$store->id?'selected':''}}>{{ $store->name }}</option>                                    @endforeach

                                                    </select>

                                                </div>
                                                <div class="col-4">
                                                    <input  id="fullname"  class="form-control" name="fromdate" placeholder=" search name" value="{{request('fromdate')}}"  type="date" />
                                                </div><br><br>
                                                <div class="col-4">
                                                    <input  id="fullname"  class="form-control" name="todate" placeholder=" search name" value="{{request('todate')}}"  type="date" />
                                                </div>
                                                <div class="col-4">

                                                    <select id="" name="order_by" class="form-control" >
                                                        <option value="" {{ request('order_by')==''?'selected':''}}>Order By</option>
                                                        <option value="quantity-asc" {{ request('order_by')=='quantity-asc'?'selected':''}}>Quantity Asc</option>
                                                        <option value="quantity-desc" {{ request('order_by')=='quantity-desc'?'selected':''}}>Quantity Desc</option>
                                                        <option value="amount-asc" {{ request('order_by')=='amount-asc'?'selected':''}}>Amount Asc</option>
                                                        <option value="amount-desc" {{ request('order_by')=='amount-desc'?'selected':''}}>Amount Desc</option>


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
                                        <th>Total Quantity</th>
                                        <th>Total Cost</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($sales as $sale)
                                        <tr>
                                            <td>{{$sale->entity->name??''}}</td>
                                            <td>{{$sale->size->size??''}}</td>
                                            <td>{{$sale->quantity}}</td>
                                            <td>{{$sale->cost}}</td>
                                        </tr
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        {{$sales->links()}}
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
