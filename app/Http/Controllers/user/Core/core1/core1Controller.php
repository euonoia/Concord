<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;

class core1Controller extends Controller
{
    public function index()
    {
        return view('core.core1.auth.login'); 
    }

    // Show policies page for Core1
    public function policies()
    {
        return view('core.core1.policies'); 
    }

    // Show reports page for Core1
    public function reports()
    {
        return view('core.core1.reports'); 
    }
}
