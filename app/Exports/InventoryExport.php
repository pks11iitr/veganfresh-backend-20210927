<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InventoryExport implements FromView
{
    public $data;

    public function __construct($data){

        $this->data=$data;
        //dd($this->data);
    }

    public function view(): View
    {

        //dd($this->data);

        return view('admin.reports.inventory-packet', [
            'data' => $this->data
        ]);
    }
}
