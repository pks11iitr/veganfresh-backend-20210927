@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Customer</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{route('customer.list')}}">Customer</a></li>
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
                <h3 class="card-title">Customer Update</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="post" enctype="multipart/form-data" action="{{route('customer.update',['id'=>$customers->id])}}">
                 @csrf
                <div class="card-body">
                <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" value="{{$customers->name}}">
                  </div>


                  <!-- <div class="form-group">
                    <label for="exampleInputEmail1">Email</label>
                    <input type="text" name="email" class="form-control"  placeholder="Enter Email" value="{{$customers->email}}">
                  </div> -->



                  <div class="form-group">
                    <label for="exampleInputEmail1">DOB</label>
                    <input type="text" name="dob" class="form-control" id="exampleInputEmail1" placeholder="Enter Description" value="{{$customers->dob}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Address</label>
                    <input type="text" name="address" class="form-control" id="exampleInputEmail1" placeholder="Enter Address" value="{{$customers->address}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">City</label>
                    <input type="text" name="city" class="form-control" id="exampleInputEmail1" placeholder="Enter City" value="{{$customers->city}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">State</label>
                    <input type="text" name="state" class="form-control" id="exampleInputEmail1" placeholder="Enter State" value="{{$customers->state}}">
                  </div>

                    <div class="form-group">
                        <label>Is Active</label>
                        <select class="form-control" name="status" required>
                           <option  selected="selected" value="1" {{$customers->status==1?'selected':''}}>Active</option>
                            <option value="0" {{$customers->status==0?'selected':''}}>Inactive</option>
                            <option value="2" {{$customers->status==2?'selected':''}}>Blocked</option>
                        </select>
                      </div>
                    <div class="form-group">
                        <label>Membership</label>
                        <select class="form-control" name="active_membership" required>
                            <option value="0">Select Membership</option>
                            @foreach($memberships as $member)
                                <option value="{{$member->id}}" @if($member->id==$customers->active_membership){{'selected'}}@endif>{{$member->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Membership Expiry</label>
                        <input type="date" name="membership_expiry" class="form-control" id="exampleInputEmail1" placeholder="Enter State" value="{{$customers->membership_expity}}">
                    </div>
                  <div class="form-group">
                    <label for="exampleInputFile">File input</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" name="image" class="custom-file-input" id="exampleInputFile" accept="image/*">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>

                      </div>
                      <div class="input-group-append">
                        <span class="input-group-text" id="">Upload</span>

                      </div>

                    </div>
                  </div>
                  <image src="{{$customers->image}}" height="100" width="200">
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




    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                  <h3 class="card-title">Customer Address</h3>
              </div>
              <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                      <th>Name</th>
                      
                      <th>Mobile</th>
                      <th>Email</th> 
                      <th>House No</th>
                      <th>Floor</th> 
                      <th>Appertment Name</th> 
                    <th>society </th>
                    <th>landmark </th>
                    <th>area </th>
                    <th>city </th>
                    
                    <th>Address Type </th> 
                   
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($caddress as $cat)
                    <tr>
                        <td>{{$cat->first_name}} {{$cat->last_name}}</td>
                        
                        <td>{{$cat->mobile_no}}</td>
                        <td>{{$cat->email}}</td>
                        <td>{{$cat->house_no}}</td>
                        <td>{{$cat->floor}}</td>
                        <td>{{$cat->appertment_name}}</td>
                        <td>{{$cat->street}}</td>
                        <td>{{$cat->landmark}}</td>
                        <td>{{$cat->area}}</td>
                         
                        <td>{{$cat->city}} {{$cat->pincode}}</td>
                       
                        <td>{{$cat->address_type}}</td>
                    </tr>

                  @endforeach
                  </tbody>
              </table>
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

