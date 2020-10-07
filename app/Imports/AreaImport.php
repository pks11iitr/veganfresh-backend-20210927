<?php

namespace App\Imports;

use App\Models\Area;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;

class AreaImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new Area([
            'name'     => $row[0],
        ]);
    }
}
