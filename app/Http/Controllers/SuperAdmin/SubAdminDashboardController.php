<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubAdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.subadminhome');
    }
}
