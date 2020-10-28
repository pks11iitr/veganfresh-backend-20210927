<?php


namespace App\Exports;


use Illuminate\Contracts\View\View;

class ProductsExport
{
    public $products;

    public function __construct($data){

        $this->products=$data;
        //dd($this->data);
    }
    public function view(): View
    {

        //dd($this->data);

        return view('admin.reports.products-report', [
            'products' => $this->products
        ]);
    }
}
