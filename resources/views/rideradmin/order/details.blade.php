@extends('layouts.admin')
@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">

                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Order </a></li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                    {{--<div class="card-header">
                        <h3 class="card-title">Order Detail</h3>
                    </div>--}}
                    <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Order Details</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>RefId</td><td>{{$order->refid}}</td>
                                </tr>
                                <tr>
                                    <td>Date & Time</td><td>{{$order->created_at}}</td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>{{$order->total_cost+$order->coupon_discount+$order->delivery_charge}}</td>
                                </tr>
                                <tr>
                                    <td>Delivery Charge</td>
                                    <td>{{$order->delivery_charge}}</td>
                                </tr>
                                <tr>
                                    <td>Coupon Discount</td><td>{{$order->coupon_discount }}</td>
                                </tr>
                                <tr>
                                    <td>Coupon Applied</td><td>{{$order->coupon_applied}}</td>
                                </tr>
                                <tr>
                                    <td>Payment Status</td><td>{{$order->payment_status}} &nbsp; @if(in_array($order->payment_status, ['payment-wait']))
                                            <a href="{{route('payment.status.change', ['id'=>$order->id,'status'=>'paid'])}}" name='status' class="btn btn-primary">Mark As Paid</a>
                                        @endif</td>
                                </tr>
                                <tr>
                                    <td>Payment Mode</td><td>{{$order->payment_mode}}</td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>{{$order->status}}<br><br>
                                        @if(in_array($order->status, ['confirmed']))
                                            <a href="{{route('order.status.change', ['id'=>$order->id,'status'=>'processing'])}}" name='status' class="btn btn-primary">Processing</a>
                                        @endif
                                        @if(in_array($order->status, ['processing']))
                                            <a href="{{route('order.status.change', ['id'=>$order->id,'status'=>'dispatched'])}}" name='status' class="btn btn-primary">Dispatched</a>
                                        @endif
                                        @if(in_array($order->status, ['dispatched']))
                                            <a href="{{route('order.status.change', ['id'=>$order->id,'status'=>'delivered'])}}" name='status' class="btn btn-primary">Delivered</a><br><br>
                                        @endif
                                        @if(in_array($order->status, ['confirmed', 'pending', 'processing']))
                                            <a href="{{route('order.status.change', ['id'=>$order->id,'status'=>'cancelled'])}}" name='status' class="btn btn-primary">Cancelled</a>
                                        @endif
                                        @if(in_array($order->status, ['return-request']))
                                            <a href="{{route('order.status.change', ['id'=>$order->id,'status'=>'return-accepted'])}}" name='status' class="btn btn-primary">Return-accepted</a>
                                        @endif
                                        @if(in_array($order->status, ['return-accepted', 'cancelled']) && $order->payment_status=='paid')
                                            <a href="{{route('order.status.change', ['id'=>$order->id, 'status'=>'refunded'])}}" name='status' class="btn btn-primary">Refunded</a>
                                        @endif
                                        @if(in_array($order->status, ['delivered']))
                                            <a href="{{route('order.status.change', ['id'=>$order->id,'status'=>'completed'])}}" name='status' class="btn btn-primary">Completed</a>
                                        @endif
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Product Details</th>
                                    {{--<th></th>--}}
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->details as $detail)
                                    <tr>
                                        <td>{{$detail->entity->name??''}}</td>
                                        {{--<td>{{$detail->size->name??''}}</td>--}}
                                        <td>Quantity: {{$detail->quantity}}</td>
                                        <td>Rs. {{$detail->cost}}/Item</td>
                                        <td>Rs. {{$detail->cost*$detail->quantity}} Total</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Customer Details</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Name</td><td>{{$order->name}}</td>
                                </tr>
                                <tr>
                                    <td>Mobile</td><td>{{$order->mobile}}</td>
                                </tr>
                                <tr>
                                    <td>Email</td><td>{{$order->email}}</td>
                                </tr>
                                <tr>
                                    <td>Adress</td><td>{{$order->address}}</td>
                                </tr>
                                </tbody>

                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>

@endsection
