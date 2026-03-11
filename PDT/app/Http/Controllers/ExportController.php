<?php

namespace App\Http\Controllers;

use App\Exports\AllTablesExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportAll()
    {
        return Excel::download(new AllTablesExport, 'all_data.xlsx');
    }
}
