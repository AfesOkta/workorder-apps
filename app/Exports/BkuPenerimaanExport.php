<?php

namespace App\Exports;

use App\Models\BkuPenerimaan;
use Maatwebsite\Excel\Concerns\FromCollection;

class BkuPenerimaanExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return BkuPenerimaan::all();
    }
}
