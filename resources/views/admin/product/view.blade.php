@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Products</h1>
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
			  <div class="col-3">
                <a href="{{route('product.create')}}" class="btn btn-primary">Add Product</a></div>&nbsp;&nbsp;
                 <a href="{{route('product.bulk.form')}}" class="btn btn-primary">Bulk Upload</a></div>

            <div class="col-9">
		 <form class="form-validate form-horizontal"  method="get" action="" enctype="multipart/form-data">

                     <div class="row">
					      <div class="col-4">
                           <input  id="fullname" onfocus="this.value=''" class="form-control" name="search" placeholder=" search name" value="{{request('search')}}"  type="text" />
                           </div>
					  <div class="col-4">
                          <select id="ordertype" name="ordertype" class="form-control" >
                             <option value="DESC" {{ request('ordertype')=='DESC'?'selected':''}}>DESC</option>
                              <option value="ASC" {{ request('ordertype')=='ASC'?'selected':''}}>ASC</option>
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
                    <th>Company</th>
                    <th>Ratings</th>
                    <th>Is Offer</th>
                    <th>Is Hotdeals</th>
                    <th>Is NewArrival</th>
                    <th>Is Discounted</th>
                    <th>Stock Type</th>
{{--                    <th>Min Qty</th>--}}
{{--                    <th>Max Qty</th>--}}
                    <th>Stock</th>
{{--                    <th>Image</th>--}}
                    <th>Isactive</th>
                   <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				@foreach($products as $product)
                  <tr>
					  <td>{{$product->name}}</td>
					  <td>{{$product->company}}</td>
					  <td>{{$product->ratings}}</td>
					  <td> @if($product->is_offer==1){{'Yes'}}
                          @else{{'No'}}
                          @endif
                      </td>
                      <td> @if($product->is_hotdeal==1){{'Yes'}}
                          @else{{'No'}}
                          @endif
                      </td>
                      <td> @if($product->is_newarrival==1){{'Yes'}}
                          @else{{'No'}}
                          @endif
                      </td>
                      <td> @if($product->is_discounted==1){{'Yes'}}
                          @else{{'No'}}
                          @endif
                      </td>
					  <td>{{$product->stock_type}}</td>
{{--					  <td>{{$product->max_qty}}</td>--}}
					  <td>{{$product->stock}}</td>
{{--                      <td><img src="{{$product->image}}" height="80px" width="80px"/></td>--}}
                       <td>
                        @if($product->isactive==1){{'Yes'}}
                             @else{{'No'}}
                             @endif
                        </td>
                      <td><a href="{{route('product.edit',['id'=>$product->id])}}" class="btn btn-success">Edit</a></td>
                 </tr>
                 @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                      <th>Name</th>
                      <th>Company</th>
                      <th>Ratings</th>
                      <th>Is Offer</th>
                      <th>Is Hotdeals</th>
                      <th>Is NewArrival</th>
                      <th>Is Discounted</th>
                      <th>Stock Type</th>
{{--                      <th>Min Qty</th>--}}
{{--                      <th>Max Qty</th>--}}
{{--                      <th>Stock</th>--}}
{{--                      <th>Image</th>--}}
                      <th>Isactive</th>
                      <th>Action</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
              {{$products->links()}}
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

