<?php
namespace App\Http\Controllers;
 
use App\Models\Doctor;
use Illuminate\Http\Request;
 
class DoctorController extends Controller
{
    public function index()
    {
        return response()->json(
            Doctor::with('user', 'clinic')
                  ->where('is_available', true)
                  ->get()
        );
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'clinic_id'  => 'required|exists:clinics,id',
            'specialty'  => 'required|string',
            'bio'        => 'nullable|string',
        ]);
        return response()->json(Doctor::create($request->validated()), 201);
    }
 
    public function show(Doctor $doctor)
    {
        return response()->json($doctor->load('user', 'clinic'));
    }
 
    public function update(Request $request, Doctor $doctor)
    {
        $doctor->update($request->only([
            'specialty','bio','is_available','clinic_id'
        ]));
        return response()->json($doctor);
    }
 
    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return response()->json(null, 204);
    }
}
