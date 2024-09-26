<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use App\Contracts\PermissionRepositoryInterface;
use App\Models\Product;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;




class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
    ];

    
    public function boot()
    {
        $this->registerPolicies();
      
        
    }

    
}
