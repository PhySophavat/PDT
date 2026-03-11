<?php

namespace App\Http\Controllers;   
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;

class UserController extends Controller
{
    public function exportAllUsers()
    {
        return Excel::download(new UsersExport, 'all_users.xlsx');
    }




}
