@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Sub-Admin</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{route('subadmin.list')}}">Subadmin</a></li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Sub-Admin Edit</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('subadmin.edit',['id'=>$subadmin->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Name</label>
                                                <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" value="{{$subadmin->name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Email</label>
                                                <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter Email" value="{{$subadmin->email}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Mobile</label>
                                                <input type="text" name="mobile" class="form-control" id="exampleInputEmail1" placeholder="Enter Mobile" value="{{$subadmin->mobile}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Address</label>
                                                <input type="text" name="address" class="form-control" id="exampleInputEmail1" placeholder="Enter Address" value="{{$subadmin->address}}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Password</label>
                                                <input type="text" name="password" class="form-control" id="exampleInputEmail1" placeholder="Enter New Password" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select class="form-control" name="status" required>
                                                    <option value="1" {{$subadmin->status=='1'?'Selected':''}}>Active</option>
                                                    <option value="0" {{$subadmin->status=='0'?'Selected':''}}>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Dashboard Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[dashboard-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('dashboard-viewer')) {{'checked'}} @endif> View Dashboard
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Banner Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[banner-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('banner-viewer')) {{'checked'}} @endif> View Banner
                                                    <input type="checkbox" name="permissions[banner-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('banner-editor')) {{'checked'}} @endif> Add/Update Banners
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Category Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[category-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('category-viewer')) {{'checked'}} @endif> View Category
                                                    <input type="checkbox" name="permissions[category-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('category-editor')) {{'checked'}} @endif> Add/Update Category
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Sub-Category Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[subcategory-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('subcategory-viewer')) {{'checked'}} @endif> View Sub-Category
                                                    <input type="checkbox" name="permissions[subcategory-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('subcategory-editor')) {{'checked'}} @endif> Add/Update Sub-Category
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Product Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[product-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('product-viewer')) {{'checked'}} @endif> View Product
                                                    <input type="checkbox" name="permissions[product-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('product-editor')) {{'checked'}} @endif> Add/Update Product
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Customer Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[customer-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('customer-viewer')) {{'checked'}} @endif> View Customer
                                                    <input type="checkbox" name="permissions[customer-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('customer-editor')) {{'checked'}} @endif> Add/Update Customer
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Coupon Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[coupon-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('coupon-viewer')) {{'checked'}} @endif> View Coupon
                                                    <input type="checkbox" name="permissions[coupon-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('coupon-editor')) {{'checked'}} @endif> Add/Update Coupon
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Order Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[order-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('order-viewer')) {{'checked'}} @endif> View Order
                                                    <input type="checkbox" name="permissions[order-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('order-editor')) {{'checked'}} @endif> Add/Update Order
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Sales Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[sale-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('sale-viewer')) {{'checked'}} @endif> View Sales
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Return Product Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[return-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('return-viewer')) {{'checked'}} @endif> View Return Products
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Inventory Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[inventory-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('inventory-viewer')) {{'checked'}} @endif> View Inventory
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Notification Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[notification-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('notification-editor')) {{'checked'}} @endif> Send Notifications
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Complaints Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[complaint-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('complaint-viewer')) {{'checked'}} @endif> View Complaint
                                                    <input type="checkbox" name="permissions[complaint-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('complaint-editor')) {{'checked'}} @endif> Reply Complaint
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Area List Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[arealist-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('arealist-viewer')) {{'checked'}} @endif> View Area List
                                                    <input type="checkbox" name="permissions[arealist-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('arealist-editor')) {{'checked'}} @endif> Add/Update Area List
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Rider Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[rider-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('rider-viewer')) {{'checked'}} @endif> View Riders
                                                    <input type="checkbox" name="permissions[rider-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('rider-editor')) {{'checked'}} @endif> Add/Update Riders
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Store Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[store-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('store-viewer')) {{'checked'}} @endif> View Stores
                                                    <input type="checkbox" name="permissions[store-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('store-editor')) {{'checked'}} @endif> Add/Update Stores
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Configuration Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[configuration-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('configuration-editor')) {{'checked'}} @endif> View Configuration
                                                    <input type="checkbox" name="permissions[configuration-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Update Configuration
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Sub-Admin Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[subadmin-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('subadmin-viewer')) {{'checked'}} @endif> View Sub-Admins
                                                    <input type="checkbox" name="permissions[subadmin-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('subadmin-editor')) {{'checked'}} @endif> Add/Update Sub-Admins
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Purchase Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[purchase-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('purchase-viewer')) {{'checked'}} @endif> View Purchase Items
                                                    <input type="checkbox" name="permissions[purchase-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('purchase-editor')) {{'checked'}} @endif> Add/Update Purchase Items
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Return Request Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[returnrequest-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('returnrequest-viewer')) {{'checked'}} @endif> View Return Request
                                                    <input type="checkbox" name="permissions[returnrequest-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('returnrequest-editor')) {{'checked'}} @endif> Add/Update Return Request
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Timeslot Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[timeslot-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('timeslot-viewer')) {{'checked'}} @endif> View Timeslot
                                                    <input type="checkbox" name="permissions[timeslot-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if($subadmin->hasRole('timeslot-editor')) {{'checked'}} @endif> Add/Update Timeslot
                                                </div>
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
        <!-- /.content -->

    </div>
    <!-- ./wrapper -->
@endsection

