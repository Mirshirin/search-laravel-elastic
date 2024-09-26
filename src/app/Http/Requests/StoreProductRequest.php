<?php
namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    protected $user; // Define a property to hold the user instance

    /**
     * Instantiate a new request instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function authorize()
    {
        //
    }

    public function rules()
    {
        
        return [
            'name' => ['required','max:255','string'],    
            'description' => ['required','max:255','string'],   
            'price' => ['required', 'numeric', 'min:0'],  
            'image' => [ 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048']     
        ];
    }
}