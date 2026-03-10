<?php
namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\LearningModule;
use App\Models\admin\Hr\hr2\CourseEnroll;
use App\Models\admin\Hr\hr2\Competency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminLearningController extends Controller
{
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr2') {
            abort(403);
        }
    }

    public function index()
    {
        $this->authorizeHrAdmin();
        $modules = LearningModule::withCount('enrolls')->orderBy('id','desc')->get();
        $departments = DB::table('departments_hr2')->get();
        return view('admin.hr2.learning', compact('modules', 'departments'));
    }

    public function getSpecializations($dept)
    {
        
        $specs = DB::table('department_specializations_hr2')
            ->where('dept_code', $dept)
            ->where('is_active', 1)
            ->get(['specialization_name']); 
            
        return response()->json($specs);
    }

    public function getCompetencies($dept, $spec)
    {
        $competencies = Competency::where('department_id',$dept)
            ->where('specialization_name',$spec)
            ->where('is_active',1)
            ->get(['competency_code','name']);
        return response()->json($competencies);
    }

    public function generateModuleCode($dept, $spec)
    {
        $prefix = strtoupper($dept.'-'.substr($spec,0,3).'-MOD-');
        $last = LearningModule::where('module_code','like',$prefix.'%')->orderBy('module_code','desc')->first();
        $num = $last ? intval(substr($last->module_code,-3))+1 : 1;
        return response()->json(['code'=>$prefix.str_pad($num,3,'0',STR_PAD_LEFT)]);
    }

    public function store(Request $request)
    {
        $this->authorizeHrAdmin();
        $request->validate([
            'competency_code' => 'nullable|string|max:100',
            'module_code' => 'required|string|max:50|unique:learning_modules_hr2,module_code',
            'module_name' => 'required|string|max:255',
            'dept_code' => 'required|string|max:20',
            'specialization_name' => 'required|string|max:255',
            'module_type' => 'required|in:Compliance,Clinical,Simulation,Research,Other',
            'duration_hours' => 'nullable|integer|min:1',
            'is_mandatory' => 'nullable|boolean',
        ]);

        // Ensure competency is valid for selected dept + spec
        if($request->competency_code) {
            $exists = Competency::where('competency_code',$request->competency_code)
                ->where('department_id',$request->dept_code)
                ->where('specialization_name',$request->specialization_name)
                ->where('is_active',1)->exists();
            if(!$exists) {
                return redirect()->back()->with('error','Selected competency is not valid for this department/specialization.');
            }
        }

        $module = LearningModule::create([
            'competency_code'=>$request->competency_code,
            'module_code'=>$request->module_code,
            'module_name'=>$request->module_name,
            'dept_code'=>$request->dept_code,
            'specialization_name'=>$request->specialization_name,
            'description'=>$request->description,
            'module_type'=>$request->module_type,
            'duration_hours'=>$request->duration_hours ?? 1,
            'is_mandatory'=>$request->is_mandatory ?? 1,
        ]);

        $residents = DB::table('employees')
            ->where('department_id',$request->dept_code)
            ->where('specialization',$request->specialization_name)
            ->where('post_grad_status','residency')->get();

        foreach($residents as $res){
            CourseEnroll::firstOrCreate([
                'employee_id'=>$res->id,
                'module_id'=>$module->id
            ]);
        }

        return redirect()->back()->with('success','Learning module added and assigned successfully.');
    }

    public function destroy($id)
    {
        $this->authorizeHrAdmin();
        $module = LearningModule::findOrFail($id);
        $module->delete();
        return redirect()->back()->with('success','Learning module deleted successfully.');
    }
}