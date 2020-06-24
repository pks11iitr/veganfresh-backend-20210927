@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Customers</h1>
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
				              
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>DOB</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Image</th>
                    <th>Isactive</th>
                   <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				@foreach($customers as $customer)  
                  <tr>
					  <td>{{$customer->name}}</td>
					  <td>{{$customer->mobile}}</td>
					  <td>{{$customer->email}}</td>
					  <td>{{$customer->dob}}</td>
					  <td>{{$customer->address}}</td>
					  <td>{{$customer->city}}</td>
					  <td>{{$customer->state}}</td>
                      <td><img src="{{$customer->image}}" height="80px" width="80px"/></td>
                       <td>
                        @if($customer->status==1){{'Active'}}
                             @elseif($customer->status==2){{'Blocked'}}@else{{'Inactive'}}
                             @endif
                        </td>
                      <td><a href="{{route('customer.edit',['id'=>$customer->id])}}" class="btn btn-success">Edit</a></br></br>
                 </tr>
                 @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>DOB</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Image</th>
                    <th>Isactive</th>
                   <th>Action</th>
                  </tr>                 
                  </tfoot>
                </table>
              </div>
              {{$customers->links()}}
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

