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
							   
        <form class="form-validate form-horizontal"  method="post" action="/orders/order_search " enctype="multipart/form-data">
                           <input type = "hidden" name = "_token" value = "<?php echo csrf_token(); ?>">
                     <div class="row">
					      <div class="col-4"> 
                           <input  id="fullname" onfocus="this.value=''" class="form-control" name="search" placeholder=" search name" value=""  type="text" />
                           </div>
					  <div class="col-4">
                          <select id="status" name="status" class="form-control" >
							  
                             <option value="">Please Select Order Status</option>
                             
                             <option value="pending">Pending</option>
                             <option value="confirmed">Confirmed</option>
                             <option value="cancelled">Cancelled</option>
                          </select>
                      </div>
                      <div class="col-4">
                          <select id="payment_status" name="payment_status" class="form-control" >
							  
                             <option value="">Please Select Payment Status</option>
                             
                             <option value="paid">Paid</option>
                             <option value="payment-wait">payment-wait</option>
                          </select>
                      </div><br><br>
                      <div class="col-4"> 
                           <input  id="fullname" onfocus="this.value=''" class="form-control" name="fromdate" placeholder=" search name" value=""  type="date" />
                           </div>
                           <div class="col-4"> 
                           <input  id="fullname" onfocus="this.value=''" class="form-control" name="todate" placeholder=" search name" value=""  type="date" />
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
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Cost</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Payment Mode</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                     @if(!empty($order->details[0]->entity) && $order->details[0]->entity instanceof \App\Models\Product)
                                        <tr>
                                            <td>{{$order->refid}}</td>
                                            <td>{{$order->customer->name??''}} <br>Mob: {{$order->customer->mobile??''}}</td>
                                            <td>{{$order->created_at}}</td>
                                            <td>
                                                @if(!empty($order->details[0]->entity) && $order->details[0]->entity instanceof \App\Models\Therapy)
                                                Therapy Booking
                                                @else
                                                Product Purchase
                                                @endif
                                            </td>
                                            <td>{{$order->total_cost}}</td>
                                            <td>{{$order->status}}</td>
                                            <td>{{$order->payment_status}}</td>
                                            <td>{{$order->payment_mode}}</td>
                                            <td><a href="{{route('order.view',['id'=>$order->id])}}" class="btn btn-success">View</a>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>OrderID</th>
                                        <th>User</th>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Cost</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Payment Mode</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        {{$orders->links()}}
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
