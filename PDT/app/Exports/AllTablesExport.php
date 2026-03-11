<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class AllTablesExport implements FromCollection
{
    public function collection()
    {
        $tables = DB::select('SHOW TABLES');
        $data = [];

        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            $rows = DB::table($tableName)->get();

            // Add table name
            $data[] = ["===== $tableName ====="];

            // Add each row
            foreach ($rows as $row) {
                $data[] = (array) $row;
            }

            // Blank row between tables
            $data[] = [''];
        }

        return collect($data);
    }
}
