<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\LearningModule;
use App\Models\admin\Hr\hr2\LearningMaterial;
use App\Models\admin\Hr\hr2\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminLearningMaterialsController extends Controller
{
    /**
     * Ensure only HR2 Admin can access
     */
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr2') {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Materials selector page
     * Shows Department -> Specialization -> Module dropdowns
     */
    public function selector()
    {
        $this->authorizeHrAdmin();

        $departments = Department::all();

        return view('admin.hr2.learning.materials-selector', compact('departments'));
    }

    /**
     * Get modules by department + specialization (AJAX)
     */
    public function getModulesByDeptSpec($deptCode, $spec)
    {
        $this->authorizeHrAdmin();

        $modules = LearningModule::where('dept_code', $deptCode)
                    ->where('specialization_name', $spec)
                    ->get(['id','module_name','module_code']);

        return response()->json($modules);
    }

    /**
     * List all materials for a module (AJAX)
     */
    public function listMaterials($moduleCode)
    {
        $this->authorizeHrAdmin();

        $materials = LearningMaterial::where('module_code', $moduleCode)
                        ->orderBy('created_at','desc')
                        ->get(['id','title','url','file_path','type']);

        return response()->json($materials);
    }

    /**
     * Store new learning material
     */
    public function store(Request $request, $moduleCode)
    {
        $this->authorizeHrAdmin();

        $module = LearningModule::where('module_code', $moduleCode)->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'url'  => 'nullable|url|max:500',
            'type' => 'required|string'
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('learning_materials', 'public');
        }

        if (!$filePath && !$request->url) {
            return back()->with('error', 'Please upload a file or provide a URL.');
        }

        LearningMaterial::create([
            'module_code' => $module->module_code,
            'title' => $request->title,
            'file_path' => $filePath,
            'url' => $request->url,
            'type' => $request->type,
        ]);

        return back()->with('success', 'Material added successfully.');
    }

    /**
     * Delete learning material
     */
    public function destroy($id)
    {
        $this->authorizeHrAdmin();

        $material = LearningMaterial::findOrFail($id);

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return back()->with('success', 'Material deleted successfully.');
    }
}