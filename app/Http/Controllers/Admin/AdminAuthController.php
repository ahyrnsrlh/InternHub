<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function login(): View
    {
        return view('pages.admin.auth.login');
    }
}
