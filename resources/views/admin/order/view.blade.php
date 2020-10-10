@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Order</h1>
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
                                <div class="row">
                                    <div class="col-12">

                                        <form class="form-validate form-horizontal"  method="get" action="" enctype="multipart/form-data">

                                            <div class="row">
                                                <div class="col-4">
                                                    <input  id="fullname"  class="form-control" name="search" placeholder=" search name/email/mobile/order id" value="{{request('search')}}"  type="text" />
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

                                                </div>

                                                <div class="col-4">

                                                    <select id="status" name="status" class="form-control" >

                                                        <option value="" {{ request('status')==''?'selected':''}}>Please select</option>
                                                        <option value="pending" {{ request('status')=='confirmed'?'selected':''}}>New Order</option>
                                                        <option value="confirmed" {{ request('status')==='dispatched'?'selected':''}}>Dispatched</option>
                                                        <option value="cancelled" {{ request('status')=='cancelled'?'selected':''}}>Cancelled</option>
                                                        <option value="cancelled" {{ request('status')=='completed'?'selected':''}}>Completed</option>
                                                    </select>

                                                </div><br><br>
                                                <div class="col-4">
                                                    <select id="payment_status" name="payment_status" class="form-control" >

                                                        <option value="" {{ request('payment_status')==''?'selected':''}}>Select Payment Status</option>
                                                        <option value="paid" {{ request('payment_status')=='paid'?'selected':''}}>Paid</option>
                                                        <option value="payment-wait" {{ request('payment_status')==='payment-wait'?'selected':''}}>Pending</option>

                                                    </select>

                                                </div><br><br>
                                                <div class="col-4">
                                                    <input  id="fullname"  class="form-control" name="fromdate" placeholder=" search name" value="{{request('fromdate')}}"  type="date" />
                                                </div>
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
                                        <th>User</th>
                                        <th>Store Name</th>
                                        <th>Rider Name</th>
                                        <th>Delivery Slot</th>
                                        <th>Cost</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Payment Mode</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                            <tr>
                                                <td>{{$order->refid}}</td>
                                                <td>{{$order->storename->name??''}}</td>
                                                <td>{{$order->rider->name??''}}</td>
                                                <td>{{$order->customer->name??''}} <br>Mob: {{$order->customer->mobile??''}}</td>
                                                <td>{{$order->delivery_date}} {{$order->timeslot->name??''}}</td>
                                                <td>{{$order->total_cost+$order->delivery_charge}}</td>
                                                <td>{{$order->status}}</td>
                                                <td>{{$order->payment_status}}</td>
                                                <td>{{$order->payment_mode}}</td>
                                                <td>
                                                    <a href="{{route('order.details',['id'=>$order->id])}}" class="btn btn-primary">Details</a><br><br>
                                                    <a href="{{route('invoice.view',['id'=>$order->id])}}" class="btn btn-primary">Invoice</a>
                                                </td>
                                            </tr
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        {{$orders->links()}}
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
