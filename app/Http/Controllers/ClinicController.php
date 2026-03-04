<?php
namespace App\Http\Controllers;
 
use App\Models\Clinic;
use Illuminate\Http\Request;
 
class ClinicController extends Controller
{
    public function index()
    {
        return response()->json(Clinic::where('is_active', true)->get());
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string',
            'address' => 'required|string',
            'city'    => 'required|string',
        ]);
        return response()->json(Clinic::create($request->validated()), 201);
    }
 
    public function show(Clinic $clinic)
    {
        return response()->json($clinic->load('doctors.user'));
    }
 
    public function update(Request $request, Clinic $clinic)
    {
        $clinic->update($request->validated());
        return response()->json($clinic);
    }
 
    public function destroy(Clinic $clinic)
    {
        $clinic->delete();
        return response()->json(null, 204);
    }
}
