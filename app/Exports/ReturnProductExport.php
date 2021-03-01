<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ReturnProductExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct($returnproducts)
    {
        $this->returnproducts=$returnproducts;
    }

    public function view(): View
    {
        return view('admin.retrunproduct.invoice', [
            'returnproducts' => $this->returnproducts
        ]);
    }
}
