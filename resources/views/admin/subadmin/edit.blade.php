@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Store</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Store</li>
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
                                <h3 class="card-title">Store Edit</h3>
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
                                                    <input type="checkbox" name="permissions[dashboard-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if(auth()->user()->hasRole('dashboard-viewer')) {{'checked'}} @endif> View Dashboard
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Banner Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[banner-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if(auth()->user()->hasRole('banner-viewer')) {{'checked'}} @endif> View Banner
                                                    <input type="checkbox" name="permissions[banner-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if(auth()->user()->hasRole('banner-editor')) {{'checked'}} @endif> Add/Update Banners
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Category Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[category-viewer]"  id="exampleInputEmail1" placeholder="Enter Password" @if(auth()->user()->hasRole('category-viewer')) {{'checked'}} @endif> View Category
                                                    <input type="checkbox" name="permissions[category-editor]"  id="exampleInputEmail1" placeholder="Enter Password" @if(auth()->user()->hasRole('category-editor')) {{'checked'}} @endif> Add/Update Category
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Sub-Category Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[subcategory-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Sub-Category
                                                    <input type="checkbox" name="permissions[subcategory-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Add/Update Sub-Category
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Product Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[product-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Product
                                                    <input type="checkbox" name="permissions[product-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Add/Update Product
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Customer Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[customer-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Customer
                                                    <input type="checkbox" name="permissions[customer-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Add/Update Customer
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Coupon Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[coupon-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Coupon
                                                    <input type="checkbox" name="permissions[coupon-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Add/Update Coupon
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Order Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[order-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Order
                                                    <input type="checkbox" name="permissions[order-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Add/Update Order
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Sales Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[sale-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Sales
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Return Product Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[return-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Return Products
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Inventory Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[inventory-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Inventory
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Notification Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[notification-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Send Notifications
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Complaints Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[complaint-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Complaint
                                                    <input type="checkbox" name="permissions[complaint-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Reply Complaint
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Area List Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[arealist-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Area List
                                                    <input type="checkbox" name="permissions[arealist-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Add/Update Area List
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Rider Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[rider-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Riders
                                                    <input type="checkbox" name="permissions[rider-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Add/Update Riders
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Store Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[store-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Stores
                                                    <input type="checkbox" name="permissions[store-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Add/Update Stores
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Configuration Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[configuration-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> View Configuration
                                                    <input type="checkbox" name="permissions[configuration-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Update Configuration
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Sub-Admin Permissions</label><br>
                                                <div>
                                                    <input type="checkbox" name="permissions[subadmin-viewer]"  id="exampleInputEmail1" placeholder="Enter Password"> View Sub-Admins
                                                    <input type="checkbox" name="permissions[subadmin-editor]"  id="exampleInputEmail1" placeholder="Enter Password"> Add/Update Sub-Admins
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

