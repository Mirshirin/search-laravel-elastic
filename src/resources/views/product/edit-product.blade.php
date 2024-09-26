@extends('layouts.app')
@section('content')



<div class="col-md-10 offset-1 mt-5">
  <div class="card">
<div class="card-body">
    <h4 class="btn btn-danger continue-shopping-btn">Edit Product</h4>  
    <form id="frm" class="form-inline" method="POST" action="{{ route('products.update',$product->id) }} ">
        @csrf
        @method('put')
      <label class="sr-only-visible" for="inlineFormInputName2">Name</label>
      <input type="text" class="form-control mb-2 mr-sm-2" name="name"  value="{{ old('name', $product->name) }}" placeholder="Enter  name" style= "background-color:white !important; color: black;" >
      @error('name')
                <div class= "error">{{ $message }}</div>
      @enderror 
      <div class="mb-3">
      <label class="sr-only-visible" for="inlineFormInputName2">description</label>
      <textarea class="form-control mb-2 mr-sm-2" name="description" rows="5">{{ old('description', $product->description) }}</textarea>
      </div>
      <label class="sr-only-visible" for="inlineFormInputName2">Product Price</label>
      <input type="number" class="form-control mb-2 mr-sm-2" name="price" value="{{ old('price', $product->price) }}" placeholder="Enter price " style= "background-color:white !important; color: black;" >
      @error('price')
                <div class= "error">{{ $message }}</div>
      @enderror
      
      <label class="sr-only-visible" for="inlineFormInputName2">Image</label>
      <input type="file" class="form-control"   name="image"  placeholder="Enter product discount " style= "background-color:white !important; color: black;" >
      <img src="/storage/{{ $product->image }}" alt="{{ $product->name }}" height="60px" class="mb-2 mt-2">
      @error('image')
                  <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
      @enderror
      <br>
      <button type="submit" class="btn btn-primary mb-2">Submit</button>
    </form>
  </div>
</div>
</div>
@endsection
