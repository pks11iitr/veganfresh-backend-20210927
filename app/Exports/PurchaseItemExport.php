<?php

namespace App\Exports;

use App\Models\PurchaseItem;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseItemExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($purchases)
    {
        $this->purchases=$purchases;
    }

    public function view(): View
    {
        return view('admin.purchaseitem.invoice', [
            'purchases' => $this->purchases
        ]);
    }
}
