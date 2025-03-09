<?php

namespace App\Exports;

use App\Models\BkuPenerimaan;
use App\Models\Skpd;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class BkuPenerimaanTipeBankExport implements FromView, WithTitle
{

    protected $month;
    protected $allSkpd;

    public function title(): string
    {
        return 'List Bku Penerimaan Tipe Bank';
    }

    public function __construct($month, $allSkpd) {
        $this->month = $month;
        $this->allSkpd = $allSkpd;
    }

    public function view(): View
    {
        $month = $this->month;
        $results = exportBkuTipeBank($this->month,$this->allSkpd);
        $totalBku = exportBkuTipeBank($this->month,$this->allSkpd);
        $skpd = Skpd::find($this->allSkpd);
        return view('exports.bkupenerimaan_bank',compact('results','totalBku','skpd','month'));
    }
}
