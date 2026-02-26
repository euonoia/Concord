<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('core.core1.settings.index');
    }
}

