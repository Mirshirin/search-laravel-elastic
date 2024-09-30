
@extends('layouts.app')
@section('content')


    <div class="container">
      <div class="col-md-10 offset-1 mt-5">
             <h2 class="text-center">**********Search Data in Laravel 11 full text**************</h2>
             <div class="search-container">
             <form action="{{ route('product.index') }}" method="get" >
              <input type="text" name="search" class="form-control" id="search-input" placeholder="Search...">

              <ul id="suggestion-list"></ul>
              <button class="btn btn-success" type="submit">Search</button>
              </form>
            </div>
            <div>
            <form action="{{ route('products.reindex') }}" method="post" >
               @csrf
              <button class="btn btn-success" type="submit">Reindex</button>
              </form>
            </div> 
            <div>
            <form action="{{ route('products.getIndex') }}" method="post" >
            @csrf

              <button class="btn btn-success" type="submit">Get index</button>
              </form>
            </div> 
            <a href="{{ route('products.create') }}" class="btn btn-danger continue-shopping-btn">Create Product</a>           
            <h2>Search Results</h2>
            <ul id="search-results">
                <!-- Results will be displayed here -->
            </ul>
            <div class="input-group mb-3">                
                <table class="table table-bordered data-table">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Id</th>
                      <th>name</th>
                      <th>Description</th>
                      <th>Price</th>
                      <th>ImageName</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  @php
                  $key=1;
                  @endphp
                  <tbody>
                    @foreach ($products as $product)
                    <tr>
                      <td>{{  $key++   }}</td>
                      <td>{{ $product['id']}}</td>
                      <td>{{ $product['name'] }}</td>
                      <td>{{ $product['description'] }}</td>
                      <td>{{ $product['price'] }}</td>
                      <td>{{ $product['image'] }}</td>
                      <td>
                          <a href="{{ route('products.edit',$product['id']) }}" class="btn btn-sm btn-info">Edit</a>      
                          <form action="{{ route('products.destroy',$product['id']) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger">Delete</button>
                          </form>                       
                      </td>
                    </tr>
                    @endforeach

                  </tbody>                 
                </table>
                <div>
                  <div>
                    @if($currentPage >1)
                    <a href="?search={{ request('search')}}&page={{ $currentPage - 1 }}">Previous Page</a>
                    @endif 
                  </div>
                  <div>
                    @if($totalResults > ($currentPage * $size))
                    <a href="?search={{ request('search') }}&page={{ $currentPage + 1 }}">Next Page</a>
                    @endif 
                  </div>
                  
                </div>
               
            </div>  
      </div>             
    <div>  
   



@endsection