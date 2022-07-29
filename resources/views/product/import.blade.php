@extends('layouts.main')
@section('content')
<div class="content-wrapper">
<section class="content-header">
      <h1>
        
      </h1>
      
    </section>
<section class="content">
      <!-- Small boxes (Stat box) -->
	
<div class="row">
  <section class="content">
      <div class="row">
        <div class="col-xs-12">
            <div class="box">
            
            <!-- /.box-header -->
            <div class="box-body">
             
              <div class="col-md-12 col-sm-12 col-xs-12">
      <form action="{{URL::to('/')}}/product/import_product" method="post" enctype="multipart/form-data">
      @csrf
        <h3 class="text-center">Import Products</h3>
        <div class="profile text-center">
          <div class="drag">
             <div class="upload-drop-zone" id="drop-zone">
                <label class="btn-bs-file brow">
                  <input type="file" name="csv_file" id="csv_file" />
                </label>
             </div>
          </div>
       </div>
        
        <div class="innerryt_linkbtn text-center">
          <button class="btn btn-primary" type="submit">Import</button>
        </div>
        </form>
        </div>
       </div>
      </div>
     </div>
    </div>
  </section>
</div>
  	</section>
 </div>
@stop