<?php
namespace App\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface ProductSearchInterface
{
    public function search(string $query);
}
