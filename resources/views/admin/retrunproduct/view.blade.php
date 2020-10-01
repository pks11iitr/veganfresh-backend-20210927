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
                        <div class="card card-primary">
                            <div class="card-header">
                                    <h3 class="card-title">Retrun Product</h3>

                                {{--<div class="row">
                                    <div class="col-12">

                                        <form class="form-validate form-horizontal"  method="get" action="" enctype="multipart/form-data">

                                            <div class="row">
                                                <div class="col-4">
                                                    <input  id="fullname"  class="form-control" name="search" placeholder=" search name/email/mobile" value="{{request('search')}}"  type="text" />
                                                </div>
                                                <div class="col-4">

                                                    <select id="status" name="status" class="form-control" >

                                                        <option value="" {{ request('status')==''?'selected':''}}>Please select</option>
                                                        <option value="pending" {{ request('status')=='pending'?'selected':''}}>pending</option>
                                                        <option value="confirmed" {{ request('status')==='confirmed'?'selected':''}}>confirmed</option>
                                                        <option value="cancelled" {{ request('status')=='cancelled'?'selected':''}}>cancelled</option>
                                                    </select>

                                                </div>
                                                <div class="col-4">
                                                    <select id="payment_status" name="payment_status" class="form-control" >

                                                        <option value="" {{ request('payment_status')==''?'selected':''}}>Please Select</option>
                                                        <option value="paid" {{ request('payment_status')=='paid'?'selected':''}}>paid</option>
                                                        <option value="payment-wait" {{ request('payment_status')==='payment-wait'?'selected':''}}>payment-wait</option>

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

                                </div>--}}
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>OrderID</th>
                                        <th>Item</th>
                                        <th>Size</th>
                                        <th>Cost</th>
                                        <th>Returned Quantity</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($returnproducts as $returnproduct)
                                        <tr>
                                            <td>{{$returnproduct->order->refid??''}}</td>
                                            <td>{{$returnproduct->name}}</td>
                                            <td>{{$returnproduct->size->size??''}}</td>
                                            <td>{{$returnproduct->price}}</td>
                                            <td>{{$returnproduct->quantity}}</td>
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
