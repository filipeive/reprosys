<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;

class SalesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Sale::all();
    }
    public function exportExcel(Request $request)
    {
        return Excel::download(new SalesExport, 'relatorio.xlsx');
    }
    
}
