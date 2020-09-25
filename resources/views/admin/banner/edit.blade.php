@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Banner Update</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Banner Update</li>
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
                <h3 class="card-title">Banner Update</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="post" enctype="multipart/form-data" action="{{route('banners.update',['id'=>$banner->id])}}">
                 @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Entity Type</label>
                                <select class="form-control select2" name="entity_type">

                                    @if($banner->entity_type=='App\Models\Category')
                                        @foreach($offercategorys as $offercategory)
                                            <option value="offer_{{$offercategory->id}}"
                                            @if($banner->entity_id==$offercategory->id){{'selected'}}@endif>{{$offercategory->name}}</option>
                                        @endforeach
                                        @foreach($subcategorys as $subcategory)
                                            <option value="subcat_{{$subcategory->id}}"
                                            @if($banner->entity_id==$subcategory->id){{'selected'}}@endif>{{$subcategory->name}}</option>
                                        @endforeach
                                            @foreach($categorys as $category)
                                                <option value="cat_{{$category->id}}"
                                                @if($banner->entity_id==$category->id){{'selected'}}@endif>{{$category->name}}</option>
                                            @endforeach
                                    @endif

                                    @if($banner->entity_type=='App\Models\SubCategory')
                                            @foreach($offercategorys as $offercategory)
                                                <option value="offer_{{$offercategory->id}}"
                                                @if($banner->entity_id==$offercategory->id){{'selected'}}@endif>{{$offercategory->name}}</option>
                                            @endforeach
                                                @foreach($categorys as $category)
                                                    <option value="cat_{{$category->id}}"
                                                    @if($banner->entity_id==$category->id){{'selected'}}@endif>{{$category->name}}</option>
                                                @endforeach

                                    @foreach($subcategorys as $subcategory)
                                        <option value="subcat_{{$subcategory->id}}"
                                        @if($banner->entity_id==$subcategory->id){{'selected'}}@endif>{{$subcategory->name}}</option>
                                    @endforeach
                                        @endif

                                    @if($banner->entity_type=='App\Models\OfferCategory')
                                            @foreach($categorys as $category)
                                                <option value="cat_{{$category->id}}"
                                                @if($banner->entity_id==$category->id){{'selected'}}@endif>{{$category->name}}</option>
                                            @endforeach
                                                @foreach($subcategorys as $subcategory)
                                                    <option value="subcat_{{$subcategory->id}}"
                                                    @if($banner->entity_id==$subcategory->id){{'selected'}}@endif>{{$subcategory->name}}</option>
                                                @endforeach
                                    @foreach($offercategorys as $offercategory)
                                        <option value="offer_{{$offercategory->id}}"
                                        @if($banner->entity_id==$offercategory->id){{'selected'}}@endif>{{$offercategory->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <!-- /.form-group -->
                            <div class="form-group">
                                <label>Image</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="image" class="custom-file-input" id="exampleInputFile">
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="">Upload</span>
                                    </div>
                                </div>
                            </div>
                            <img src="{{$banner->image}}" height="100" width="200">
                            <!-- /.form-group -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Isactive</label>
                                <select class="form-control select2" name="isactive">
                                    <option value="">Please Select Status</option>
                                    <option value="1" {{$banner->isactive==1?'selected':''}}>Yes</option>
                                    <option value="0" {{$banner->isactive==0?'selected':''}}>No</option>
                                </select>
                            </div>
                            <!-- /.form-group -->
                        </div>
                        <!-- /.col -->
                    </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
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

