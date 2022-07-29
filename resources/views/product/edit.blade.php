@extends('layouts.form')
@section('content')
<style>
    .error{
        color:red !important;
    }
    img {
    vertical-align: middle;
    border-style: none;
    border-radius: 50%;
}
</style>
<div class="registration-form">
@if (Session::has('message'))
                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">
                        {{ Session::get('message') }}
                    </p>
                @endif
        <form action="{{ route('product.update') }}" method="post" id="add_product" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <input type="hidden" name="id" value="{{$product->id}}">
                <input type="text" class="form-control item" value="{{$product->title}}" name="title" placeholder="Product Name" >
            </div>
            <div class="form-group">
                <textarea class="form-control item"  name="description" placeholder="product Description" >{{$product->description}}</textarea>
            </div>
            <div class="form-group">
                <input type="text" class="form-control item" name="price" value="{{$product->price}}" placeholder="Product Price">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-block create-account" value="Update Product">
            </div>
        </form>
        <div class="social-media">
            <a href="{{ route('index') }}">BACK</a>
        </div>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#add_product").validate({
        errorElement: 'span',
        rules: {
            post_name: {
                required: true,
                minlength: 3,
                maxlength: 30
            },
            post_content: {
                required: true,
                minlength: 3,
                maxlength: 30
            },
            image: {
                required: true,
            },


        },
        messages: {
            post_name: {
                required: " Product name is required",
            },
            post_content: {
                required: "Product Description is required",
            }
        },
    });


    });
</script>
@endsection