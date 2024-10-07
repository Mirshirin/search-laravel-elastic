*********************search-laravel-elastic
# Shop API Documentation

## Base URL
All API endpoints are prefixed with `/api`. For example:
http://localhost:8090/api/products

## Endpoints

### 1. Get All Products
- **Method:** GET
- **URL:** `http://localhost:8090/api/products`
- **Response:**
```json
[
  {
    "id": 1,
    "name": "Product 1",
    "description": "Description of product 1",
    "price": "100.00",
    "image": 2.jpg
  }
]
=============================================
### 2. Add a New Product

    • Method: POST
    • URL: http://localhost:8090/api/products
    • Request Body:
{
  "name": "New Product",
  "description": "Product description",
  "price": 150.00,
  "image": 1.jpg
}
Response:
{
  "id": 2,
  "name": "New Product",
  "description": "Product description",
  "price": "150.00",
  "image": 1.jpg
}
============================================
### 3. Update a Product
    • Method: PUT
    • URL: http://127.0.0.1:8090/api/products/26 or {id}
    • Request Body:
json
{
  "name": "aria",
  "description": "This is an updated description for 2 times",
  "price": 199.99,
  "image": "3.jpg"
}
Response:
{
  "id": 1,
  "name": "aria",
  "description": "This is an updated description for 2 times",
  "price": 199.99,
  "image": "3.jpg"
}
===========================================
### 4.Delete a Product
    • Method: DELETE
    • URL: http://localhost:8090/api/products/26 or {id}
    • Response:
json
{
  "message": "Product deleted successfully."
}
===========================================
### 4. Search Products (Elasticsearch)
    • Method: GET
    • URL:http://localhost:8090/api/search?search=perferendis&page=1
    • Response:
json
[
  {
    "id": 1,
    "name": "perferendis",
    "description": "Product Description",
    "price": "100.00",
    "image": 10
  }
]


## Running the Project with Docker

### Prerequisites
- Docker
- Docker Compose

### Steps to Run the Project
1. Clone the repository:
   ```bash
   git clone https://github.com/Mirshirin/search-laravel-elastic.git
   cd yourproject

2.Build and start the containers:
docker-compose up –d --build

3.The script does not run migrations and seed the database; these tasks are handled in the docker-entrypoint.sh file.
docker exec -it laravel-app bash

4-:Access the application:
    • Laravel App: http://localhost:8090
    • API Endpoints: http://localhost:8090/api/products
    • Web Endpoints: http://localhost:8090/products
   
