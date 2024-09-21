@extends('layouts.app')
@section('content')
    <div class="container">
      <div class="col-md-10 offset-1 mt-5">
             <h2 class="text-center">**********Search Data in Laravel 11 full text**************</h2>
            <form action="{{ route('product.index') }}" method="get" >
              <input type="text" name="search" value="{{ $query }}" class="form-control" placeholder="Search.......">
              <button class="btn btn-success" type="submit">Search</button>
            </form>
            <div class="input-group mb-3">                
                <table class="table table-bordered data-table">
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>Title</th>
                      <th>Description</th>
                      <th>Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($products as $product)
                    <tr>
                      <td>{{ $product->id }}</td>
                      <td>{{ $product->title }}</td>
                      <td>{{ $product->description }}</td>
                      <td>{{ $product->price }}</td>
                    </tr>
                    @endforeach
                  </tbody>                 
                </table>
            </div>  
      </div>             
    <div>  
    


@endsection