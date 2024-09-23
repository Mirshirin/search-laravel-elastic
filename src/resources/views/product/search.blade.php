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
                    </tr>
                    @endforeach
                  </tbody>                 
                </table>
               
            </div>  
      </div>             
    <div>  
   



@endsection