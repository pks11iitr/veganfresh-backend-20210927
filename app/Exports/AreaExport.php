<?php

namespace App\Exports;

use App\Models\Area;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class AreaExport implements FromView
{
    public $data;

    public function __construct($data){

        $this->data=$data;
        //dd($this->data);
    }

    public function view(): View
    {

        //dd($this->data);

        return view('admin.reports.area-list', [
            'data' => $this->data
        ]);
    }
}
