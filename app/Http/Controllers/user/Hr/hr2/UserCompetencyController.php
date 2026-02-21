<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\user\Hr\hr2\Competency; 
use Illuminate\Support\Facades\Auth;

class UserCompetencyController extends Controller
{
    public function index()
    {
       
        if (!Auth::check()) {
            return redirect()->route('portal.login');
        }

      
        $competencies = Competency::orderBy('created_at', 'desc')->get();

       
        return view('hr.hr2.competencies', compact('competencies'));
    }
}